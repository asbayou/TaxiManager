<?php

namespace App\Http\Controllers;

use App\Models\Taxi;
use App\Models\DailyRecord;
use App\Models\Vidange;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TaxiController extends Controller
{
    // List recent daily records with filters and summary info
    public function index(Request $request)
    {
        $query = DailyRecord::with('taxi');

        // Apply filters if present
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
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

        // Get filtered results, limit to last 20
        $recentDailyRecords = $query->latest()->limit(20)->get();

        // Return JSON if AJAX
        if ($request->ajax()) {
            $data = $recentDailyRecords->map(function ($record) {
                return [
                    'date' => $record->date ? Carbon::parse($record->date)->format('Y-m-d') : '',
                    'taxi_plate' => $record->taxi?->plate_number,
                    'kilometers' => $record->kilometers,
                    'earnings' => number_format($record->earnings, 2),
                    'expenses' => number_format($record->expenses, 2),
                ];
            });
            return response()->json($data);
        }

        // Summary data for dashboard
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $previousMonth = Carbon::now()->subMonth();
        $previousMonthNumber = $previousMonth->month;
        $previousMonthYear = $previousMonth->year;

        $totalEarningsThisMonth = DailyRecord::whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonth)
            ->sum('earnings');

        $totalEarningsPreviousMonth = DailyRecord::whereYear('date', $previousMonthYear)
            ->whereMonth('date', $previousMonthNumber)
            ->sum('earnings');

$totalExpensesThisMonth = DailyRecord::whereMonth('date', now()->month)->sum('expenses')
                        + Vidange::whereMonth('date', now()->month)->sum('cost');


        $totalExpensesPreviousMonth = DailyRecord::whereYear('date', $previousMonthYear)
            ->whereMonth('date', $previousMonthNumber)
            ->sum('expenses');

        // Last 7 days for sparklines
        $last7DaysLabels = [];
        $last7DaysEarnings = [];
        $last7DaysExpenses = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $last7DaysLabels[] = $date->format('d M');
            $last7DaysEarnings[] = DailyRecord::whereDate('date', $date->format('Y-m-d'))->sum('earnings');
            $last7DaysExpenses[] = DailyRecord::whereDate('date', $date->format('Y-m-d'))->sum('expenses');
        }

        $totalTaxis = Taxi::count();
        $activeTaxis = Taxi::where('is_active', true)->count();
        $inactiveTaxis = Taxi::where('is_active', false)->count();
        $taxis = Taxi::with(['vidanges', 'dailyRecords'])->get();

        // Vidange alert logic
        $vidangeAlerts = [];

foreach ($taxis as $taxi) {
    $lastVidange = Vidange::where('taxi_id', $taxi->id)->latest('date')->first();

    if ($lastVidange) {
        // Sum daily kilometers after last vidange date
        $kmSinceLast = DailyRecord::where('taxi_id', $taxi->id)
            ->where('date', '>', $lastVidange->date)
            ->sum('kilometers');
    } else {
        // Sum all kilometers if no vidange recorded
        $kmSinceLast = DailyRecord::where('taxi_id', $taxi->id)->sum('kilometers');
    }

    if ($kmSinceLast >= 7000) {
        $vidangeAlerts[] = [
            'taxi' => $taxi,
            'kmSinceLast' => $kmSinceLast
        ];
    }
}


        return view('taxis.index', compact(
            'recentDailyRecords',
            'totalEarningsThisMonth',
            'totalEarningsPreviousMonth',
            'totalExpensesThisMonth',
            'totalExpensesPreviousMonth',
            'totalTaxis',
            'activeTaxis',
            'inactiveTaxis',
            'last7DaysLabels',
            'last7DaysEarnings',
            'last7DaysExpenses',
            'taxis',
            'vidangeAlerts'
        ));
    }

    // Show form to create new taxi
    public function create()
    {
        return view('taxis.create');
    }

    // Store new taxi in DB
    public function store(Request $request)
    {
        $request->validate([
            'plate_number' => 'required|string|max:255|unique:taxis,plate_number',
            'model' => 'nullable|string|max:255',
            'year' => 'nullable|numeric|min:1990|max:' . Carbon::now()->year,
            'calculation_type' => 'required|in:fixed,per_km',
            'fixed_daily_price' => 'nullable|numeric|min:0',
            'price_per_km' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        Taxi::create([
            'plate_number' => $request->plate_number,
            'model' => $request->model,
            'year' => $request->year,
            'calculation_type' => $request->calculation_type,
            'fixed_daily_price' => $request->fixed_daily_price,
            'price_per_km' => $request->price_per_km,
            'notes' => $request->notes,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('taxis.index')->with('success', 'Taxi created successfully!');
    }

    // Show form to edit an existing taxi
    public function edit(Taxi $taxi)
    {
        return view('taxis.edit', compact('taxi'));
    }

    // Update taxi in DB
    public function update(Request $request, Taxi $taxi)
    {
        $request->validate([
            'plate_number' => 'required|string|max:255|unique:taxis,plate_number,' . $taxi->id,
            'model' => 'nullable|string|max:255',
            'year' => 'nullable|numeric|min:1990|max:' . Carbon::now()->year,
            'calculation_type' => 'required|in:fixed,per_km',
            'fixed_daily_price' => 'nullable|numeric|min:0',
            'price_per_km' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $taxi->update([
            'plate_number' => $request->plate_number,
            'model' => $request->model,
            'year' => $request->year,
            'calculation_type' => $request->calculation_type,
            'fixed_daily_price' => $request->fixed_daily_price,
            'price_per_km' => $request->price_per_km,
            'notes' => $request->notes,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('taxis.index')->with('success', 'Taxi updated successfully!');
    }

    // Delete taxi from DB
    public function destroy(Taxi $taxi)
    {
        $taxi->delete();
        return redirect()->route('taxis.index')->with('success', 'Taxi deleted successfully!');
    }

    // Manage view showing all taxis with edit/delete buttons
    public function manage()
    {
        $taxis = Taxi::with(['vidanges', 'dailyRecords'])->get();
        return view('taxis.manage', compact('taxis'));
    }

    // Get monthly data for a single taxi (earnings/expenses by day)
    public function monthlyData(Request $request, Taxi $taxi)
    {
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);

        $dailyRecords = DailyRecord::where('taxi_id', $taxi->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get();
$vidanges = Vidange::where('taxi_id', $taxi->id)
    ->whereYear('date', $year)
    ->whereMonth('date', $month)
    ->get(['date', 'cost']);

        $data = [];

        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::create($year, $month, $d)->format('Y-m-d');
            $data[$date] = ['earnings' => 0, 'expenses' => 0];
        }

        foreach ($dailyRecords as $record) {
            $dateKey = Carbon::parse($record->date)->format('Y-m-d');

            $data[$dateKey] = [
                'earnings' => $record->earnings,
                'expenses' => $record->expenses,
            ];
        }

        $labels = array_keys($data);
        $earnings = array_map(fn($v) => $v['earnings'], $data);
        $expenses = array_map(fn($v) => $v['expenses'], $data);

        return response()->json([
            'labels' => $labels,
            'earnings' => $earnings,
            'expenses' => $expenses,
            'total_earnings' => array_sum($earnings),
            'total_expenses' => array_sum($expenses) +  $vidanges->sum('cost'),
               
            'taxi_plate' => $taxi->plate_number,
            'year' => $year,
            'month' => $month,
            'vidanges' => $vidanges,
        ]);
    }
}
