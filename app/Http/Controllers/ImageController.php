<?php

namespace App\Http\Controllers;
use App\Models\Image;
use App\Services\ImageService;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index()
    {
        $images = Image::all();
        return view('images.index', compact('images'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $images = $this->imageService->storeImages($request);

        if (empty($images)) {
            return back()->with('error', 'No images were uploaded.');
        }

        return back()->with('success', 'Images uploaded successfully.');
    }

    public function destroy($id)
    {
        $deleted = $this->imageService->deleteImage($id);

        if ($deleted) {
            return response()->json(['success' => 'Image deleted successfully.']);
        } else {
            return response()->json(['error' => 'Failed to delete image.'], 500);
        }
    }

    public function update(Request $request, $id)
    {

        $newImage = $this->imageService->updateImage($request, $id);


        return response()->json([
            'success' => 'Image updated successfully!',
            'newImage' => $newImage
        ]);
    }
}
