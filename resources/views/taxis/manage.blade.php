@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1>Manage Taxis</h1>

    <a href="{{ route('taxis.create') }}" class="btn btn-primary mb-3">Add New Taxi</a>

    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Plate Number</th>
                    <th>Model</th>
                    <th>Year</th>
                    <th>Calculation Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($taxis as $taxi)
                    <tr>
                        <td>{{ $taxi->plate_number }}</td>
                        <td>{{ $taxi->model ?? '-' }}</td>
                        <td>{{ $taxi->year ?? '-' }}</td>
                        <td>{{ ucfirst($taxi->calculation_type) }}</td>
                        <td>
                            @if ($taxi->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('taxis.edit', $taxi) }}" class="btn btn-sm btn-warning me-1 mb-1">Edit</a>

                            <button 
                                class="btn btn-sm btn-info btn-more-info me-1 mb-1" 
                                data-taxi-id="{{ $taxi->id }}"
                                data-taxi-plate="{{ $taxi->plate_number }}"
                            >More Info</button>

                            <form action="{{ route('taxis.destroy', $taxi) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this taxi?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger mb-1" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Taxi Monthly Info Modal -->
<div class="modal fade" id="taxiInfoModal" tabindex="-1" aria-labelledby="taxiInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="taxiInfoModalLabel">Taxi Monthly Info</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <div class="mb-3 d-flex align-items-center justify-content-between">
          <div>
            <strong>Taxi:</strong> <span id="modalTaxiPlate"></span>
          </div>
          <div>
            <label for="modalMonthSelect" class="form-label">Month:</label>
            <input type="month" id="modalMonthSelect" class="form-control d-inline-block" style="width: 150px;">
          </div>
        </div>

        <div class="mb-3 d-flex justify-content-around">
          <div>
            <h5>Earnings</h5>
            <div id="modalTotalEarnings" class="fs-4 text-success"></div>
          </div>
          <div>
            <h5>Expenses</h5>
            <div id="modalTotalExpenses" class="fs-4 text-danger"></div>
          </div>
        </div>

        <div id="vidangeDetails" class="mt-3"></div>

        <canvas id="taxiCombinedSparkline" height="100"></canvas>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- Bootstrap JS (make sure you have this included in your layout or add it here) --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
let taxiChart;
const modal = new bootstrap.Modal(document.getElementById('taxiInfoModal'));

document.querySelectorAll('.btn-more-info').forEach(button => {
    button.addEventListener('click', () => {
        const taxiId = button.dataset.taxiId;
        const taxiPlate = button.dataset.taxiPlate;

        document.getElementById('modalTaxiPlate').textContent = taxiPlate;

        const monthInput = document.getElementById('modalMonthSelect');
        if (!monthInput.value) {
            const now = new Date();
            monthInput.value = now.toISOString().slice(0,7);
        }

        loadTaxiMonthlyData(taxiId, monthInput.value);

        monthInput.onchange = () => {
            loadTaxiMonthlyData(taxiId, monthInput.value);
        };

        modal.show();
    });
});

function loadTaxiMonthlyData(taxiId, yearMonth) {
    if (!yearMonth) return;
    const [year, month] = yearMonth.split('-');

    fetch(`/taxis/${taxiId}/monthly-data?year=${year}&month=${month}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('modalTotalEarnings').textContent = Number(data.total_earnings).toFixed(2) + ' DH';
        document.getElementById('modalTotalExpenses').textContent = Number(data.total_expenses).toFixed(2) + ' DH';

        const vidangeDetails = document.getElementById('vidangeDetails');
        vidangeDetails.innerHTML = '';

        if (data.vidanges && data.vidanges.length > 0) {
            const title = document.createElement('h6');
            title.textContent = 'Vidanges This Month';
            vidangeDetails.appendChild(title);

            const ul = document.createElement('ul');
            ul.className = 'list-group';

            data.vidanges.forEach(v => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = `
                    <span><i class="fa-solid fa-oil-can text-warning me-2"></i>${v.date}</span>
                    <strong>${Number(v.cost).toFixed(2)} DH</strong>
                `;
                ul.appendChild(li);
            });

            vidangeDetails.appendChild(ul);
        } else {
            vidangeDetails.innerHTML = '<p class="text-muted">No vidanges this month.</p>';
        }

        const ctx = document.getElementById('taxiCombinedSparkline').getContext('2d');
        if (taxiChart) taxiChart.destroy();

        taxiChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels.map(d => d.slice(8)),
                datasets: [
                    {
                        label: 'Earnings',
                        data: data.earnings,
                        borderColor: 'green',
                        fill: true,
                        tension: 0.3,
                    },
                    {
                        label: 'Expenses',
                        data: data.expenses,
                        borderColor: 'red',
                        fill: false,
                        tension: 0.3,
                    }
                ]
            },
            options: {
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    x: { display: true, title: { display: true, text: 'Day' } },
                    y: { display: true, title: { display: true, text: 'Amount (DH)' } }
                },
                elements: {
                    point: { radius: 3 }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
    })
    .catch(e => {
        console.error('Error loading taxi monthly data:', e);
        alert('Failed to load taxi data.');
    });
}
</script>
@endsection
