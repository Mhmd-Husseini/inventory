<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\BaseController;
use App\Models\Item;
use App\Models\ProductType;
use App\Traits\PaginatedResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends BaseController
{
    use PaginatedResponse;
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'product_type_id' => 'required|exists:product_types,id',
        ]);
        
        $productType = ProductType::findOrFail($request->product_type_id);
        
        if ($response = $this->authorizeProductType($productType)) {
            return $response;
        }
        
        $query = Item::where('product_type_id', $request->product_type_id);
        
        if ($request->has('search')) {
            $query->where('serial_number', 'like', '%' . $request->search . '%');
        }
        
        $perPage = $request->get('limit', $this->perPage);
        
        $items = $query->paginate($perPage);
        
        return $this->successResponse($this->paginatedResponse($items));
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
            'product_type_id' => 'required|exists:product_types,id',
            'serial_number' => 'required|string|max:255|unique:items,serial_number',
        ]);
        
        $productType = ProductType::findOrFail($request->product_type_id);
        
        if ($response = $this->authorizeProductType($productType)) {
            return $response;
        }
        
        $item = Item::create([
            'product_type_id' => $request->product_type_id,
            'serial_number' => $request->serial_number,
            'is_sold' => false,
        ]);
        
        return $this->successResponse($item, 'Item created successfully', 201);
    }

    /**
     * Store multiple items at once.
     */
    public function storeBatch(Request $request)
    {
        $request->validate([
            'product_type_id' => 'required|exists:product_types,id',
            'serial_numbers' => 'required|array',
            'serial_numbers.*' => 'required|string|max:255|unique:items,serial_number',
        ]);
        
        $productType = ProductType::findOrFail($request->product_type_id);
        
        if ($response = $this->authorizeProductType($productType)) {
            return $response;
        }
        
        $items = [];
        
        foreach ($request->serial_numbers as $serialNumber) {
            $items[] = Item::create([
                'product_type_id' => $request->product_type_id,
                'serial_number' => $serialNumber,
                'is_sold' => false,
            ]);
        }
        
        return $this->successResponse(['items' => $items], 'Items created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        $productType = $item->productType;
        
        if ($response = $this->authorizeProductType($productType)) {
            return $response;
        }
        
        return $this->successResponse($item);
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
    public function update(Request $request, Item $item)
    {
        $productType = $item->productType;
        
        if ($response = $this->authorizeProductType($productType)) {
            return $response;
        }
        
        $request->validate([
            'serial_number' => 'required|string|max:255|unique:items,serial_number,' . $item->id,
            'is_sold' => 'boolean',
        ]);
        
        $item->update([
            'serial_number' => $request->serial_number,
            'is_sold' => $request->input('is_sold', $item->is_sold),
        ]);
        
        return $this->successResponse($item, 'Item updated successfully');
    }

    /**
     * Toggle the sold status of an item.
     */
    public function toggleSold(Request $request, Item $item)
    {
        $productType = $item->productType;
        
        if ($response = $this->authorizeProductType($productType)) {
            return $response;
        }
        
        $item->update([
            'is_sold' => !$item->is_sold,
        ]);
        
        $status = $item->is_sold ? 'sold' : 'available';
        return $this->successResponse($item, "Item marked as {$status} successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $productType = $item->productType;
        
        if ($response = $this->authorizeProductType($productType)) {
            return $response;
        }
        
        $item->delete();
        
        return response()->json(null, 204);
    }
}
