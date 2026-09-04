<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $drivers = Driver::with('taxi')
            ->where('is_active', true)
            ->get();

        return view('index', compact('drivers'));
    }
}
