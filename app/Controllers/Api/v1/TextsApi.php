<?php

namespace App\Controllers\Api\v1;

use App\Models\ModText;
use App\Models\ModVisitors;

class TextsApi extends ApiController
{
    public function list()
    {
        $mod_visitors = new ModVisitors();
        $mod_text = new ModText();

        $auth_code_id = $this->request->getVar('var_auth_code_id') ?? $this->request->getGet('auth_code_id') ?? $this->request->getVar('varAuthCodeId');
        $session_id   = $this->request->getGet('session_id') ?? $this->request->getVar('var_text_sess_id');

        if (empty($session_id) && !empty($auth_code_id)) {
            $sess = $mod_visitors->auth_codes_get_phone_by_auth_code_id($auth_code_id);
            if (!empty($sess)) {
                $session_id = $sess[0]->auth_codes_uuid;
            }
        }

        if (empty($session_id)) {
            $session_id = 'general';
        }

        $texts = $mod_text->text_get_uploaded_texts($session_id);

        return $this->respondSuccess([
            'texts' => $texts,
            'count' => count($texts)
        ], 'Texts retrieved successfully');
    }

    public function create()
    {
        $mod_visitors = new ModVisitors();
        $mod_text = new ModText();
        $json = $this->request->getJSON(true) ?: $this->request->getPost();

        $session_id   = $json['session_id'] ?? $json['var_text_sess_id'] ?? $this->request->getVar('var_text_sess_id') ?? $this->request->getVar('var_auth_code_id') ?? $this->request->getVar('session_id');
        $text_content = $json['text_content'] ?? $json['var_text_content'] ?? $this->request->getVar('var_text_content') ?? null;
        $title        = $json['title'] ?? $json['var_text_title'] ?? $this->request->getVar('var_text_title') ?? 'Mobile Text';
        $source       = $json['source'] ?? $json['var_text_source'] ?? $this->request->getVar('var_text_source') ?? 'Mobile App';

        if (!empty($session_id) && is_numeric($session_id)) {
            $sess = $mod_visitors->auth_codes_get_phone_by_auth_code_id($session_id);
            if (!empty($sess)) {
                $session_id = $sess[0]->auth_codes_uuid;
            }
        }

        if (empty($text_content)) {
            return $this->respondError('Text content cannot be empty', 400);
        }

        $data = [
            'text_uuid'       => random_string('alnum', 16),
            'text_session_id' => $session_id ?: 'general',
            'text_dev_id'     => $this->request->getHeaderLine('X-Device-UUID') ?: $this->request->getVar('var_dev_uuid'),
            'text_title'      => $title,
            'text_content'    => $text_content,
            'text_source'     => $source,
            'text_created_at' => date('Y-m-d H:i:s'),
        ];

        $mod_text->text_save($data);

        return $this->respondSuccess($data, 'Text saved successfully', 201);
    }

    public function delete($id = null)
    {
        $mod_text = new ModText();
        $json = $this->request->getJSON(true) ?: $this->request->getRawInput();
        $text_uuid = $id ?: ($json['var_text_uuid'] ?? $json['text_uuid'] ?? $this->request->getVar('text_uuid'));

        if (empty($text_uuid)) {
            return $this->respondError('Text UUID required for deletion', 400);
        }

        $deleted = $mod_text->text_to_delete($text_uuid);

        if ($deleted) {
            return $this->respondSuccess(['text_uuid' => $text_uuid], 'Text deleted successfully');
        }

        return $this->respondError('Text not found or already deleted', 404);
    }
}
