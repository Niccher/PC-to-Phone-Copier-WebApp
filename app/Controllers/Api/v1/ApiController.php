<?php

namespace App\Controllers\Api\v1;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class ApiController extends BaseController
{
    use ResponseTrait;

    protected function respondSuccess($data = null, string $message = 'Success', int $statusCode = 200)
    {
        return $this->respond([
            'success' => true,
            'code'    => $statusCode,
            'message' => $message,
            'data'    => $data,
        ], $statusCode);
    }

    protected function respondError(string $message = 'Error', int $statusCode = 400, $errors = null)
    {
        return $this->respond([
            'success' => false,
            'code'    => $statusCode,
            'message' => $message,
            'errors'  => $errors,
        ], $statusCode);
    }
}
