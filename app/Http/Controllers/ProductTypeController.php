<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\BaseController;
use App\Models\ProductType;
use App\Services\S3Service;
use App\Traits\PaginatedResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
     * The S3 service instance.
     *
     * @var S3Service
     */
    protected $s3Service;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(S3Service $s3Service)
    {
        $this->s3Service = $s3Service;
    }

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
            try {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = 'product_types/' . Str::uuid() . '.' . $extension;
                
                $path = $this->s3Service->upload($file, $filename);
                
                if (!$path) {
                    return $this->errorResponse('Failed to upload image to S3', 500);
                }
                
                $data['image_path'] = $path;
                
            } catch (\Exception $e) {
                Log::error('S3 upload error: ' . $e->getMessage());
                return $this->errorResponse('Error uploading to S3: ' . $e->getMessage(), 500);
            }
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
            try {
                // Delete old image if exists
                if ($productType->image_path) {
                    $this->s3Service->delete($productType->image_path);
                }
                
                // Generate a unique filename with original extension
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = 'product_types/' . Str::uuid() . '.' . $extension;
                
                // Upload to S3 using our custom service
                $path = $this->s3Service->upload($file, $filename);
                
                if (!$path) {
                    return $this->errorResponse('Failed to upload image to S3', 500);
                }
                
                // Store the file path
                $data['image_path'] = $path;
                
            } catch (\Exception $e) {
                Log::error('S3 upload error: ' . $e->getMessage());
                return $this->errorResponse('Error uploading to S3: ' . $e->getMessage(), 500);
            }
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

        // Delete image from S3 if exists
        if ($productType->image_path) {
            $this->s3Service->delete($productType->image_path);
        }

        $productType->delete();

        return response()->json(null, 204);
    }
}
