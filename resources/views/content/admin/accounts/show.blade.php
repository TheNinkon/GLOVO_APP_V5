@extends('layouts/layoutMaster')

@section('title', 'Historial de Cuenta')

@section('content')
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Admin / Cuentas /</span> Historial
  </h4>

  {{-- Tarjeta con detalles de la Cuenta --}}
  <div class="card mb-4">
    <div class="card-body">
      <h5 class="card-title">Cuenta: {{ $account->courier_id }}</h5>
      <div class="row">
        <div class="col-md-6">
          <p class="mb-1"><strong>Email:</strong> {{ $account->email }}</p>
          <p class="mb-1"><strong>Ciudad:</strong> {{ $account->city }}</p>
          <p class="mb-0"><strong>Estado:</strong> <span
              class="badge bg-label-{{ $account->status == 'active' ? 'success' : 'danger' }}">{{ ucfirst($account->status) }}</span>
          </p>
        </div>
        <div class="col-md-6">
          <p class="mb-1"><strong>Fecha Inicio:</strong> {{ $account->start_date->format('d/m/Y') }}</p>
          <p class="mb-0"><strong>Fecha Fin:</strong>
            {{ $account->end_date ? $account->end_date->format('d/m/Y') : 'N/A' }}</p>
        </div>
      </div>
    </div>
  </div>

  {{-- Tarjeta con el Historial de Asignaciones --}}
  <div class="card">
    <h5 class="card-header">Historial de Asignaciones</h5>
    <div class="table-responsive text-nowrap">
      <table class="table">
        <thead>
          <tr>
            <th>Rider Asignado</th>
            <th>Fecha de Inicio</th>
            <th>Fecha de Fin</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody class="table-border-bottom-0">
          @forelse ($account->assignments as $assignment)
            <tr>
              <td>
                {{-- Mostramos el nombre, incluso si el rider fue eliminado después --}}
                <strong>{{ $assignment->rider->full_name ?? 'Rider Eliminado (ID: ' . $assignment->rider_id . ')' }}</strong>
              </td>
              <td>{{ $assignment->start_at->format('d/m/Y H:i') }}</td>
              <td>{{ $assignment->end_at ? $assignment->end_at->format('d/m/Y H:i') : 'N/A' }}</td>
              <td>
                @if ($assignment->status == 'active')
                  <span class="badge bg-label-success">Activa</span>
                @else
                  <span class="badge bg-label-secondary">Finalizada</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center">Esta cuenta nunca ha sido asignada.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-4">
    <a href="{{ route('admin.accounts.index') }}" class="btn btn-label-secondary">
      <i class="ti tabler-arrow-left me-1"></i> Volver al listado
    </a>
  </div>

@endsection
