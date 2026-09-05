<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Review;
use Goedemiddag\ReCaptcha\Rules\ReCaptchaRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DriverController extends Controller
{
    public function show(Driver $driver): View
    {
        $driver->load('taxi');

        $reviews = $driver->reviews()->where('is_visible', true)->latest()->get();
        $avg = $driver->rating;

        return view('driver', compact('driver', 'reviews', 'avg'));
    }

    public function storeReview(Request $request, Driver $driver): RedirectResponse
    {
        $data = $request->validate([
            'user_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'g-recaptcha-response' => ['required', new ReCaptchaRule],
        ]);

        $colors = ['16a34a', 'd97706', 'db2777', '2563eb', '7c3aed', '0891b2'];
        $data['avatar_color'] = $colors[array_rand($colors)];

        Review::create([
            'driver_id' => $driver->id,
            'user_name' => $data['user_name'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'avatar_color' => $data['avatar_color'],
            'rating' => $data['rating'],
            'comment' => $data['comment'],
            'is_visible' => true,
        ]);

        $count = $driver->reviews()->where('is_visible', true)->count();
        $sum = (float) $driver->reviews()->where('is_visible', true)->sum('rating');
        $driver->update([
            'reviews_count' => $count,
            'rating' => $count > 0 ? round($sum / $count, 1) : 0,
        ]);

        return back()->with('success', __('Review submitted successfully!'));
    }
}
