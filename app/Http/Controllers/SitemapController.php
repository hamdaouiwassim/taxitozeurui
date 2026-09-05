<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $drivers = Driver::with('taxi')
            ->where('is_active', true)
            ->whereHas('taxi', fn ($q) => $q->where('is_active', true))
            ->get();

        $content = view('sitemap', compact('drivers'))->render();

        return response($content, 200)->header('Content-Type', 'application/xml');
    }
}
