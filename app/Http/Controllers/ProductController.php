<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Categories;
use JWTAuth;

class ProductController extends Controller
{
    protected $user;
 
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function listProducts(){
        return Product::all();
    }

    public function index()
    {
        // return $this->user->products()->get(['name', 'price', 'quantity', 'description'])->toArray();
        return $this->user->products()->get()->toArray();
    }

    public function show($id)
    {
        // $product = $this->user->products()->find($id);
        $product = Product::find($id);
    
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'lo sentimos, el producto con id ' . $id . ' no se encuentra'
            ], 400);
        }
    
        return $product;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'price' => 'required|integer',
            'quantity' => 'required|integer',
            'idCategory' => 'required|integer'
        ]);
    

        if($request->hasFile('photo')){
            $file = $request->file('photo');
            $name = time().$file->getClientOriginalName();
            $file->move(public_path().'/images/',$name);
        }

        // $category = Categories::find($request->idCategory);
        
        $product = new Product();
        $product->name = $request->name;
        $product->price = $request->price;
        $product->quantity = $request->quantity;
        $product->description = $request->description;
        $product->photo = $name;
        $product->idCategory = $request->idCategory;;
        // $product->idCategory


    
        if ($this->user->products()->save($product))
            return response()->json([
                'success' => true,
                'product' => $product
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'lo sentimos, el producto no puede ser agregado'
            ], 500);
    }

    public function update(Request $request, $id)
    {
        $product = $this->user->products()->find($id);        
    
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'lo sentimos, el producto con id ' . $id . ' no se encuentra'
            ], 400);
        }
        $updated = $product->fill($request->all())->save();
    
        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => 'Producto Actualizado Correctamente'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'lo sentimos, el producto no puede ser actualizado'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $product = $this->user->products()->find($id);
    
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'lo sentimos, el producto con id ' . $id . ' no se encuentra'
            ], 400);
        }
    
        if ($product->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado exitosamente'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'El producto no puede ser Eliminado'
            ], 500);
        }
    }

    public function search($search){
        $products = Product::where('name', 'like', '%'.$search.'%')
            ->orWhere('description', 'like', '%'.$search.'%')
            ->orderBy('id', 'desc')->get();
        //->orderBy('id', 'desc')->paginate(6);

        /*$users = User::with(array('posts' => function($query)
        {
            $query->where('title', 'like', '%first%');

        }))->get();*/
        return $products;
    }

}
