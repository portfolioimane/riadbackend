<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'sku' => 'required|string|unique:products',
            'category' => 'required|string',
            'supplier' => 'required|string',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'image' => 'nullable|file|image|max:2048', // max 2MB
        ]);

        // Handle image upload if present
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = Storage::url($path); // e.g., /storage/products/filename.jpg
        } else {
            $validated['image'] = null;
        }

        $validated['last_updated'] = now()->toDateString();

        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'category' => 'required|string',
            'supplier' => 'required|string',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'image' => 'nullable|file|image|max:2048', // max 2MB
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image) {
                $oldImagePath = str_replace('/storage/', '', $product->image);
                Storage::disk('public')->delete($oldImagePath);
            }

            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = Storage::url($path);
        } else {
            // If image is not sent, keep old one
            $validated['image'] = $product->image;
        }

        $validated['last_updated'] = now()->toDateString();

        $product->update($validated);

        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        // Delete image file if exists
        if ($product->image) {
            $oldImagePath = str_replace('/storage/', '', $product->image);
            Storage::disk('public')->delete($oldImagePath);
        }

        $product->delete();

        return response()->json(['message' => 'Deleted successfully.']);
    }
}
