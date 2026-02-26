<?php

namespace App\Http\Controllers;

use App\Models\Loker;
use Illuminate\Http\Request;

class LokerController extends Controller
{
    /**
     * Detail loker + daftar arsip (AJAX)
     */
    public function detail($id)
    {
        $loker = Loker::with([
            'lemari',
            'arsips' => function ($q) {
                $q->orderBy('created_at', 'asc');
            }
        ])->findOrFail($id);

        return view('admin.loker._detail', compact('loker'));
    }

    public function show($id)
{
    $loker = Loker::with(['lemari', 'arsips'])->findOrFail($id);

    return view('admin.loker.show-loker', compact('loker'));
}

}

