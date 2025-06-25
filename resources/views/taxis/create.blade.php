@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Add New Taxi</h2>

    <form method="POST" action="{{ route('taxis.store') }}">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Plate Number</label>
                <input type="text" name="plate_number" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Model</label>
                <input type="text" name="model" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Year</label>
                <input type="number" name="year" class="form-control" min="1990" max="2100">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Calculation Type</label>
                <select name="calculation_type" class="form-control" required>
                    <option value="fixed">Fixed Daily Rate</option>
                    <option value="per_km">Per Kilometer</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Fixed Daily Price (DH)</label>
                <input type="number" step="0.01" name="fixed_daily_price" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Price per Kilometer (DH)</label>
                <input type="number" step="0.01" name="price_per_km" class="form-control">
            </div>

            <div class="col-12 mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="2"></textarea>
            </div>

            <div class="col-md-6 mb-3 form-check">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                <label class="form-check-label">Active</label>
            </div>

            <div class="col-md-6 mb-3 text-end">
                <button type="submit" class="btn btn-success">Add Taxi</button>
            </div>
        </div>
    </form>
</div>
@endsection
