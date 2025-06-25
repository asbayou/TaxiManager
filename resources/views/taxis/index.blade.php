@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Dashboard</h1>

    {{-- Vidange Alerts --}}
@foreach($taxis as $taxi)
 @php
    $lastVidange = $taxi->vidanges->sortByDesc('date')->first();
    $lastVidangeDate = $lastVidange?->date;

    $records = $lastVidangeDate
        ? $taxi->dailyRecords->where('date', '>', $lastVidangeDate)
        : $taxi->dailyRecords;

    $totalKm = $records->sum('kilometers');
@endphp


    @if ($totalKm >= 8000)
        <div class="alert alert-danger fw-bold d-flex justify-content-between align-items-center mb-3">
            🔴 Taxi {{ $taxi->plate_number }} needs immediate vidange ({{ $totalKm }} km)
            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#vidangeModal{{ $taxi->id }}">
                Confirm Vidange
            </button>
            
<!-- Modal -->
<div class="modal fade" id="vidangeModal{{ $taxi->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('vidange.store') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="taxi_id" value="{{ $taxi->id }}">
            
            <div class="modal-header">
                <h5 class="modal-title">Confirm Vidange - Taxi {{ $taxi->plate_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label for="date{{ $taxi->id }}" class="form-label">Date</label>
                    <input type="date" name="date" id="date{{ $taxi->id }}" class="form-control"
                           value="{{ now()->toDateString() }}" required>
                </div>

                <div class="mb-3">
                    <label for="kilometers{{ $taxi->id }}" class="form-label">Kilometers</label>
                    <input type="number" name="kilometers" id="kilometers{{ $taxi->id }}" class="form-control"
                           value="{{ $totalKm  }}" required>
                </div>

                <div class="mb-3">
                    <label for="cost{{ $taxi->id }}" class="form-label">Cost (DH)</label>
                    <input type="number" name="cost" id="cost{{ $taxi->id }}" class="form-control"
                           step="0.01" required>
                </div>

                <div class="mb-3">
                    <label for="notes{{ $taxi->id }}" class="form-label">Notes</label>
                    <textarea name="notes" id="notes{{ $taxi->id }}" rows="2"
                              class="form-control" placeholder="Optional..."></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Save Vidange</button>
            </div>
        </form>
    </div>
</div>
        </div>
    @elseif ($totalKm >= 7000)
        <div class="alert alert-warning fw-bold mb-3">
            ⚠️ Taxi {{ $taxi->plate_number }} needs vidange soon ({{ $totalKm }} km)
        </div>
    @endif
@endforeach

    {{-- Main summary cards --}}
    <div class="row row-cols-1 row-cols-md-5 g-4 justify-content-center mb-5">
        @php
            $cards = [
                ['title' => 'Total Taxis', 'value' => $totalTaxis, 'bg' => 'primary', 'icon' => 'fa-taxi'],
                ['title' => 'Active Taxis', 'value' => $activeTaxis, 'bg' => 'success', 'icon' => 'fa-check-circle'],
                ['title' => 'Inactive Taxis', 'value' => $inactiveTaxis, 'bg' => 'dark', 'icon' => 'fa-times-circle'],
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="col">
                <div class="card h-100 text-white bg-{{ $card['bg'] }} shadow-lg border-0">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center mb-3">
                            <div class="fs-1 me-3">
                                <i class="fa-solid {{ $card['icon'] }}"></i>
                            </div>
                            <div>
                                <div class="text-uppercase small fw-bold">{{ $card['title'] }}</div>
                                <div class="h3 mb-0 mt-2 fw-semibold">{{ $card['value'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
<div class="row row-cols-1 row-cols-md-5 g-4 justify-content-center mb-5">
    @foreach ($taxis as $taxi)
        @php
            $lastVidange = $taxi->vidanges->sortByDesc('date')->first();
            $lastVidangeDate = $lastVidange?->date ? \Carbon\Carbon::parse($lastVidange->date)->format('Y-m-d') : 'No record';
        @endphp

        <div class="col">
            <div class="card h-100 text-white bg-warning shadow-lg border-0">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-3">
                        <div class="fs-1 me-3">
                            <i class="fa-solid fa-oil-can"></i>
                        </div>
                        <div>
                            <div class="text-uppercase small fw-bold">Taxi {{ $taxi->plate_number }}</div>
                            <div class="h6 mb-0 mt-2 fw-semibold">Last Vidange: {{ $lastVidangeDate }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endforeach
</div>

    {{-- Earnings & Expenses with percentage change --}}
    @php
        $earningsChange = $totalEarningsPreviousMonth > 0
            ? (($totalEarningsThisMonth - $totalEarningsPreviousMonth) / $totalEarningsPreviousMonth) * 100
            : 0;
        $expensesChange = $totalExpensesPreviousMonth > 0
            ? (($totalExpensesThisMonth - $totalExpensesPreviousMonth) / $totalExpensesPreviousMonth) * 100
            : 0;
    @endphp

    <div class="row g-4 mb-5 justify-content-center">
        <div class="col-12 col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white fw-bold">
                    Earnings This Month
                </div>
                <div class="card-body d-flex align-items-center justify-content-between">
                    <h3 class="mb-0">{{ number_format($totalEarningsThisMonth, 2) }} DH</h3>
                    <div class="fs-4 {{ $earningsChange >= 0 ? 'text-success' : 'text-danger' }}">
                        <i class="fa-solid fa-arrow-{{ $earningsChange >= 0 ? 'up' : 'down' }}"></i>
                        {{ number_format(abs($earningsChange), 2) }}%
                    </div>
                </div>
                <div class="card-footer text-muted small">
                    Compared to last month
                </div>
            </div>
        </div>

        <div class="col-12 col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-danger text-white fw-bold">
                    Expenses This Month
                </div>
                <div class="card-body d-flex align-items-center justify-content-between">
                    <h3 class="mb-0">{{ number_format($totalExpensesThisMonth, 2) }} DH</h3>
                    <div class="fs-4 {{ $expensesChange >= 0 ? 'text-danger' : 'text-success' }}">
                        <i class="fa-solid fa-arrow-{{ $expensesChange >= 0 ? 'up' : 'down' }}"></i>
                        {{ number_format(abs($expensesChange), 2) }}%
                    </div>
                </div>
                <div class="card-footer text-muted small">
                    Compared to last month
                </div>
            </div>
        </div>
    </div>

    {{-- Combined Sparkline chart for last 7 days earnings and expenses --}}
    <div class="row g-4 mb-5 justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header fw-bold">Earnings vs Expenses Last 7 Days</div>
                <div class="card-body">
                    <canvas id="combinedSparkline" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters & Table --}}
    <div class="container mt-5">
        <!-- Filters -->
        <form id="filterForm" class="row g-3 mb-4">
            <div class="col-md-3">
                <label for="date_from" class="form-label">Date From</label>
                <input type="date" class="form-control filter-input" id="date_from" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label">Date To</label>
                <input type="date" class="form-control filter-input" id="date_to" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <label for="taxi_plate" class="form-label">Taxi Plate</label>
                <input type="text" class="form-control filter-input" id="taxi_plate" name="taxi_plate" placeholder="Search plate" value="{{ request('taxi_plate') }}">
            </div>
            <div class="col-md-3">
                <label for="calculation_type" class="form-label">Calculation Type</label>
                <select class="form-select filter-input" id="calculation_type" name="calculation_type">
                    <option value="">All</option>
                    <option value="fixed" {{ request('calculation_type') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                    <option value="per_km" {{ request('calculation_type') == 'per_km' ? 'selected' : '' }}>Per Kilometer</option>
                </select>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Taxi Plate</th>
                        <th>Kilometers</th>
                        <th>Earnings (DH)</th>
                        <th>Expenses (DH)</th>
                    </tr>
                </thead>
                <tbody id="dailyRecordsBody">
                    @foreach($recentDailyRecords as $record)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($record->date)->format('Y-m-d') }}</td>
                            <td>{{ $record->taxi?->plate_number ?? 'N/A' }}</td>
                            <td>{{ $record->kilometers }}</td>
                            <td>{{ number_format($record->earnings, 2) }}</td>
                            <td>{{ number_format($record->expenses, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.querySelectorAll('.filter-input').forEach(input => {
    if (input.type === 'text') {
        input.addEventListener('keyup', event => {
            if (event.key === 'Enter') {
                fetchFilteredData();
            }
        });
    } else {
        input.addEventListener('change', fetchFilteredData);
    }
});

function fetchFilteredData() {
    const formData = new FormData(document.getElementById('filterForm'));
    const params = new URLSearchParams(formData).toString();

    fetch(`{{ route('taxis.index') }}?${params}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        const tbody = document.getElementById('dailyRecordsBody');
        tbody.innerHTML = '';

        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center">No records found.</td></tr>';
            return;
        }

        data.forEach(record => {
            tbody.innerHTML += `
                <tr>
                    <td>${record.date}</td>
                    <td>${record.taxi_plate ?? 'N/A'}</td>
                    <td>${record.kilometers}</td>
                    <td>${record.earnings}</td>
                    <td>${record.expenses}</td>
                </tr>
            `;
        });
    })
    .catch(error => console.error('Error fetching data:', error));
}

const labels = @json($last7DaysLabels);
const earningsData = @json($last7DaysEarnings);
const expensesData = @json($last7DaysExpenses);

new Chart(document.getElementById('combinedSparkline'), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Earnings',
                data: earningsData,
                borderColor: 'green',
                fill: false,
                tension: 0.3,
                pointRadius: 0,
            },
            {
                label: 'Expenses',
                data: expensesData,
                borderColor: 'red',
                fill: false,
                tension: 0.3,
                pointRadius: 0,
            }
        ]
    },
    options: {
        plugins: { legend: { display: true } },
        scales: {
            x: { display: false },
            y: { beginAtZero: true }
        }
    }
});
</script>
@endsection
