@extends('layouts/layoutMaster')
@section('title', 'Importar Forecast')

@section('content')
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Admin / Forecasts /</span> Importar
  </h4>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('admin.forecasts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
          <label for="city" class="form-label">Ciudad</label>
          <input type="text" class="form-control" id="city" name="city" required>
        </div>
        <div class="mb-3">
          <label for="week_start_date" class="form-label">Fecha de Inicio de Semana (Lunes)</label>
          <input type="date" class="form-control" id="week_start_date" name="week_start_date" required>
        </div>
        <div class="mb-3">
          <label for="forecast_file" class="form-label">Archivo CSV del Forecast</label>
          <input class="form-control" type="file" id="forecast_file" name="forecast_file" required>
        </div>
        <button type="submit" class="btn btn-primary">Importar</button>
      </form>
    </div>
  </div>
@endsection
