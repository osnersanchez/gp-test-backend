<?php

namespace App\Http\Controllers;

use App\shoppingCar;
use Illuminate\Http\Request;
use App\Product;
use JWTAuth;
use Illuminate\Support\Facades\DB;

class ShoppingCarController extends Controller
{

    protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index()
    {
        return $this->shoppingCarList();
    }

    public function store(Request $request)
    {
      $product = Product::find($request->idProduct);

      if (!$product) {
        return response()->json([
            'success' => false,
            'message' => 'lo sentimos, el producto con id ' . $request->idProduct . ' no se encuentra'
        ], 400);
      }

      if($product){
          if($request->quantity <= $product->quantity){
              $amount = $request->quantity * $product->price;
              $this->user->shopping()->attach($product->id, ['quantity' => $request->quantity, 'amount' => $amount]);
              return response()->json(['message' => 'Producto Agregado al Carrito'], 200);
          }
          return response()->json([
              'success' => false,
              'message' => 'lo sentimos, no se cuenta con suficiente stock para el producto con id ' . $request->idProduct
          ], 400);
      }

      return response()->json(['message' => 'Este producto no puede ser agregado al Carrito'], 500);
    }

    function destroy($id)
    {
        $order = shoppingCar::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'lo sentimos, la pedido con id ' . $id . ' no se encuentra'
            ], 400);
        }

        if ($order->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'pedido eliminado Exitosamente'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'este pedido no puede ser eliminado'
            ], 500);
        }
    }

    private function shoppingCarList(){
        $shoppingList =  DB::table('shopping_cars')
            ->join('products','shopping_cars.idProduct','=','products.id')
            ->select('shopping_cars.id', 'products.id as idProduct', 'products.price as pricePerUnit', 'products.name', 'shopping_cars.quantity','shopping_cars.amount', 'shopping_cars.status')
            ->where('shopping_cars.idUser','=',$this->user->id)
            ->get();

        return $shoppingList;
    }

    private function carStatus($status){
        $shoppingList =  DB::table('shopping_cars')
            ->join('products','shopping_cars.idProduct','=','products.id')
            ->select('shopping_cars.id', 'products.id as idProduct', 'products.price as pricePerUnit', 'products.name', 'shopping_cars.quantity','shopping_cars.amount', 'shopping_cars.status')
            ->where('shopping_cars.idUser','=',$this->user->id)
            ->where('shopping_cars.status','=',$status)
            ->get();

        return $shoppingList;
    }

    public function shoppingCarStatus($status){        
        $status = ($status == 'en_proceso' ? 'en proceso' : $status);
        $shoppingList =  $this->carStatus($status);

        if (!$shoppingList) {
            return response()->json([
                'success' => false,
                'message' => 'lo sentimos, no se encuentran pedidos con estado ' . $status
            ], 400);
        }

        if($shoppingList){
            return response()->json(['success' => true, 'data' => $shoppingList]);
        }

        return response()->json(['message' => 'Error consultado el estado de la orden'], 500);
    }

    public function checkoutCar()
    {
        $shopping = $this->carStatus('en proceso');

        if (!$shopping || !count($shopping)) {
            return response()->json([
                'success' => false,
                'message' => 'lo sentimos, no hay productos que procesar '
            ], 400);
        }

        foreach ($shopping as $key => $order) {
            $this->saveStatus($order->id,'comprado');
        }

        $shopping = $this->carStatus('en proceso');

        return response()->json(['success' => true, 'data' => $shopping]);
    }

    public function checkoutList(Request $request)
    {
        foreach ($request->order as $key => $order) {
            $this->saveStatus($order,'comprado');
        }
        $shopping = $this->carStatus('en proceso');

        return response()->json(['success' => true, 'data' => $shopping]);
    }

    private function saveStatus($id, $status){
        $shop = shoppingCar::find($id);
        if($shop){
            $product = Product::find($shop->idProduct);
            if($product){
                if($shop->quantity <= $product->quantity){
                    $product->quantity -= $shop->quantity;
                    $shop->status = $status;
                    $product->save();
                }else{
                    $shop->quantity = $product->quantity;
                }
                $shop->save();
            }
        }
    }
}
