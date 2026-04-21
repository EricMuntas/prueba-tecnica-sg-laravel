<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function dashboard()
    {
        return view('dashboard');
    }


    public function productIndex()
    {
        return view('product-index');
    }

    public function productShow(int $id)
    {
        return view('product-show', ['id' => $id]);
    }

    public function cartIndex()
    {
        return view('shopping-cart');
    }

    public function orderEdit(int $id)
    {
        return view('order-edit', ['orderId' => $id]);
    }
}

