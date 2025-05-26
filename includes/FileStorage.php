<?php
/**
 * FileStorage Class
 * 
 * Handles file storage operations with support for local storage and cloud storage
 * Implements adapter pattern to allow easy switching between storage providers
 */
class FileStorage {
    // Storage provider constants
    const STORAGE_LOCAL = 'local';
    const STORAGE_AWS_S3 = 's3';
    const STORAGE_GOOGLE_CLOUD = 'gcloud';
    const STORAGE_AZURE_BLOB = 'azure';
    
    // Default image sizes for optimization
    const SIZE_THUMBNAIL = 'thumbnail'; // 150x150
    const SIZE_MEDIUM = 'medium';       // 300x300
    const SIZE_LARGE = 'large';         // 800x600
    const SIZE_ORIGINAL = 'original';   // Original size
    
    private $provider;
    private $config;
    private $connection;
    
    /**
     * Constructor
     * 
     * @param string $provider Storage provider to use
     * @param array $config Configuration options for the provider
     */
    public function __construct($provider = self::STORAGE_LOCAL, $config = []) {
        $this->provider = $provider;
        $this->config = $this->mergeWithDefaultConfig($config);
        $this->initializeProvider();
    }
    
    /**
     * Merge provided config with default values
     * 
     * @param array $config User-provided configuration
     * @return array Complete configuration
     */
    private function mergeWithDefaultConfig($config) {
        $defaultConfig = [
            // Local storage defaults
            'local_base_path' => dirname(__DIR__) . '/public/uploads/',
            'local_base_url' => '/AgroSmart Market/public/uploads/',
            
            // S3 defaults
            's3_region' => 'us-east-1',
            's3_version' => 'latest',
            's3_bucket' => 'agrosmart-market',
            's3_base_url' => 'https://agrosmart-market.s3.amazonaws.com/',
            
            // Google Cloud Storage defaults
            'gcloud_bucket' => 'agrosmart-market',
            'gcloud_base_url' => 'https://storage.googleapis.com/agrosmart-market/',
            
            // Azure Blob Storage defaults
            'azure_container' => 'agrosmart-market',
            'azure_base_url' => 'https://agrosmartmarket.blob.core.windows.net/agrosmart-market/',
            
            // General defaults
            'optimize_images' => true,
            'image_sizes' => [
                self::SIZE_THUMBNAIL => ['width' => 150, 'height' => 150, 'crop' => true],
                self::SIZE_MEDIUM => ['width' => 300, 'height' => 300, 'crop' => false],
                self::SIZE_LARGE => ['width' => 800, 'height' => 600, 'crop' => false],
                self::SIZE_ORIGINAL => ['width' => null, 'height' => null, 'crop' => false]
            ],
            'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'max_file_size' => 5 * 1024 * 1024, // 5MB
            'use_unique_filenames' => true,
            'preserve_filenames' => false,
            'use_original_extension' => true,
            'create_year_month_folders' => true
        ];
        
        // Merge configs, with user-provided values taking precedence
        return array_merge($defaultConfig, $config);
    }
    
    /**
     * Initialize the storage provider
     */
    private function initializeProvider() {
        switch ($this->provider) {
            case self::STORAGE_AWS_S3:
                $this->initializeS3();
                break;
                
            case self::STORAGE_GOOGLE_CLOUD:
                $this->initializeGoogleCloud();
                break;
                
            case self::STORAGE_AZURE_BLOB:
                $this->initializeAzureBlob();
                break;
                
            case self::STORAGE_LOCAL:
            default:
                $this->initializeLocalStorage();
                break;
        }
    }
    
    /**
     * Initialize local file storage
     */
    private function initializeLocalStorage() {
        // Create the upload directory if it doesn't exist
        if (!file_exists($this->config['local_base_path'])) {
            mkdir($this->config['local_base_path'], 0777, true);
        }
        
        // Create subdirectories for different file types
        $dirs = ['products', 'profiles', 'categories', 'misc'];
        foreach ($dirs as $dir) {
            $path = $this->config['local_base_path'] . $dir;
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
        }
        
        $this->connection = true; // No actual connection needed for local storage
    }
    
