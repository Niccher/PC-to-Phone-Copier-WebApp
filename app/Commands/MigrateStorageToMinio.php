<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\StorageService;

class MigrateStorageToMinio extends BaseCommand
{
    protected $group       = 'Storage';
    protected $name        = 'storage:migrate-minio';
    protected $description = 'Migrate existing local uploads to MinIO/S3 object storage.';
    protected $usage       = 'storage:migrate-minio [options]';
    protected $options     = [
        '--dry-run' => 'Simulate migration without uploading files.',
        '--delete'  => 'Delete local files after successful upload to MinIO.',
    ];

    public function run(array $params)
    {
        $dryRun = array_key_exists('dry-run', $params) || CLI::getOption('dry-run');
        $delete = array_key_exists('delete', $params) || CLI::getOption('delete');

        CLI::write("=== P2P Copier Storage Migration to MinIO ===", 'yellow');
        if ($dryRun) {
            CLI::write("[DRY RUN MODE] No changes will be committed to MinIO.", 'cyan');
        }

        $storage = StorageService::getInstance();
        if (!$dryRun) {
            CLI::write("Connecting to MinIO and checking bucket...", 'white');
            if (!$storage->ensureBucketExists()) {
                CLI::error("Failed to connect to MinIO or ensure bucket exists. Check your .env configuration.");
                return;
            }
            CLI::write("Bucket '{$storage->getBucket()}' is ready.", 'green');
        }

        $dirs = [
            'active' => WRITEPATH . 'uploads/copied_files',
            'trash'  => WRITEPATH . 'uploads/copied_files_deleted',
            'legacy' => FCPATH . 'assets/media/uploads',
        ];

        $totalFound = 0;
        $totalUploaded = 0;
        $totalErrors = 0;

        foreach ($dirs as $type => $dir) {
            if (!is_dir($dir)) {
                CLI::write("Skipping {$type} directory (does not exist): {$dir}", 'dark_gray');
                continue;
            }

            CLI::write("Scanning {$type} files in: {$dir}", 'white');
            $files = array_diff(scandir($dir), ['.', '..', '.gitkeep', 'index.html']);

            foreach ($files as $file) {
                $fullPath = $dir . DIRECTORY_SEPARATOR . $file;
                if (!is_file($fullPath)) {
                    continue;
                }

                $totalFound++;
                $prefix = ($type === 'trash') ? 'trash/' : 'active/';
                $key = $prefix . $file;

                CLI::write("-> Migrating [{$type}] {$file} to {$key}...", 'white');

                if ($dryRun) {
                    CLI::write("   [SIMULATED] Would upload {$fullPath} (" . filesize($fullPath) . " bytes)", 'cyan');
                    $totalUploaded++;
                    continue;
                }

                $mimeType = function_exists('mime_content_type') ? @mime_content_type($fullPath) : null;
                $success = $storage->upload($key, $fullPath, $mimeType);

                if ($success) {
                    CLI::write("   [SUCCESS] Uploaded {$file}", 'green');
                    $totalUploaded++;

                    if ($delete) {
                        @unlink($fullPath);
                        CLI::write("   [DELETED] Local file removed", 'light_red');
                    }
                } else {
                    CLI::error("   [FAILED] Could not upload {$file}");
                    $totalErrors++;
                }
            }
        }

        CLI::newLine();
        CLI::write("=== Migration Summary ===", 'yellow');
        CLI::write("Total files found:    {$totalFound}");
        CLI::write("Successfully moved:   {$totalUploaded}", 'green');
        CLI::write("Errors encountered:   {$totalErrors}", $totalErrors > 0 ? 'red' : 'green');
    }
}
