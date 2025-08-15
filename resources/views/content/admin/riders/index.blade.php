@extends('layouts/layoutMaster')

@section('title', 'Gestión de Riders')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'])
@endsection

@section('page-script')
  {{-- Vamos a crear este archivo JS en el siguiente paso --}}
  @vite('resources/assets/js/admin/riders-list.js')
@endsection

@section('content')
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Admin /</span> Riders
  </h4>

  <div class="card">
    <div class="card-header">
      <h5 class="card-title">Listado de Riders</h5>
    </div>
    <div class="card-datatable table-responsive">
      <table class="table" id="riders-table">
        <thead class="border-top">
          <tr>
            <th>ID</th>
            <th>Nombre Completo</th>
            <th>DNI</th>
            <th>Ciudad</th>
            <th>Email</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
@endsection
