<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Libraries\StorageService;
use App\Models\ModUpload;
use App\Models\ModText;
use CodeIgniter\API\ResponseTrait;

class TrashApi extends BaseController
{
    use ResponseTrait;

    protected $modUpload;
    protected $modText;

    public function __construct()
    {
        $this->modUpload = new ModUpload();
        $this->modText = new ModText();
    }

    public function restoreFile()
    {
        $fileUuid = $this->request->getPost('file_uuid');
        $sessId = session()->get('sess_id');

        if (!$fileUuid || !$sessId) {
            return $this->respond(['success' => false, 'message' => 'Invalid parameters'], 400);
        }

        try {
            // Get file from deleted table
            $file = $this->modUpload->get_deleted_file_by_uuid($fileUuid, $sessId);

            if (!$file) {
                return $this->respond(['success' => false, 'message' => 'File not found in trash'], 404);
            }

            // Remove deleted fields and restore to main table
            unset($file->up_file_deleted_id);
            unset($file->deleted_at);
            $this->modUpload->restore_file($file);

            // Restore physical file in storage
            $diskName = basename($file->up_file_Sys_Name ?? $file->up_file_Name ?? $file->up_file_Orig_Name ?? '');
            if (!empty($diskName)) {
                StorageService::getInstance()->move("trash/{$diskName}", "active/{$diskName}");
            }

            return $this->respond(['success' => true, 'message' => 'File restored successfully']);
        } catch (\Exception $e) {
            return $this->respond(['success' => false, 'message' => 'Failed to restore file: ' . $e->getMessage()], 500);
        }
    }

    public function restoreText()
    {
        $textUuid = $this->request->getPost('text_uuid');
        $sessId = session()->get('sess_id');

        if (!$textUuid || !$sessId) {
            return $this->respond(['success' => false, 'message' => 'Invalid parameters'], 400);
        }

        try {
            // Get text from deleted table
            $text = $this->modText->get_deleted_text_by_uuid($textUuid, $sessId);

            if (!$text) {
                return $this->respond(['success' => false, 'message' => 'Text not found in trash'], 404);
            }

            // Remove deleted fields and restore to main table
            unset($text->up_text_deleted_id);
            unset($text->deleted_at);
            $this->modText->restore_text($text);

            return $this->respond(['success' => true, 'message' => 'Text restored successfully']);
        } catch (\Exception $e) {
            return $this->respond(['success' => false, 'message' => 'Failed to restore text: ' . $e->getMessage()], 500);
        }
    }

    public function permanentDeleteFile()
    {
        $fileUuid = $this->request->getPost('file_uuid');
        $sessId = session()->get('sess_id');

        if (!$fileUuid || !$sessId) {
            return $this->respond(['success' => false, 'message' => 'Invalid parameters'], 400);
        }

        try {
            $file = $this->modUpload->get_deleted_file_by_uuid($fileUuid, $sessId);
            $diskName = $file ? basename($file->up_file_Sys_Name ?? $file->up_file_Name ?? $file->up_file_Orig_Name ?? '') : null;

            // Permanently delete from deleted table
            $deleted = $this->modUpload->permanent_delete_file($fileUuid, $sessId);

            if ($deleted) {
                if ($diskName) {
                    $storage = StorageService::getInstance();
                    $storage->delete("trash/{$diskName}");
                    $storage->delete("active/{$diskName}");
                }
                return $this->respond(['success' => true, 'message' => 'File permanently deleted']);
            } else {
                return $this->respond(['success' => false, 'message' => 'File not found or already deleted'], 404);
            }
        } catch (\Exception $e) {
            return $this->respond(['success' => false, 'message' => 'Failed to delete file: ' . $e->getMessage()], 500);
        }
    }

    public function permanentDeleteText()
    {
        $textUuid = $this->request->getPost('text_uuid');
        $sessId = session()->get('sess_id');

        if (!$textUuid || !$sessId) {
            return $this->respond(['success' => false, 'message' => 'Invalid parameters'], 400);
        }

        try {
            // Permanently delete from deleted table
            $deleted = $this->modText->permanent_delete_text($textUuid, $sessId);

            if ($deleted) {
                return $this->respond(['success' => true, 'message' => 'Text permanently deleted']);
            } else {
                return $this->respond(['success' => false, 'message' => 'Text not found or already deleted'], 404);
            }
        } catch (\Exception $e) {
            return $this->respond(['success' => false, 'message' => 'Failed to delete text: ' . $e->getMessage()], 500);
        }
    }

    public function emptyTrash()
    {
        $sessId = session()->get('sess_id');

        if (!$sessId) {
            return $this->respond(['success' => false, 'message' => 'Invalid session'], 400);
        }

        try {
            $deletedFiles = $this->modUpload->get_deleted_files($sessId);
            $storage = StorageService::getInstance();
            $keysToDelete = [];
            if (!empty($deletedFiles)) {
                foreach ($deletedFiles as $df) {
                    $diskName = basename($df->up_file_Sys_Name ?? $df->up_file_Name ?? $df->up_file_Orig_Name ?? '');
                    if (!empty($diskName)) {
                        $keysToDelete[] = "trash/{$diskName}";
                        $keysToDelete[] = "active/{$diskName}";
                    }
                }
            }

            $filesDeleted = $this->modUpload->empty_trash_files($sessId);
            $textsDeleted = $this->modText->empty_trash_texts($sessId);

            if (!empty($keysToDelete)) {
                $storage->deleteMultiple($keysToDelete);
            }

            $totalDeleted = $filesDeleted + $textsDeleted;

            return $this->respond([
                'success' => true,
                'message' => 'Trash emptied successfully',
                'deleted_count' => $totalDeleted
            ]);
        } catch (\Exception $e) {
            return $this->respond(['success' => false, 'message' => 'Failed to empty trash: ' . $e->getMessage()], 500);
        }
    }
}
