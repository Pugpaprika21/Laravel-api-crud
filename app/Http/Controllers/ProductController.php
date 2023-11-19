<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt');
    }

    public function show(): JsonResponse
    {
        $products = Product::select('id', 'name', 'price', 'quantity', 'created_at')->orderBy('created_at', 'desc')->get();
        return response()->json(['data' => $products, 'rows' => $products->count()]);
    }

    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'price' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'quantity' => ['required', 'integer'],
            'user_id' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $product = new Product();

        if ($product::where('name', $request->name)->first()) {
            return response()->json(['message' => 'The product name is already taken.'], 422);
        }

        $product->name = $request->name;
        $product->price = $request->price;
        $product->quantity = $request->quantity;
        $product->user_id = $request->user_id;
        $product->save();

        //$userJWT = auth()->user();

        return response()->json(['status' => true, 'message' => 'create product success.']);
    }

    public function update(Request $request, int $productId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'price' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'quantity' => ['required', 'integer'],
            'user_id' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $product = new Product();

        if ($product::where('name', $request->name)->first()) {
            return response()->json(['message' => 'The product name is already taken.'], 422);
        }

        if ($product::find($productId)) {

            $product::where('id', '=', $productId)->update([
                'name' => $request->name,
                'price' => $request->price,
                'quantity' => $request->quantity,
            ]);

            return response()->json(['message' => 'update product success.'], 200);
        }

        return response()->json(['message' => 'no product data.'], 422);
    }

    public function delete(int $productId): JsonResponse
    {
        $product = new Product();

        if (!$product::find($productId)) {
            return response()->json(['message' => 'product not found.'], 201);
        }

        $product::where('id', '=', $productId)->delete();
        return response()->json(['message' => 'product is deleted.'], 200);
    }
}
