<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    // List all gallery images
    public function index()
    {
        $images = GalleryImage::all();
        return response()->json($images);
    }

    // Upload multiple images
    public function store(Request $request)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|max:2048', // max 2MB per image
        ]);

        $savedImages = [];

        foreach ($request->file('images') as $image) {
            $path = $image->store('gallery', 'public'); // store in storage/app/public/gallery
            $savedImages[] = GalleryImage::create(['image_path' => $path]);
        }

        return response()->json($savedImages, 201);
    }

    // Delete image by id
    public function destroy($id)
    {
        $image = GalleryImage::findOrFail($id);

        // Delete file from storage
        Storage::disk('public')->delete($image->image_path);

        // Delete DB record
        $image->delete();

        return response()->json(['message' => 'Image deleted successfully']);
    }
}
