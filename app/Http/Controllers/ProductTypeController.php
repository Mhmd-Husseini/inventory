<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\BaseController;
use App\Models\ProductType;
use App\Traits\PaginatedResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductTypeController extends BaseController
{
    use PaginatedResponse;

    /**
     * The default number of items per page.
     *
     * @var int
     */
    protected $perPage = 15;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProductType::where('user_id', Auth::id());
        
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $perPage = $request->get('limit', $this->perPage);
        
        $productTypes = $query->paginate($perPage);
        
        return $this->successResponse($this->paginatedResponse($productTypes));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'current_stocks' => 0,
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('product_types', 'public');
            $data['image_path'] = $path;
        }

        $productType = ProductType::create($data);

        return $this->successResponse($productType, 'Product type created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductType $productType)
    {
        if ($response = $this->authorizeProductType($productType)) {
            return $response;
        }

        return $this->successResponse($productType);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductType $productType)
    {
        if ($response = $this->authorizeProductType($productType)) {
            return $response;
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
        ];

        if ($request->hasFile('image')) {
            if ($productType->image_path) {
                Storage::disk('public')->delete($productType->image_path);
            }
            
            $path = $request->file('image')->store('product_types', 'public');
            $data['image_path'] = $path;
        }

        $productType->update($data);

        return $this->successResponse($productType, 'Product type updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductType $productType)
    {
        if ($response = $this->authorizeProductType($productType)) {
            return $response;
        }

        if ($productType->image_path) {
            Storage::disk('public')->delete($productType->image_path);
        }

        $productType->delete();

        return response()->json(null, 204);
    }
}
