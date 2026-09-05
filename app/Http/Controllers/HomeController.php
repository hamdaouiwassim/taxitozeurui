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
            ->whereHas('taxi', fn ($q) => $q->where('is_active', true))
            ->orderByDesc('rating')
            ->orderByDesc('reviews_count')
            ->get();

        return view('index', compact('drivers'));
    }
}
