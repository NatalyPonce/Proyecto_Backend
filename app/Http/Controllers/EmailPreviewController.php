<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmailPreviewController extends Controller
{
    public function __invoke(){

        request()-> validate([
          //Validaciones de los campos del JSON.
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
      //se crea un arreglo vacío.
      $productos = [];
      //se itera por cada producto en products y se meten al arreglo creado anteriormente. Simulando esta estructura: productos[[producto1], [Producto2]]
      foreach($request['products'] as $producto){
        $productos[]=[$producto['name'], $producto['quantity'], $producto['price'], $producto['quantity']*$producto['price']];
      }

      //se asigna el total en cero.
      $total=0;

      //Aprovechando el arreglo de productos que creamos anteriormente, tomó el subtotal y lo sumo acumulativamente.
      foreach($productos as $producto){
        $total+=end($producto); //el metodo end() me permite acceder al ultimo elemento de un array, que en este caso es el subtotal.
      }
 
      $data = [
        // guardos los datos a través de la variable data
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
    //Envío data a el EmailPreview
       return view('EmailPreview', $data);
    }
        
}
