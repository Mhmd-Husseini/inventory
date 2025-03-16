<?php

namespace App\Services;

use Aws\S3\S3Client;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class S3Service
{
    protected $s3Client;
    protected $bucket;
    protected $region;
    protected $baseUrl;

    public function __construct()
    {
        $this->bucket = config('filesystems.disks.s3.bucket');
        $this->region = config('filesystems.disks.s3.region');
        $this->baseUrl = config('filesystems.disks.s3.url');

        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => $this->region,
            'credentials' => [
                'key' => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
        ]);
    }

    /**
     * Upload a file to S3 without using ACLs
     *
     * @param UploadedFile $file The file to upload
     * @param string $path The path to store the file at
     * @return string|false The file path if successful, false otherwise
     */
    public function upload(UploadedFile $file, string $path)
    {
        try {
            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucket,
                'Key' => $path,
                'Body' => fopen($file->getRealPath(), 'r'),
                'ContentType' => $file->getMimeType(),
            ]);

            return $path;
        } catch (Exception $e) {
            Log::error("S3 upload error: " . $e->getMessage(), [
                'path' => $path,
                'file' => $file->getClientOriginalName()
            ]);
            return false;
        }
    }

    /**
     * Get the URL for an object in S3
     *
     * @param string $path
     * @return string
     */
    public function getUrl(string $path): string
    {
        return $this->baseUrl . '/' . $path;
    }

    /**
     * Delete an object from S3
     *
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        try {
            $this->s3Client->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $path,
            ]);
            
            return true;
        } catch (Exception $e) {
            Log::error("S3 delete error: " . $e->getMessage(), ['path' => $path]);
            return false;
        }
    }
} 