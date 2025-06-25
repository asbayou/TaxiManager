@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1>Edit Taxi</h1>

    <form action="{{ route('taxis.update', $taxi) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="mb-3 col-md-6">
                <label for="plate_number" class="form-label">Plate Number</label>
                <input type="text" name="plate_number" id="plate_number" class="form-control @error('plate_number') is-invalid @enderror" value="{{ old('plate_number', $taxi->plate_number) }}" required>
                @error('plate_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 col-md-6">
                <label for="model" class="form-label">Model</label>
                <input type="text" name="model" id="model" class="form-control @error('model') is-invalid @enderror" value="{{ old('model', $taxi->model) }}">
                @error('model')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 col-md-6">
                <label for="year" class="form-label">Year</label>
                <input type="number" name="year" id="year" class="form-control @error('year') is-invalid @enderror" value="{{ old('year', $taxi->year) }}">
                @error('year')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 col-md-6">
                <label for="calculation_type" class="form-label">Calculation Type</label>
                <select name="calculation_type" id="calculation_type" class="form-select @error('calculation_type') is-invalid @enderror" required>
                    <option value="fixed" {{ old('calculation_type', $taxi->calculation_type) == 'fixed' ? 'selected' : '' }}>Fixed</option>
                    <option value="per_km" {{ old('calculation_type', $taxi->calculation_type) == 'per_km' ? 'selected' : '' }}>Per Kilometer</option>
                </select>
                @error('calculation_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 col-md-6">
                <label for="fixed_daily_price" class="form-label">Fixed Daily Price</label>
                <input type="number" step="0.01" name="fixed_daily_price" id="fixed_daily_price" class="form-control @error('fixed_daily_price') is-invalid @enderror" value="{{ old('fixed_daily_price', $taxi->fixed_daily_price) }}">
                @error('fixed_daily_price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 col-md-6">
                <label for="price_per_km" class="form-label">Price Per Kilometer</label>
                <input type="number" step="0.01" name="price_per_km" id="price_per_km" class="form-control @error('price_per_km') is-invalid @enderror" value="{{ old('price_per_km', $taxi->price_per_km) }}">
                @error('price_per_km')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 col-md-12">
                <label for="notes" class="form-label">Notes</label>
                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $taxi->notes) }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-check mb-4 col-md-12">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $taxi->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Update Taxi</button>
        <a href="{{ route('taxis.manage') }}" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>
@endsection