    /**
     * Initialize Amazon S3 connection
     * Requires AWS SDK for PHP
     */
    private function initializeS3() {
        // This is a placeholder for actual S3 implementation
        // In a real implementation, you would use the AWS SDK
        /*
        require 'vendor/autoload.php';
        
        $this->connection = new Aws\S3\S3Client([
            'version' => $this->config['s3_version'],
            'region' => $this->config['s3_region'],
            'credentials' => [
                'key' => $this->config['s3_key'],
                'secret' => $this->config['s3_secret'],
            ],
        ]);
        */
        
        // For now, fall back to local storage
        $this->initializeLocalStorage();
    }
    
    /**
     * Initialize Google Cloud Storage connection
     */
    private function initializeGoogleCloud() {
        // Placeholder for Google Cloud Storage implementation
        // Fall back to local storage for now
        $this->initializeLocalStorage();
    }
    
    /**
     * Initialize Azure Blob Storage connection
     */
    private function initializeAzureBlob() {
        // Placeholder for Azure Blob Storage implementation
        // Fall back to local storage for now
        $this->initializeLocalStorage();
    }
    
    /**
     * Upload a file
     * 
     * @param array $file $_FILES array element
     * @param string $type File type (products, profiles, etc.)
     * @param string $customName Optional custom filename
     * @return array Result with success/error and file info
     */
    public function uploadFile($file, $type = 'misc', $customName = null) {
        // Validate file
        $validationResult = $this->validateFile($file);
        if (!$validationResult['valid']) {
            return ['error' => $validationResult['message']];
        }
        
        // Generate filename
        $filename = $this->generateFilename($file, $customName);
        
        // Determine the upload path
        $uploadPath = $this->getUploadPath($type, $filename);
        
        // Determine file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Check if we should create optimization versions
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        $variants = [];
        
        // For images, create different sizes if optimization is enabled
        if ($isImage && $this->config['optimize_images']) {
            $variants = $this->createImageVariants($file['tmp_name'], $type, $filename, $extension);
        }
        
        // Upload the original file
        $result = $this->uploadToProvider($file['tmp_name'], $uploadPath);
        
        if (!$result['success']) {
            return ['error' => 'Failed to upload file: ' . $result['message']];
        }
        
        // Construct the response
        $response = [
            'success' => true,
            'filename' => $filename,
            'path' => $uploadPath,
            'url' => $this->getFileUrl($type, $filename),
            'type' => $type,
            'size' => $file['size'],
            'mime_type' => $file['type']
        ];
        
        // Add variant URLs if any were created
        if (!empty($variants)) {
            $response['variants'] = $variants;
        }
        
        return $response;
    }
    
    /**
     * Validate a file
     * 
     * @param array $file $_FILES array element
     * @return array Validation result
     */
    private function validateFile($file) {
        // Check if file was uploaded without errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'message' => 'Upload error: ' . $this->getUploadErrorMessage($file['error'])];
        }
        
