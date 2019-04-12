<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Categories;
use JWTAuth;


class CategoriesController extends Controller
{

    protected $user;
 
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index(){
        $categories = Categories::all();

        return response()->json(['success' => true, 'data' => $categories], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $category = new Categories(['name' => $request->get('name')]);
        $category->save();

        return response()->json(['success' => true, 'data' => 'Categoria Creada Exitosamente'], 200);
    }

    public function show($id){
        $category = Categories::find($id);
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'lo sentimos, la categoria con id ' . $id . ' no se encuentra'
            ], 400);
        }
            
        return $category;
    }

    public function update(Request $request, $id){
        $category = Categories::find($id);

        $request->validate([
            'name'=>'required',
        ]);

        if($category){
            $category = Categories::find($id);
            $category->name = $request->get('name');
            $category->save();
            return response()->json(['data' => 'categoria actualizada Exitosamente'], 200);
        }
        return response()->json(['data' => 'La categoria a actualizar no existe'], 401);
    }

    public function destroy($id){

        $category = Categories::find($id);
    
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'lo sentimos, la categoria con ' . $id . ' no se encuentra'
            ], 400);
        }
    
        if ($category->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'categoria eliminada Exitosamente'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'esta categoria no puede ser eliminada'
            ], 500);
        }
    }

    public function productsByCategories($id){
        $category = Categories::with('products')->find($id);       

        if(!$category){
            return response()->json([
                'success' => false,
                'message' => 'lo sentimos, la categoria con ' . $id . ' no se encuentra'
            ], 400);
        }
        return $category;
    }
}
