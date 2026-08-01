<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class DashboardController extends Controller
{
    public function owner()
    {
        return Inertia::render('Owner/Dashboard');
    }

    public function admin()
    {
        return Inertia::render('Admin/Dashboard');
    }

    public function sales()
    {
        return Inertia::render('Sales/Dashboard');
    }

    public function customer()
    {
        return Inertia::render('Customer/Dashboard');
    }
}
