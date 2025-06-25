<?php

namespace App\Http\Controllers;

use App\Models\Taxi;
use App\Models\Vidange;
use App\Models\DailyRecord;
use Illuminate\Http\Request;

class VidangeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'taxi_id' => 'required|exists:taxis,id',
            'date' => 'required|date',
            'kilometers' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        Vidange::create($request->all());

        return redirect()->back()->with('success', 'Vidange recorded successfully!');
    }

    // Optional: for future, if you want to show vidanges
    public function history(Taxi $taxi)
    {
        $vidanges = $taxi->vidanges()->latest()->get();

        return view('vidanges.history', compact('taxi', 'vidanges'));
    }
}
