<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function index()
    {
        $user = Auth::user();

        // Jika menggunakan Breeze, redirect ke edit profile
        return redirect()->route('profile.edit');

        // Atau jika ingin tetap gunakan view lama
        // return view('profile.index', compact('user'));
    }

    /**
     * Update the user's profile.
     */

}
