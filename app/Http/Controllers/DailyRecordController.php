<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Taxi;
use App\Models\DailyRecord;
use Illuminate\Support\Facades\Validator;

class DailyRecordController extends Controller
{
 

public function index(Request $request)
{
    $query = DailyRecord::with('taxi');

    // Filters
    if ($request->filled('date_from') && $request->filled('date_to')) {
        $query->whereBetween('date', [$request->date_from, $request->date_to]);
    } elseif ($request->filled('date_from')) {
        $query->where('date', '>=', $request->date_from);
    } elseif ($request->filled('date_to')) {
        $query->where('date', '<=', $request->date_to);
    }

    if ($request->filled('taxi_plate')) {
        $query->whereHas('taxi', function ($q) use ($request) {
            $q->where('plate_number', 'like', '%' . $request->taxi_plate . '%');
        });
    }

    if ($request->filled('calculation_type')) {
        $query->whereHas('taxi', function ($q) use ($request) {
            $q->where('calculation_type', $request->calculation_type);
        });
    }

    $records = $query->latest()->paginate(15);
    $taxis = Taxi::all();

    return view('daily-records.index', compact('records', 'taxis'));
}

public function create()
{
    $taxis = Taxi::where('is_active', true)->get();
    return view('daily_records.create', compact('taxis'));
}

public function store(Request $request)
{
    $request->validate([
        'taxi_id' => 'required|exists:taxis,id',
        'date' => 'required|date',
        'kilometers' => 'required|numeric|min:0',
        'earnings' => 'required|numeric|min:0',
        'expenses' => 'required|numeric|min:0',
        'notes' => 'nullable|string',
        'Kilometre' => 'nullable|numeric|min:0',
    ]);

    DailyRecord::create($request->all());

    return redirect()->route('taxis.index')->with('success', 'Daily record added successfully!');
}
}
