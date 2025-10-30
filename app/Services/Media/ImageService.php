<?php

declare(strict_types=1);

namespace App\Services\Media;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    private ImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }
    public function uploadProductImages(Product $product, array $images): void
    {
        foreach ($images as $index => $image) {
            $this->uploadProductImage($product, $image, $index);
        }
    }

    public function uploadProductImage(Product $product, UploadedFile $image, int $sortOrder = 0): ProductImage
    {
        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $path = "products/{$product->id}/{$filename}";

        // Compress and store image
        $this->compressAndStoreImage($image, $path);

        // Create thumbnail
        $this->createThumbnail($path);

        // Create multiple sizes
        $this->createImageSizes($path);

        // Check if this is the first image (set as primary)
        $isPrimary = $product->images()->count() === 0;

        return $product->images()->create([
            'path' => $path,
            'is_primary' => $isPrimary,
            'sort_order' => $sortOrder,
        ]);
    }

    public function compressAndStoreImage(UploadedFile $image, string $path): void
    {
        $fullPath = public_path('storage/' . ltrim($path, '/'));
        
        // Ensure directory exists
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Get image info
        $imageInfo = getimagesize($image->getPathname());
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        
        // Calculate new dimensions (max 1920px width, maintain aspect ratio)
        $maxWidth = 1920;
        $maxHeight = 1080;
        
        if ($width > $maxWidth || $height > $maxHeight) {
            $ratio = min($maxWidth / $width, $maxHeight / $height);
            $newWidth = (int) ($width * $ratio);
            $newHeight = (int) ($height * $ratio);
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        // Compress and resize image
        $this->imageManager->read($image->getPathname())
            ->resize($newWidth, $newHeight)
            ->toJpeg(85) // 85% quality for good compression
            ->save($fullPath);
    }

    public function createThumbnail(string $path): void
    {
        $fullPath = public_path('storage/' . ltrim($path, '/'));
        $pathInfo = pathinfo($fullPath);
        $thumbnailPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];

        $this->imageManager->read($fullPath)
            ->resize(600, 600)
            ->toJpeg(80) // 80% quality for thumbnails
            ->save($thumbnailPath);
    }

    public function deleteImage(ProductImage $image): void
    {
        // Delete all image sizes from storage
        $this->deleteAllImageSizes($image->path);

        // Delete database record
        $image->delete();
    }

    /**
     * Delete all image sizes (original, thumb, small, medium, large)
     */
    public function deleteAllImageSizes(string $path): void
    {
        $pathInfo = pathinfo($path);
        $basePath = $pathInfo['dirname'] . '/' . $pathInfo['filename'];
        $extension = $pathInfo['extension'];

        // Delete all possible sizes
        $sizes = ['', '_thumb', '_small', '_medium', '_large'];
        
        foreach ($sizes as $size) {
            $filePath = $basePath . $size . '.' . $extension;
            $abs = public_path('storage/' . ltrim($filePath, '/'));
            if (file_exists($abs)) {
                @unlink($abs);
            }
        }
    }

    public function getImageUrl(string $path): string
    {
        return asset('storage/' . ltrim($path, '/'));
    }

    public function getThumbnailUrl(string $path): string
    {
        $pathInfo = pathinfo($path);
        $thumbnailPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
        return asset('storage/' . ltrim($thumbnailPath, '/'));
    }

    /**
     * Create multiple image sizes for different use cases
     */
    public function createImageSizes(string $path): void
    {
        $fullPath = public_path('storage/' . ltrim($path, '/'));
        $basePath = pathinfo($fullPath, PATHINFO_DIRNAME);
        $filename = pathinfo($fullPath, PATHINFO_FILENAME);
        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);

        // Small size (300x300) for product lists
        $smallPath = $basePath . '/' . $filename . '_small.' . $extension;
        $this->imageManager->read($fullPath)
            ->resize(300, 300)
            ->toJpeg(75)
            ->save($smallPath);

        // Medium size (600x600) for product details
        $mediumPath = $basePath . '/' . $filename . '_medium.' . $extension;
        $this->imageManager->read($fullPath)
            ->resize(600, 600)
            ->toJpeg(80)
            ->save($mediumPath);

        // Large size (1200x1200) for zoom
        $largePath = $basePath . '/' . $filename . '_large.' . $extension;
        $this->imageManager->read($fullPath)
            ->resize(1200, 1200)
            ->toJpeg(85)
            ->save($largePath);
    }

    /**
     * Get image URL for specific size
     */
    public function getImageUrlBySize(string $path, string $size = 'original'): string
    {
        if ($size === 'original') {
            return $this->getImageUrl($path);
        }

        $pathInfo = pathinfo($path);
        $sizedPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_' . $size . '.' . $pathInfo['extension'];
        
        return asset('storage/' . ltrim($sizedPath, '/'));
    }
}
