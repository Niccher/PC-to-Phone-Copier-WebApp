<?php

namespace App\Controllers\Api\v1;

use App\Models\ModText;

class TextsApi extends ApiController
{
    public function list()
    {
        $mod_text = new ModText();
        $session_id = $this->request->getGet('session_id') ?? $this->request->getVar('var_text_sess_id');

        if (empty($session_id)) {
            return $this->respondError('Session ID required', 400);
        }

        $texts = $mod_text->text_get_uploaded_texts($session_id);

        return $this->respondSuccess([
            'texts' => $texts,
            'count' => count($texts)
        ], 'Texts retrieved successfully');
    }

    public function create()
    {
        $mod_text = new ModText();
        $json = $this->request->getJSON(true) ?: $this->request->getPost();

        $session_id   = $json['session_id'] ?? $json['var_text_sess_id'] ?? null;
        $text_content = $json['text_content'] ?? $json['var_text_content'] ?? null;
        $title        = $json['title'] ?? $json['var_text_title'] ?? 'Mobile Text';

        if (empty($text_content)) {
            return $this->respondError('Text content cannot be empty', 400);
        }

        $data = [
            'text_uuid'       => random_string('alnum', 16),
            'text_session_id' => $session_id ?: 'general',
            'text_dev_id'     => $this->request->getHeaderLine('X-Device-UUID'),
            'text_title'      => $title,
            'text_content'    => $text_content,
            'text_source'     => 'Mobile App',
            'text_created_at' => date('Y-m-d H:i:s'),
        ];

        $mod_text->text_save($data);

        return $this->respondSuccess($data, 'Text saved successfully', 201);
    }
}
