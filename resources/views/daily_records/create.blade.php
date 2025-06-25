@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Add Daily Record</h2>

    <form action="{{ route('daily-records.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="taxi_id" class="form-label">Taxi</label>
                <select name="taxi_id" id="taxi_id" class="form-select" required>
                    <option value="">Select a taxi</option>
                    @foreach ($taxis as $taxi)
                        <option value="{{ $taxi->id }}">{{ $taxi->plate_number }} - {{ $taxi->model }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" id="date" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="kilometers" class="form-label">Kilometers</label>
                <input type="number" step="0.01" name="kilometers" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="earnings" class="form-label">Earnings (DH)</label>
                <input type="number" step="0.01" name="earnings" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="expenses" class="form-label">Expenses (DH)</label>
                <input type="number" step="0.01" name="expenses" class="form-control" required>
            </div>

              <div class="col-md-6 mb-3">
                <label for="expenses" class="form-label">Kilometrage de TAXI</label>
                <input type="number" step="0.01" name="Kilometre" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3"></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Save Record</button>
    </form>
</div>
@endsection
