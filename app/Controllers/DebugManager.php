<?php

namespace App\Controllers;

use App\Models\ModUpload;
use CodeIgniter\API\ResponseTrait;

class DebugManager extends BaseController
{
    use ResponseTrait;

    public function search() {
        $mod_upload = new ModUpload();

        $search_term = $this->request->getVar('search') ?? '';
        $category = $this->request->getVar('category') ?? '';
        $tags = $this->request->getVar('tags') ? explode(',', $this->request->getVar('tags')) : [];
        $file_type = $this->request->getVar('file_type') ?? '';

        $sess_id = $this->session->get('sess_id');
        $results = $mod_upload->search_files($sess_id, $search_term, $category, $tags, $file_type);

        return $this->respond([
            'status' => 1,
            'files' => $results
        ]);
    }

    public function rename() {
        $mod_upload = new ModUpload();

        $file_uuid = $this->request->getVar('file_uuid');
        $new_name = $this->request->getVar('new_name');

        if (empty($file_uuid) || empty($new_name)) {
            return $this->respond([
                'status' => 0,
                'message' => 'File UUID and new name are required'
            ]);
        }

        $result = $mod_upload->rename_file($file_uuid, $new_name);

        return $this->respond([
            'status' => $result ? 1 : 0,
            'message' => $result ? 'File renamed successfully' : 'Failed to rename file'
        ]);
    }

    public function add_tag() {
        $mod_upload = new ModUpload();

        $file_uuid = $this->request->getVar('file_uuid');
        $tag_name = $this->request->getVar('tag_name');

        if (empty($file_uuid) || empty($tag_name)) {
            return $this->respond([
                'status' => 0,
                'message' => 'File UUID and tag name are required'
            ]);
        }

        $result = $mod_upload->add_file_tag($file_uuid, $tag_name);

        return $this->respond([
            'status' => $result ? 1 : 0,
            'message' => $result ? 'Tag added successfully' : 'Failed to add tag'
        ]);
    }

    public function remove_tag() {
        $mod_upload = new ModUpload();

        $file_uuid = $this->request->getVar('file_uuid');
        $tag_name = $this->request->getVar('tag_name');

        if (empty($file_uuid) || empty($tag_name)) {
            return $this->respond([
                'status' => 0,
                'message' => 'File UUID and tag name are required'
            ]);
        }

        $result = $mod_upload->remove_file_tag($file_uuid, $tag_name);

        return $this->respond([
            'status' => $result ? 1 : 0,
            'message' => $result ? 'Tag removed successfully' : 'Failed to remove tag'
        ]);
    }

    public function update_category() {
        $mod_upload = new ModUpload();

        $file_uuid = $this->request->getVar('file_uuid');
        $category = $this->request->getVar('category');

        if (empty($file_uuid)) {
            return $this->respond([
                'status' => 0,
                'message' => 'File UUID is required'
            ]);
        }

        $result = $mod_upload->update_file_metadata($file_uuid, ['up_file_category' => $category]);

        return $this->respond([
            'status' => $result ? 1 : 0,
            'message' => $result ? 'Category updated successfully' : 'Failed to update category'
        ]);
    }

    public function batch_delete() {
        $mod_upload = new ModUpload();

        $file_uuids = $this->request->getVar('file_uuids');

        if (empty($file_uuids) || !is_array($file_uuids)) {
            return $this->respond([
                'status' => 0,
                'message' => 'File UUIDs array is required'
            ]);
        }

        $result = $mod_upload->batch_delete_files($file_uuids);

        return $this->respond([
            'status' => $result ? 1 : 0,
            'message' => $result ? 'Files deleted successfully' : 'Failed to delete some files',
            'deleted_count' => $result ? count($file_uuids) : 0
        ]);
    }

    public function batch_add_tag() {
        $mod_upload = new ModUpload();

        $file_uuids = $this->request->getVar('file_uuids');
        $tag_name = $this->request->getVar('tag_name');

        if (empty($file_uuids) || !is_array($file_uuids) || empty($tag_name)) {
            return $this->respond([
                'status' => 0,
                'message' => 'File UUIDs array and tag name are required'
            ]);
        }

        $success_count = 0;
        foreach ($file_uuids as $uuid) {
            if ($mod_upload->add_file_tag($uuid, $tag_name)) {
                $success_count++;
            }
        }

        return $this->respond([
            'status' => 1,
            'message' => "Tag added to {$success_count} of " . count($file_uuids) . " files",
            'success_count' => $success_count
        ]);
    }

    public function get_file_details($file_uuid = null) {
        $mod_upload = new ModUpload();

        if (empty($file_uuid)) {
            $file_uuid = $this->request->getVar('file_uuid');
        }

        if (empty($file_uuid)) {
            return $this->respond([
                'status' => 0,
                'message' => 'File UUID is required'
            ]);
        }

        $file = $mod_upload->file_get_uploaded_by_file_uuid($file_uuid);

        if (empty($file)) {
            return $this->respond([
                'status' => 0,
                'message' => 'File not found'
            ]);
        }

        $file = $file[0];
        $categories = $mod_upload->get_file_categories();
        $tags = $mod_upload->get_file_tags();

        return $this->respond([
            'status' => 1,
            'file' => $file,
            'categories' => $categories,
            'available_tags' => $tags
        ]);
    }

    public function update_description() {
        $mod_upload = new ModUpload();

        $file_uuid = $this->request->getVar('file_uuid');
        $description = $this->request->getVar('description');

        if (empty($file_uuid)) {
            return $this->respond([
                'status' => 0,
                'message' => 'File UUID is required'
            ]);
        }

        $result = $mod_upload->update_file_description($file_uuid, $description);

        return $this->respond([
            'status' => $result ? 1 : 0,
            'message' => $result ? 'Description updated successfully' : 'Failed to update description'
        ]);
    }

    public function get_categories_and_tags() {
        $mod_upload = new ModUpload();

        return $this->respond([
            'status' => 1,
            'categories' => $mod_upload->get_file_categories(),
            'tags' => $mod_upload->get_file_tags()
        ]);
    }

    public function batch_download() {
        $mod_upload = new ModUpload();
        $file_uuids = $this->request->getPost('file_uuids');

        if (empty($file_uuids) || !is_array($file_uuids)) {
            return redirect()->back()->with('error', 'No files selected for download.');
        }

        $zipFile = WRITEPATH . 'uploads/P2P_Batch_Download_' . time() . '.zip';
        $zip = new \ZipArchive();

        if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            return redirect()->back()->with('error', 'Could not create ZIP archive.');
        }

        $filesAdded = 0;
        foreach ($file_uuids as $uuid) {
            $file_data = $mod_upload->file_get_uploaded_by_file_uuid($uuid);
            if (!empty($file_data) && isset($file_data[0])) {
                $file = $file_data[0];
                $physicalPath = WRITEPATH . 'uploads/copied_files/' . ($file->up_file_Orig_Name ?? '');
                if (file_exists($physicalPath)) {
                    $zip->addFile($physicalPath, $file->up_file_Orig_Name);
                    $filesAdded++;
                }
            }
        }

        $zip->close();

        if ($filesAdded === 0) {
            if (file_exists($zipFile)) {
                @unlink($zipFile);
            }
            return redirect()->back()->with('error', 'None of the selected files could be found on the server.');
        }

        $fileContent = file_get_contents($zipFile);
        @unlink($zipFile);
        return $this->response->download('P2P_Batch_Download.zip', $fileContent);
    }
}
