<?php

namespace App\Libraries;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Config\Storage as StorageConfig;

class StorageService
{
    protected StorageConfig $config;
    protected ?S3Client $s3Client = null;
    protected static ?self $instance = null;

    public function __construct(?StorageConfig $config = null)
    {
        $this->config = $config ?? config('Storage');
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get or lazy-load the AWS S3 client configured for MinIO
     */
    public function getClient(): ?S3Client
    {
        if ($this->s3Client !== null) {
            return $this->s3Client;
        }

        if (!class_exists('Aws\S3\S3Client')) {
            log_message('error', 'StorageService: Aws\S3\S3Client not found. Run composer require aws/aws-sdk-php.');
            return null;
        }

        try {
            $this->s3Client = new S3Client([
                'version'                 => 'latest',
                'region'                  => $this->config->region,
                'endpoint'                => $this->config->endpoint,
                'use_path_style_endpoint' => $this->config->usePathStyleEndpoint,
                'credentials'             => [
                    'key'    => $this->config->accessKey,
                    'secret' => $this->config->secretKey,
                ],
            ]);
        } catch (\Exception $e) {
            log_message('error', 'StorageService S3Client init error: ' . $e->getMessage());
            return null;
        }

        return $this->s3Client;
    }

    /**
     * Get target bucket name
     */
    public function getBucket(): string
    {
        return $this->config->bucket;
    }

    /**
     * Ensure the default bucket exists in MinIO
     */
    public function ensureBucketExists(): bool
    {
        $client = $this->getClient();
        if (!$client) {
            return false;
        }

        $bucket = $this->getBucket();
        try {
            if (!$client->doesBucketExist($bucket)) {
                $client->createBucket(['Bucket' => $bucket]);
            }
            return true;
        } catch (\Exception $e) {
            log_message('error', 'StorageService ensureBucketExists failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Upload a local file or string/stream to MinIO
     *
     * @param string $key S3 object key (e.g. 'active/my_file.pdf')
     * @param mixed  $source File path string, resource, or string content
     * @param string|null $contentType MIME type
     */
    public function upload(string $key, $source, ?string $contentType = null): bool
    {
        $client = $this->getClient();
        if (!$client) {
            // Local fallback if S3 is unavailable
            return $this->localUploadFallback($key, $source);
        }

        try {
            $this->ensureBucketExists();

            $params = [
                'Bucket' => $this->getBucket(),
                'Key'    => ltrim($key, '/'),
            ];

            if ($contentType) {
                $params['ContentType'] = $contentType;
            }

            if (is_resource($source)) {
                $params['Body'] = $source;
            } elseif (is_string($source) && file_exists($source)) {
                $params['SourceFile'] = $source;
                if (!$contentType && function_exists('mime_content_type')) {
                    $detected = @mime_content_type($source);
                    if ($detected) {
                        $params['ContentType'] = $detected;
                    }
                }
            } else {
                $params['Body'] = (string) $source;
            }

            $client->putObject($params);
            return true;
        } catch (\Exception $e) {
            log_message('error', "StorageService upload failed for key {$key}: " . $e->getMessage());
            // Fallback to local disk so uploads don't fail for end-users
            return $this->localUploadFallback($key, $source);
        }
    }

    /**
     * Download an object from MinIO as a stream or array with content info
     *
     * @param string $key
     * @return array|null ['stream' => resource, 'size' => int, 'type' => string]
     */
    public function downloadStream(string $key): ?array
    {
        $client = $this->getClient();
        $key = ltrim($key, '/');

        if ($client) {
            try {
                $result = $client->getObject([
                    'Bucket' => $this->getBucket(),
                    'Key'    => $key,
                ]);

                return [
                    'stream' => $result['Body']->detach(),
                    'size'   => (int) ($result['ContentLength'] ?? 0),
                    'type'   => (string) ($result['ContentType'] ?? 'application/octet-stream'),
                ];
            } catch (AwsException $e) {
                if ($e->getStatusCode() !== 404) {
                    log_message('error', "StorageService downloadStream AWS error for {$key}: " . $e->getMessage());
                }
            } catch (\Exception $e) {
                log_message('error', "StorageService downloadStream failed for {$key}: " . $e->getMessage());
            }
        }

        // Fallback to local files if S3 download didn't return
        return $this->localDownloadFallback($key);
    }

    /**
     * Check if an object exists in MinIO or local fallback
     */
    public function exists(string $key): bool
    {
        $client = $this->getClient();
        $key = ltrim($key, '/');

        if ($client) {
            try {
                return $client->doesObjectExist($this->getBucket(), $key);
            } catch (\Exception $e) {
                log_message('error', "StorageService exists check error for {$key}: " . $e->getMessage());
            }
        }

        return $this->localExistsFallback($key);
    }

    /**
     * Delete an object from MinIO
     */
    public function delete(string $key): bool
    {
        $client = $this->getClient();
        $key = ltrim($key, '/');

        if ($client) {
            try {
                $client->deleteObject([
                    'Bucket' => $this->getBucket(),
                    'Key'    => $key,
                ]);
            } catch (\Exception $e) {
                log_message('error', "StorageService delete error for {$key}: " . $e->getMessage());
            }
        }

        // Also clean up local file if exists
        $localPath = $this->resolveLocalPath($key);
        if (file_exists($localPath)) {
            @unlink($localPath);
        }

        return true;
    }

    /**
     * Batch delete objects
     *
     * @param array $keys Array of S3 keys
     */
    public function deleteMultiple(array $keys): bool
    {
        if (empty($keys)) {
            return true;
        }

        $client = $this->getClient();
        if ($client) {
            try {
                $objects = array_map(function ($k) {
                    return ['Key' => ltrim($k, '/')];
                }, $keys);

                $client->deleteObjects([
                    'Bucket' => $this->getBucket(),
                    'Delete' => ['Objects' => $objects],
                ]);
            } catch (\Exception $e) {
                log_message('error', "StorageService deleteMultiple error: " . $e->getMessage());
            }
        }

        foreach ($keys as $k) {
            $local = $this->resolveLocalPath($k);
            if (file_exists($local)) {
                @unlink($local);
            }
        }

        return true;
    }

    /**
     * Move / Rename an object in MinIO (copy + delete)
     */
    public function move(string $sourceKey, string $destKey): bool
    {
        $sourceKey = ltrim($sourceKey, '/');
        $destKey   = ltrim($destKey, '/');
        $client    = $this->getClient();

        if ($client) {
            try {
                $bucket = $this->getBucket();
                // S3 CopySource requires URL-encoded key segments
                $encodedSource = rawurlencode($bucket) . '/' . implode('/', array_map('rawurlencode', explode('/', $sourceKey)));

                $client->copyObject([
                    'Bucket'     => $bucket,
                    'Key'        => $destKey,
                    'CopySource' => $encodedSource,
                ]);

                $client->deleteObject([
                    'Bucket' => $bucket,
                    'Key'    => $sourceKey,
                ]);
                return true;
            } catch (\Exception $e) {
                log_message('error', "StorageService move error from {$sourceKey} to {$destKey}: " . $e->getMessage());
            }
        }

        // Fallback local file move
        $localSrc = $this->resolveLocalPath($sourceKey);
        $localDst = $this->resolveLocalPath($destKey);
        if (file_exists($localSrc)) {
            $dir = dirname($localDst);
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }
            return @rename($localSrc, $localDst);
        }

        return false;
    }

    /**
     * Get S3 client configured with public endpoint for generating presigned URLs
     */
    public function getPublicClient(): ?S3Client
    {
        if (empty($this->config->publicEndpoint) || $this->config->publicEndpoint === $this->config->endpoint) {
            return $this->getClient();
        }

        try {
            return new S3Client([
                'version'                 => 'latest',
                'region'                  => $this->config->region,
                'endpoint'                => $this->config->publicEndpoint,
                'use_path_style_endpoint' => $this->config->usePathStyleEndpoint,
                'credentials'             => [
                    'key'    => $this->config->accessKey,
                    'secret' => $this->config->secretKey,
                ],
            ]);
        } catch (\Exception $e) {
            log_message('error', 'StorageService public client init error: ' . $e->getMessage());
            return $this->getClient();
        }
    }

    /**
     * Generate presigned URL for direct upload or download
     *
     * @param string $key S3 object key
     * @param string $method 'GET' or 'PUT'
     * @param string $expires Expiry time string (e.g. '+20 minutes')
     */
    public function getPresignedUrl(string $key, string $method = 'GET', string $expires = '+20 minutes'): ?string
    {
        $client = $this->getPublicClient();
        if (!$client) {
            return null;
        }

        try {
            $cmdName = ($method === 'PUT') ? 'PutObject' : 'GetObject';
            $cmd = $client->getCommand($cmdName, [
                'Bucket' => $this->getBucket(),
                'Key'    => ltrim($key, '/'),
            ]);

            $request = $client->createPresignedRequest($cmd, $expires);
            return (string) $request->getUri();
        } catch (\Exception $e) {
            log_message('error', "StorageService getPresignedUrl error for {$key}: " . $e->getMessage());
            return null;
        }
    }

    // --------------------------------------------------------------------------
    // Local Fallback Helpers
    // --------------------------------------------------------------------------

    protected function resolveLocalPath(string $key): string
    {
        $basename = basename($key);
        if (strpos($key, 'trash/') === 0 || strpos($key, 'deleted/') !== false) {
            return WRITEPATH . 'uploads/copied_files_deleted/' . $basename;
        }
        return WRITEPATH . 'uploads/copied_files/' . $basename;
    }

    protected function localUploadFallback(string $key, $source): bool
    {
        $target = $this->resolveLocalPath($key);
        $dir = dirname($target);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        if (is_resource($source)) {
            $dest = fopen($target, 'wb');
            if (!$dest) return false;
            stream_copy_to_stream($source, $dest);
            fclose($dest);
            return true;
        }

        if (is_string($source) && file_exists($source)) {
            return @copy($source, $target);
        }

        return (bool) @file_put_contents($target, (string) $source);
    }

    protected function localDownloadFallback(string $key): ?array
    {
        $localPath = $this->resolveLocalPath($key);
        if (!file_exists($localPath)) {
            // Check legacy path in public
            $legacy = FCPATH . 'assets/media/uploads/' . basename($key);
            if (file_exists($legacy)) {
                $localPath = $legacy;
            } else {
                return null;
            }
        }

        $stream = fopen($localPath, 'rb');
        if (!$stream) {
            return null;
        }

        $size = filesize($localPath) ?: 0;
        $type = function_exists('mime_content_type') ? (@mime_content_type($localPath) ?: 'application/octet-stream') : 'application/octet-stream';

        return [
            'stream' => $stream,
            'size'   => $size,
            'type'   => $type,
        ];
    }

    protected function localExistsFallback(string $key): bool
    {
        $local = $this->resolveLocalPath($key);
        if (file_exists($local)) return true;
        $legacy = FCPATH . 'assets/media/uploads/' . basename($key);
        return file_exists($legacy);
    }
}
