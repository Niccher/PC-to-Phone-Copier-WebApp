<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Storage extends BaseConfig
{
    /**
     * Storage driver ('s3' or 'local')
     */
    public string $driver = 's3';

    /**
     * MinIO / S3 internal API endpoint
     */
    public string $endpoint = 'http://minio:9000';

    /**
     * MinIO / S3 public API endpoint for presigned URLs
     */
    public string $publicEndpoint = 'http://localhost:9100';

    /**
     * Access Key ID
     */
    public string $accessKey = 'minio_admin';

    /**
     * Secret Access Key
     */
    public string $secretKey = 'minio_secure_password';

    /**
     * Default S3 Bucket Name
     */
    public string $bucket = 'p2p-copier-files';

    /**
     * AWS Region (dummy for MinIO, usually us-east-1)
     */
    public string $region = 'us-east-1';

    /**
     * Use Path-Style Endpoint (must be true for MinIO)
     */
    public bool $usePathStyleEndpoint = true;

    public function __construct()
    {
        parent::__construct();

        $this->driver               = env('storage.driver', env('s3.driver', $this->driver));
        $this->endpoint             = env('s3.endpoint', $this->endpoint);
        $this->publicEndpoint       = env('s3.public_endpoint', $this->publicEndpoint);
        $this->accessKey            = env('s3.access_key', $this->accessKey);
        $this->secretKey            = env('s3.secret_key', $this->secretKey);
        $this->bucket               = env('s3.bucket', $this->bucket);
        $this->region               = env('s3.region', $this->region);
        $this->usePathStyleEndpoint = (bool) env('s3.use_path_style_endpoint', $this->usePathStyleEndpoint);
    }
}
