<?php
namespace App\Services;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ImageService
{
    public function storeImages(Request $request)
    {
        $uploadedImages = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $destinationPath = public_path('uploads');

                // Ensure the directory exists
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true, true);
                }

                // Move the file to the uploads directory
                $file->move($destinationPath, $filename);

                // Store filename in the database
                $uploadedImages[] = Image::create(['image' => $filename]);

                Log::info("Image stored: " . $filename);
            }
        } else {
            Log::error("No images found in request.");
        }

        return $uploadedImages;
    }

    public function deleteImage($id)
    {
        $image = Image::findOrFail($id);
        $filePath = public_path("uploads/{$image->image}");

        if (File::exists($filePath)) {
            unlink($filePath); // Delete the file
        } else {
            Log::error("File not found: " . $filePath);
            return false;
        }

        $image->delete(); // Delete record from DB
        return true;
    }

    public function updateImage(Request $request, $id)
    {

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);


        $image = Image::findOrFail($id);

        // Delete the old image file
        $oldFilePath = public_path("uploads/{$image->image}");
        if (File::exists($oldFilePath)) {
            unlink($oldFilePath);
        }

        // Store the new image
        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $destinationPath = public_path('uploads');


        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true, true);
        }

        // Move the file to the uploads directory
        $file->move($destinationPath, $filename);

        // Update the image record in the database
        $image->update(['image' => $filename]);

        return $filename;
    }


}
