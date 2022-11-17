<?php

namespace App\Http\Controllers\Api\v1;

use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Api\v1\ProductResource;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'name_search' => 'string',
            'name_order' => [
                Rule::in(['asc', 'desc']),
            ],
            'items_per_page' => 'integer'

        ]);

        if($validator->fails()) {

            return response()->json(

                [
                    'status_code' => JsonResponse::HTTP_NOT_ACCEPTABLE,
                    'error' =>  $validator->errors()
                ], 

                JsonResponse::HTTP_NOT_ACCEPTABLE

            );

        }

        $validated_data = $validator->validated();

        $products = DB::table('products')
                        ->where('name', 'like', '%' . $validated_data['name_search'] . '%')
                        ->orderBy('name', $validated_data['name_order'])
                        ->paginate($validated_data['items_per_page']);

        return ProductResource::collection($products);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'name' => 'required|unique:products,name',
            'price' => 'required|between:0,99.99',
            'is_for_sale' => 'required|boolean',
            'available_stock' => 'required|integer'

        ]);

        if($validator->fails()) {

            return response()->json(

                [
                    'status_code' => JsonResponse::HTTP_NOT_ACCEPTABLE,
                    'error' =>  $validator->errors()
                ], 

                JsonResponse::HTTP_NOT_ACCEPTABLE

            );

        }

        $validated_data = $validator->validated();

        if($validated_data) {

            DB::table('products')->insert([
                'name' => $validated_data['name'],
                'price' => (float) $validated_data['price'],
                'is_for_sale' => (boolean) $validated_data['is_for_sale'],
                'available_stock' => (integer) $validated_data['available_stock'],
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ]);

            return response()->json(

                [
                    'status_code' => JsonResponse::HTTP_OK,
                    'message' =>  'Product has been successfully saved!'
                ], 
                
                JsonResponse::HTTP_OK

            );

        } else {

            return 'Something went wrong!';
            
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        if($product = DB::table('products')->find($id)) {

            return new ProductResource($product);

        } else {

            return response()->json(

                [
                    'status_code' => JsonResponse::HTTP_NOT_FOUND,
                    'error' =>  'Product not found.'
                ], 

                JsonResponse::HTTP_NOT_FOUND

            );

        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if($product = DB::table('products')->find($id)) {
            $validator = Validator::make($request->all(), [

                'name' => 'sometimes|string',
                'price' => 'sometimes|between:0,99.99',
                'is_for_sale' => 'sometimes|boolean',
                'available_stock' => 'sometimes|integer'
    
            ]);

            if($validator->fails()) {

                return response()->json(
    
                    [
                        'status_code' => JsonResponse::HTTP_NOT_ACCEPTABLE,
                        'error' =>  $validator->errors()
                    ], 
    
                    JsonResponse::HTTP_NOT_ACCEPTABLE
    
                );
    
            }
            
            if($validated_data = $validator->validated()) {

                DB::table('products')
                ->where('id', '=', $id)
                ->update([
                    'name' => $validated_data['name'] ?? $product->name,
                    'price' => (float) $validated_data['price'] ?? $product->price,
                    'is_for_sale' => (boolean) $validated_data['is_for_sale'] ?? $product->is_for_sale,
                    'available_stock' => (integer) $validated_data['available_stock'] ?? $product->available_stock,
                    'updated_at' => \Carbon\Carbon::now()
                ]);

                return response()->json(

                    [
                        'status_code' => JsonResponse::HTTP_OK,
                        'message' =>  'Product was successfully updated!.'
                    ], 

                    JsonResponse::HTTP_OK

                );

            } else {

                return response()->json(
    
                    [
                        'status_code' => JsonResponse::HTTP_NOT_ACCEPTABLE,
                        'message' =>  'Something went wrong'
                    ], 
    
                    JsonResponse::HTTP_NOT_ACCEPTABLE
    
                );

            }
                
        } else {

            return response()->json(

                [
                    'status_code' => JsonResponse::HTTP_NOT_FOUND,
                    'error' =>  'Product not found.'
                ], 

                JsonResponse::HTTP_NOT_FOUND

            );

        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function create() {
        return view('create');
    }
}
