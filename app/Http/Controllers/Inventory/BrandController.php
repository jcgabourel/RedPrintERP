<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('inventory.brands.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('inventory.brands.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // La creación se maneja mediante API desde el frontend
        return redirect()->route('inventory.brands.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('inventory.brands.show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('inventory.brands.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // La actualización se maneja mediante API desde el frontend
        return redirect()->route('inventory.brands.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // La eliminación se maneja mediante API desde el frontend
        return redirect()->route('inventory.brands.index');
    }
}