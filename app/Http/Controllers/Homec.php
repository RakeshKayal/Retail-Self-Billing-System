<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

use App\Models\Category;

class Homec extends Controller
{
    //

    public function home(){
        // Redirect based on authentication status and role
        if (Auth::check()) {
            if (in_array(Auth::user()->role, ['admin', 'staff'], true)) {
                return redirect('/dashboard');
            }

            return redirect('/customer');
        }

        return redirect('/login');
    }

    public function add(){
        $categories = Category::orderBy("cat_name","asc")->get();
        return view("AddProduct", compact('categories'));
    }
    
    public function dashboard(){
        // Admin and staff share the same management dashboard
        if (in_array(Auth::user()->role, ['admin', 'staff'], true)) {
            return view('AdminDashboard');
        }
        
        // Customer dashboard
        $totalProducts = \App\Models\Product::count();
        $totalCategories = Category::count();
        $todayBills = \App\Models\Bill::whereDate('created_at', today())->count();
        $totalRevenue = \App\Models\Bill::sum('total_amount') ?? 0;
        $recentProducts = \App\Models\Product::latest()->limit(10)->with('category')->get();
        
        return view('Home', compact('totalProducts', 'totalCategories', 'todayBills', 'totalRevenue', 'recentProducts'));
    }
    
    public function delete(){
        return view("DeleteProduct");
    }
    public function addcat(){
        $categories = Category::orderBy("cat_name","asc")->get();
        return view("AddCatagories", compact('categories'));
    }

    public function bill(){
        $products = \App\Models\Product::with('category')->orderBy("product_name","asc")->get();
        return view("Billing", compact('products'));
    }
    
}
