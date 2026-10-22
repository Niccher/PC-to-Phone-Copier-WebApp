#!/bin/bash
set -e

# Wait for MySQL to be fully ready (beyond just ping)
echo "Waiting for MySQL to accept connections..."
max_retries=30
count=0
while ! php -r "new PDO('mysql:host=mysql;port=3306', 'root', 'root_password');" 2>/dev/null; do
    count=$((count + 1))
    if [ $count -ge $max_retries ]; then
        echo "ERROR: MySQL did not become ready in time. Starting Apache without migrations."
        exec apache2-foreground
    fi
    echo "MySQL not ready yet... retrying ($count/$max_retries)"
    sleep 2
done

echo "MySQL is ready!"

# Ensure Composer dependencies (including AWS S3 SDK) are installed
if [ ! -d "/var/www/html/vendor/aws" ]; then
    echo "Installing/updating Composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction || echo "WARNING: Composer install encountered an issue."
fi

# Run migrations (safe to run multiple times — only applies pending migrations)
echo "Running database migrations..."
cd /var/www/html
php spark migrate --all 2>&1 || echo "WARNING: Migration encountered an issue. Check logs."

# Ensure S3 / MinIO storage bucket exists and has download policy
echo "Ensuring S3 bucket and download policy are initialized..."
php -r '
require "/var/www/html/vendor/autoload.php";
try {
    $s3 = new Aws\S3\S3Client([
        "version"                 => "latest",
        "region"                  => "us-east-1",
        "endpoint"                => getenv("s3.endpoint") ?: "http://minio:9000",
        "use_path_style_endpoint" => true,
        "credentials"             => [
            "key"    => getenv("s3.access_key") ?: "minio_admin",
            "secret" => getenv("s3.secret_key") ?: "minio_secure_password",
        ],
    ]);
    $bucket = getenv("s3.bucket") ?: "p2p-copier-files";
    if (!$s3->doesBucketExist($bucket)) {
        $s3->createBucket(["Bucket" => $bucket]);
        echo "Created bucket: $bucket\n";
    }
    $policy = json_encode([
        "Version"   => "2012-10-17",
        "Statement" => [[
            "Effect"    => "Allow",
            "Principal" => "*",
            "Action"    => ["s3:GetObject"],
            "Resource"  => ["arn:aws:s3:::$bucket/*"]
        ]]
    ]);
    $s3->putBucketPolicy(["Bucket" => $bucket, "Policy" => $policy]);
    echo "S3 bucket $bucket verified and ready.\n";
} catch (\Throwable $e) {
    echo "Notice: S3 init check: " . $e->getMessage() . "\n";
}
' 2>&1 || true

echo "Initialization complete. Starting Apache..."
exec apache2-foreground
