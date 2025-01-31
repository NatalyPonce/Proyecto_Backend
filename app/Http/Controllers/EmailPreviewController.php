<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmailPreviewController extends Controller
{
    public function __invoke(){

        request()-> validate([
            'customer'=> ['required','string'],
            'email'=>['required', 'email'],
            'payment_method'=>['required', 'in:1,2,3'],
            'products'=>['required', 'array'],
            'products.*.name'=>['required', 'string', 'max:50'],
            'products.*.price'=>['required', 'numeric', 'gt:0'],
            'products.*.quantity'=>['required', 'integer', 'gte:1'],
            //'customer' => ['required', 'string'] es lo mismo que el de arriba
        ]);
       $request = request()-> all(); //para todos los valores
      /*   //customer = request() -> input('customer') para un valor especifico
    
      */ 
      $productos = [];
      foreach($request['products'] as $producto){
        $productos[]=[$producto['name'], $producto['quantity'], $producto['price'], $producto['quantity']*$producto['price']];
      }
      $total=0;
      foreach($productos as $producto){
        $total+=end($producto); //el metodo end() me permite acceder al ultimo elemento de un array, que en este caso es el subtotal.
      }
 
      $data = [
        'customer'=> $request['customer'],
        'producto'=> $productos,
        'total' => $total,
        'empresa'=>'Libros Marquez',
        'anio' =>now()->format('Y'),
        'created_at'=>now()->format('Y-m-d H:i'),
        'email'=>$request['email'],
        'order_number'=>'RB'.now()->format('Y').now()->format('m').'-'.rand(1, 100),
        'payment_method'=> match($request['payment_method']){
            1=>'Transferencia bancaria',
            2=> 'Contraentrega',
            3=> 'Tarjeta de credito',
        },
        'order_status'=> match($request['payment_method']){
            1=>'Pendiente de revision',
            2=> 'En proceso',
            3=> 'En proceso',
        },

    ];
       return view('EmailPreview', $data);
    }
        
}
