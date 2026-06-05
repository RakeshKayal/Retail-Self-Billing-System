<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use Illuminate\Support\Facades\Redirect;
use App\Models\Product;

class ProductC extends Controller
{
    //

    public function addpro(Request $req){

   


         $product_name=$req->product_name;
         $product_price=$req->product_price;
         $product_barcode=$req->product_barcode;
         $cat_id=$req->cat_id;
         $fl=$req->file("p_img");
         if(isset($fl)){
            $fn=$fl->getClientOriginalName();
            $fl->move("product_img",$fn);

         
         
        $obj= new Product();
        $obj->product_name=$product_name;
        $obj->product_price=$product_price;
        $obj->product_barcode=$product_barcode;
        $obj->cat_id=$cat_id;
        $obj->p_img=$fn;
        $obj->save();
        return redirect()->route('addProduct')->with('success', 'Product added successfully!');
        }else{
            return redirect()->route('addProduct')->with('error', 'Something went wrong!');
        }

    
    }



    public function add_cate(Request $r){
        $cn=$r->cat_name;
        $obj= new Category();
        $obj->cat_name=$cn;
        $obj->save();
        return Redirect()->route('addcat')->with('success', 'Category added successfully!');;

    }

    public function show(){
        $data = Product::with('category')->orderBy("product_name","asc")->get();
        return view("ShowProduct")->with(["pro"=>$data]);
    }
   public function manage(){
    $products = Product::with('category')->orderBy("product_name","asc")->get();
    return view("ManageProduct", compact('products'));
}

public function edit($id){
    $product = Product::with('category')->find($id);
    $categories = Category::orderBy("cat_name","asc")->get();
    return view("EditProduct", compact('product', 'categories'));
}

public function update(Request $req, $id){
   

    DB::table('product')->where('product_id', $id)->update([
        'product_name'    => $req->product_name,
        'product_price'   => $req->product_price,
        'product_barcode' => $req->product_barcode,
        'updated_at'      => now(),
    ]);

    return redirect()->route('manageProduct')->with('success', 'Product updated successfully!');
}

public function destroy($id){
    DB::table('product')->where('product_id', $id)->delete();
    return redirect()->route('manageProduct')->with('success', 'Product deleted successfully!');
}

public function delete_cate($id){
    DB::table('categories')->where('cat_id', $id)->delete();
    return redirect()->route('addcat')->with('success', 'Category deleted successfully!');
}
}
