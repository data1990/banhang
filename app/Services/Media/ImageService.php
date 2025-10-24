<?php

declare(strict_types=1);

namespace App\Services\Media;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageService
{
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

        // Store original image
        $image->storeAs("products/{$product->id}", $filename, 'public');

        // Create thumbnail
        $this->createThumbnail($path);

        // Check if this is the first image (set as primary)
        $isPrimary = $product->images()->count() === 0;

        return $product->images()->create([
            'path' => $path,
            'is_primary' => $isPrimary,
            'sort_order' => $sortOrder,
        ]);
    }

    public function createThumbnail(string $path): void
    {
        $fullPath = Storage::disk('public')->path($path);
        $thumbnailPath = str_replace('.', '_thumb.', $fullPath);

        Image::make($fullPath)
            ->resize(600, 600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->save($thumbnailPath);
    }

    public function deleteImage(ProductImage $image): void
    {
        // Delete files from storage
        Storage::disk('public')->delete($image->path);
        
        $thumbnailPath = str_replace('.', '_thumb.', $image->path);
        Storage::disk('public')->delete($thumbnailPath);

        // Delete database record
        $image->delete();
    }

    public function getImageUrl(string $path): string
    {
        return Storage::disk('public')->url($path);
    }

    public function getThumbnailUrl(string $path): string
    {
        $thumbnailPath = str_replace('.', '_thumb.', $path);
        return Storage::disk('public')->url($thumbnailPath);
    }
}
