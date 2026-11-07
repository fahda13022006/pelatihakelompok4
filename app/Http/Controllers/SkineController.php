<?php

namespace App\Http\Controllers;

class SkineController extends Controller
{
    public function dashboard()
    {
        return view('skine.dashboard');
    }

    public function products()
    {
        return view('skine.products');
    }

    public function orders()
    {
        return view('skine.orders');
    }

    public function distribution()
    {
        return view('skine.distribution');
    }

    public function store()
    {
        return view('skine.store');
    }
}
