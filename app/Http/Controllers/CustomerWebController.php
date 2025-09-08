<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerWebController extends Controller
{
    /**
     * Display a listing of the customers.
     */
    public function index()
    {
        return view('customers.index');
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Display the specified customer.
     */
    public function show($id)
    {
        return view('customers.show', compact('id'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit($id)
    {
        return view('customers.edit', compact('id'));
    }
}