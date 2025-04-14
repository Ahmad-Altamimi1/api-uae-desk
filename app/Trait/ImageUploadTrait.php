<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait ImageUploadTrait
{
    public function uploadImage(Request $request, $inputName, $path)
    {
        if ($request->hasFile($inputName)) {
            // Get the file extension
            $image = $request->file($inputName);
            $ext = $image->getClientOriginalExtension();
            $imageName = 'media_' . uniqid() . '.' . $ext;

            $imagePath = $image->storeAs($path, $imageName, 'public'); // Store in storage/app/public/path

            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $domain = $_SERVER['HTTP_HOST'];

            return "$protocol://$domain/storage/$imagePath";
        }

        return null;
    }

    public function updateImage(Request $request, $inputName, $path, $oldPath = null)
    {
        if ($request->hasFile($inputName)) {
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            // Get the file extension
            $image = $request->file($inputName);
            $ext = $image->getClientOriginalExtension();
            $imageName = 'media_' . uniqid() . '.' . $ext;

            // Store the image
            $imagePath = $image->storeAs($path, $imageName, 'public');

            // Get protocol and domain
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $domain = $_SERVER['HTTP_HOST'];

            // Construct the full URL
            return "$protocol://$domain/storage/$imagePath";
        }

        return null;
    }

    public function uploadMulImage(Request $request, $inputName, $path)
    {
        $imagePaths = [];
        if ($request->hasFile($inputName)) {
            $images = $request->file($inputName);
            foreach ($images as $image) {
                // Get the file extension
                $ext = $image->getClientOriginalExtension();
                $imageName = 'media_' . uniqid() . '.' . $ext;

                // Store the image
                $imagePath = $image->storeAs($path, $imageName, 'public');

                // Get protocol and domain
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                $domain = $_SERVER['HTTP_HOST'];

                // Construct the full URL
                $imagePaths[] = "$protocol://$domain/storage/$imagePath";
            }
            return $imagePaths;
        }

        return $imagePaths;
    }

    public function deleteImage(string $path)
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