        // Check file size
        if ($file['size'] > $this->config['max_file_size']) {
            return ['valid' => false, 'message' => 'File too large (max ' . ($this->config['max_file_size'] / 1024 / 1024) . 'MB)'];
        }
        
        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->config['allowed_extensions'])) {
            return ['valid' => false, 'message' => 'Invalid file type. Allowed types: ' . implode(', ', $this->config['allowed_extensions'])];
        }
        
        // For images, verify that they are actual images
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $imageInfo = getimagesize($file['tmp_name']);
            if ($imageInfo === false) {
                return ['valid' => false, 'message' => 'Invalid image file'];
            }
        }
        
        return ['valid' => true];
    }
    
    /**
     * Get human-readable upload error message
     * 
     * @param int $errorCode PHP upload error code
     * @return string Error message
     */
    private function getUploadErrorMessage($errorCode) {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'File upload stopped by extension';
            default:
                return 'Unknown upload error';
        }
    }
    
    /**
     * Generate a unique filename
     * 
     * @param array $file $_FILES array element
     * @param string $customName Optional custom filename
     * @return string Generated filename
     */
    private function generateFilename($file, $customName = null) {
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if ($customName) {
            // Use custom name but sanitize it first
            $filename = $this->sanitizeFilename($customName);
        } else if ($this->config['preserve_filenames']) {
            // Use the original filename but sanitize it
            $filename = $this->sanitizeFilename(pathinfo($file['name'], PATHINFO_FILENAME));
        } else {
            // Generate a unique filename
            $filename = uniqid('file_');
        }
        
        // Add a unique identifier if configured
        if ($this->config['use_unique_filenames']) {
            $filename .= '_' . substr(md5(time() . rand(1000, 9999)), 0, 8);
        }
        
        // Add the extension if configured
        if ($this->config['use_original_extension']) {
            $filename .= '.' . $extension;
        }
        
        return $filename;
    }
    
    /**
     * Sanitize a filename
     * 
     * @param string $filename Original filename
     * @return string Sanitized filename
     */
    private function sanitizeFilename($filename) {
        // Remove any non-alphanumeric characters except for - and _
        $filename = preg_replace('/[^\w\-\.]/', '_', $filename);
        // Remove multiple consecutive underscores
        $filename = preg_replace('/_+/', '_', $filename);
        // Trim underscores from the beginning and end
        return trim($filename, '_');
    }
    
    /**
     * Get the full upload path for a file
     * 
     * @param string $type File type (products, profiles, etc.)
     * @param string $filename Filename
     * @return string Full upload path
     */
    private function getUploadPath($type, $filename) {
        $path = '';
        
        // Add year/month folders if configured
        if ($this->config['create_year_month_folders']) {
            $path .= date('Y') . '/' . date('m') . '/';
        }
        
        // Ensure the directory exists
        $fullPath = $this->config['local_base_path'] . $type . '/' . $path;
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0777, true);
        }
        
        return $type . '/' . $path . $filename;
    }
    
    /**
     * Create optimized image variants
     * 
     * @param string $tmpFile Path to temporary file
     * @param string $type File type
     * @param string $filename Base filename
     * @param string $extension File extension
     * @return array Array of variant URLs
     */
    private function createImageVariants($tmpFile, $type, $filename, $extension) {
        $variants = [];
        $baseFilename = pathinfo($filename, PATHINFO_FILENAME);
        
        // Create each configured size
        foreach ($this->config['image_sizes'] as $size => $dimensions) {
            // Skip original size, it's handled separately
            if ($size === self::SIZE_ORIGINAL) {
                continue;
            }
            
            // Generate variant filename
            $variantFilename = $baseFilename . '-' . $size . '.' . $extension;
            
            // Determine the variant path
            $variantPath = $this->getUploadPath($type, $variantFilename);
            
            // Create the resized image
            $resized = $this->resizeImage(
                $tmpFile,
                $this->config['local_base_path'] . $variantPath,
                $dimensions['width'],
                $dimensions['height'],
                $dimensions['crop']
            );
            
            if ($resized) {
                $variants[$size] = [
                    'filename' => $variantFilename,
                    'path' => $variantPath,
                    'url' => $this->getFileUrl($type, $variantFilename),
                    'width' => $dimensions['width'],
                    'height' => $dimensions['height']
                ];
            }
        }
        
        return $variants;
    }
    
    /**
     * Resize an image
     * 
     * @param string $sourcePath Source image path
     * @param string $destPath Destination path
     * @param int $width Target width
     * @param int $height Target height
     * @param bool $crop Whether to crop the image
     * @return bool Success
     */
    private function resizeImage($sourcePath, $destPath, $width, $height, $crop = false) {
        // Get image info
        list($sourceWidth, $sourceHeight, $sourceType) = getimagesize($sourcePath);
        
        // Create source image resource
        switch ($sourceType) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            default:
                return false;
        }
        
        // Calculate dimensions
        if ($crop) {
            // Crop to exact dimensions
            $ratio = max($width / $sourceWidth, $height / $sourceHeight);
            $newWidth = $width;
            $newHeight = $height;
            $srcX = ($sourceWidth - $width / $ratio) / 2;
            $srcY = ($sourceHeight - $height / $ratio) / 2;
            $srcWidth = $width / $ratio;
            $srcHeight = $height / $ratio;
        } else {
            // Maintain aspect ratio
            $ratio = min($width / $sourceWidth, $height / $sourceHeight);
            $newWidth = $sourceWidth * $ratio;
            $newHeight = $sourceHeight * $ratio;
            $srcX = 0;
            $srcY = 0;
            $srcWidth = $sourceWidth;
            $srcHeight = $sourceHeight;
        }
        
        // Create destination image
        $destImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG images
        if ($sourceType === IMAGETYPE_PNG) {
            imagealphablending($destImage, false);
            imagesavealpha($destImage, true);
            $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
            imagefilledrectangle($destImage, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        // Resize the image
        imagecopyresampled(
            $destImage, $sourceImage,
            0, 0, $srcX, $srcY,
            $newWidth, $newHeight, $srcWidth, $srcHeight
        );
        
        // Save the image
        $result = false;
        switch ($sourceType) {
            case IMAGETYPE_JPEG:
                $result = imagejpeg($destImage, $destPath, 90);
                break;
            case IMAGETYPE_PNG:
                $result = imagepng($destImage, $destPath, 9);
                break;
            case IMAGETYPE_GIF:
                $result = imagegif($destImage, $destPath);
                break;
        }
        
        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($destImage);
        
        return $result;
    }
    
    /**
     * Upload a file to the configured provider
     * 
     * @param string $sourcePath Source file path
     * @param string $destPath Destination path
     * @return array Result with success/error
     */
    private function uploadToProvider($sourcePath, $destPath) {
        switch ($this->provider) {
            case self::STORAGE_AWS_S3:
                return $this->uploadToS3($sourcePath, $destPath);
                
            case self::STORAGE_GOOGLE_CLOUD:
                return $this->uploadToGoogleCloud($sourcePath, $destPath);
                
            case self::STORAGE_AZURE_BLOB:
                return $this->uploadToAzureBlob($sourcePath, $destPath);
                
            case self::STORAGE_LOCAL:
            default:
                return $this->uploadToLocalStorage($sourcePath, $destPath);
        }
    }
    
    /**
     * Upload a file to local storage
     * 
     * @param string $sourcePath Source file path
     * @param string $destPath Destination path
     * @return array Result with success/error
     */
    private function uploadToLocalStorage($sourcePath, $destPath) {
        $fullDestPath = $this->config['local_base_path'] . $destPath;
        
        // Ensure the directory exists
        $dir = dirname($fullDestPath);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        
        if (move_uploaded_file($sourcePath, $fullDestPath)) {
            return ['success' => true];
        } else if (copy($sourcePath, $fullDestPath)) {
            // Fallback to copy if move_uploaded_file fails (e.g., for non-uploaded files)
            return ['success' => true];
        } else {
            return ['success' => false, 'message' => 'Failed to move file to destination'];
        }
    }
    
    /**
     * Upload a file to Amazon S3
     * 
     * @param string $sourcePath Source file path
     * @param string $destPath Destination path
     * @return array Result with success/error
     */
    private function uploadToS3($sourcePath, $destPath) {
        // This is a placeholder for actual S3 implementation
        // In a real implementation, you would use the AWS SDK
        /*
        try {
            $this->connection->putObject([
                'Bucket' => $this->config['s3_bucket'],
                'Key' => $destPath,
                'SourceFile' => $sourcePath,
                'ACL' => 'public-read',
            ]);
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
        */
        
        // For now, fall back to local storage
        return $this->uploadToLocalStorage($sourcePath, $destPath);
    }
    
    /**
     * Upload a file to Google Cloud Storage
     * 
     * @param string $sourcePath Source file path
     * @param string $destPath Destination path
     * @return array Result with success/error
     */
    private function uploadToGoogleCloud($sourcePath, $destPath) {
        // Placeholder for Google Cloud Storage implementation
        // Fall back to local storage for now
        return $this->uploadToLocalStorage($sourcePath, $destPath);
    }
    
    /**
     * Upload a file to Azure Blob Storage
     * 
     * @param string $sourcePath Source file path
     * @param string $destPath Destination path
     * @return array Result with success/error
     */
    private function uploadToAzureBlob($sourcePath, $destPath) {
        // Placeholder for Azure Blob Storage implementation
        // Fall back to local storage for now
        return $this->uploadToLocalStorage($sourcePath, $destPath);
    }
    
    /**
     * Get the URL for a file
     * 
     * @param string $type File type
     * @param string $filename Filename
     * @return string File URL
     */
    public function getFileUrl($type, $filename) {
        switch ($this->provider) {
            case self::STORAGE_AWS_S3:
                return $this->config['s3_base_url'] . $type . '/' . $filename;
                
            case self::STORAGE_GOOGLE_CLOUD:
                return $this->config['gcloud_base_url'] . $type . '/' . $filename;
                
            case self::STORAGE_AZURE_BLOB:
                return $this->config['azure_base_url'] . $type . '/' . $filename;
                
            case self::STORAGE_LOCAL:
            default:
                $path = '';
                if ($this->config['create_year_month_folders']) {
                    $matches = [];
                    if (preg_match('#^' . $type . '/(\d{4}/\d{2}/)#', $filename, $matches)) {
                        $path = $matches[1];
                    }
                }
                return $this->config['local_base_url'] . $type . '/' . $path . basename($filename);
        }
    }
    
    /**
     * Delete a file
     * 
     * @param string $type File type
     * @param string $filename Filename
     * @return array Result with success/error
     */
    public function deleteFile($type, $filename) {
        switch ($this->provider) {
            case self::STORAGE_AWS_S3:
                return $this->deleteFromS3($type, $filename);
                
            case self::STORAGE_GOOGLE_CLOUD:
                return $this->deleteFromGoogleCloud($type, $filename);
                
            case self::STORAGE_AZURE_BLOB:
                return $this->deleteFromAzureBlob($type, $filename);
                
            case self::STORAGE_LOCAL:
            default:
                return $this->deleteFromLocalStorage($type, $filename);
        }
    }
    
    /**
     * Delete a file from local storage
     * 
     * @param string $type File type
     * @param string $filename Filename
     * @return array Result with success/error
     */
    private function deleteFromLocalStorage($type, $filename) {
        $path = $this->config['local_base_path'] . $type . '/' . $filename;
        
        if (file_exists($path)) {
            if (unlink($path)) {
                return ['success' => true];
            } else {
                return ['success' => false, 'message' => 'Failed to delete file'];
            }
        } else {
            return ['success' => false, 'message' => 'File not found'];
        }
    }
    
    /**
     * Delete a file from Amazon S3
     * 
     * @param string $type File type
     * @param string $filename Filename
     * @return array Result with success/error
     */
    private function deleteFromS3($type, $filename) {
        // Placeholder for S3 implementation
        // Fall back to local storage for now
        return $this->deleteFromLocalStorage($type, $filename);
    }
    
    /**
     * Delete a file from Google Cloud Storage
     * 
     * @param string $type File type
     * @param string $filename Filename
     * @return array Result with success/error
     */
    private function deleteFromGoogleCloud($type, $filename) {
        // Placeholder for Google Cloud Storage implementation
        // Fall back to local storage for now
        return $this->deleteFromLocalStorage($type, $filename);
    }
    
    /**
     * Delete a file from Azure Blob Storage
     * 
     * @param string $type File type
     * @param string $filename Filename
     * @return array Result with success/error
     */
    private function deleteFromAzureBlob($type, $filename) {
        // Placeholder for Azure Blob Storage implementation
        // Fall back to local storage for now
        return $this->deleteFromLocalStorage($type, $filename);
    }
}
