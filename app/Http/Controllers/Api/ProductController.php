<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductListResource;
use App\Http\Resources\ProductResource;
use App\Models\Api\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $search = request('search', false);
        $perPage = request('per_page', 20);
        $sortField = request('sort_field', 'updated_at');
        $sortDirection = request('sort_direction', 'desc');

        $query = Product::query();
        $query->orderBy($sortField, $sortDirection);
        if ($search) {
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        return ProductListResource::collection(
            $query->paginate($perPage)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
            $data = $request->validated();
            $data['created_by'] = $request->user()->id;
            $data['updated_by'] = $request->user()->id;

            /** @var \Illuminate\Http\UploadedFile $image */
            $image = $data['image'] ?? null;
            // Check if image was given and save on local filesystem
            if ($image) {
            $relativePath = $this->saveImage($image);
            $data['image'] = URL::to(Storage::url($relativePath));
            $data['image_mime'] = $image->getClientMimeType();
            $data['image_size'] = $image->getSize();
            }

            $product = Product::create($data);

            return new ProductResource($product);


    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $data['updated_by'] = $request->user()->id;

        /**
         * @var \Illuminate\Http\UploadedFile $image
         */
        $image = $data['image'] ?? null;

        if ($image) {
            $relativePath = $this->saveImage($image);
            $data['image'] = URL::to(Storage::url($relativePath));
            $data['image_mime'] = $image->getClientMimeType();
            $data['image_size'] = $image->getSize();
            }

        // If there is an old image before updating, delete it
        if ($product->image) {
            Storage::deleteDirectory('images/' . dirname($product->image));
        }

        $product->update($data);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }

    private function saveImage(\Illuminate\Http\UploadedFile $image)
    {
        $path = Str::random();
        if (!Storage::disk('public')->putFileAs('images/' . $path, $image, $image->getClientOriginalName())) {
            throw new \Exception("Unable to save file \"{$image->getClientOriginalName()}\"");
        }
        return 'images/' . $path . '/' . $image->getClientOriginalName();

    }
}
