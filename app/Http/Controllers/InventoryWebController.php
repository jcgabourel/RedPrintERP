<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventoryWebController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function productsIndex()
    {
        return view('inventory.products.index');
    }

    /**
     * Show the form for creating a new product.
     */
    public function productsCreate()
    {
        return view('inventory.products.create');
    }

    /**
     * Display the specified product.
     */
    public function productsShow($id)
    {
        return view('inventory.products.show', compact('id'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function productsEdit($id)
    {
        return view('inventory.products.edit', compact('id'));
    }

    /**
     * Display a listing of the categories.
     */
    public function categoriesIndex()
    {
        return view('inventory.categories.index');
    }

    /**
     * Show the form for creating a new category.
     */
    public function categoriesCreate()
    {
        return view('inventory.categories.create');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function categoriesEdit($id)
    {
        return view('inventory.categories.edit', compact('id'));
    }

    /**
     * Display a listing of the brands.
     */
    public function brandsIndex()
    {
        return view('inventory.brands.index');
    }

    /**
     * Show the form for creating a new brand.
     */
    public function brandsCreate()
    {
        return view('inventory.brands.create');
    }

    /**
     * Show the form for editing the specified brand.
     */
    public function brandsEdit($id)
    {
        return view('inventory.brands.edit', compact('id'));
    }

    /**
     * Display a listing of the warehouses.
     */
    public function warehousesIndex()
    {
        return view('inventory.warehouses.index');
    }

    /**
     * Show the form for creating a new warehouse.
     */
    public function warehousesCreate()
    {
        return view('inventory.warehouses.create');
    }

    /**
     * Show the form for editing the specified warehouse.
     */
    public function warehousesEdit($id)
    {
        return view('inventory.warehouses.edit', compact('id'));
    }

    /**
     * Display inventory movements.
     */
    public function movementsIndex()
    {
        return view('inventory.movements.index');
    }

    /**
     * Show the form for creating a new inventory movement.
     */
    public function movementsCreate()
    {
        return view('inventory.movements.create');
    }

    /**
     * Display low stock alerts.
     */
    public function alertsIndex()
    {
        return view('inventory.alerts.index');
    }

    /**
     * Display inventory dashboard.
     */
    public function dashboard()
    {
        return view('inventory.dashboard');
    }
}