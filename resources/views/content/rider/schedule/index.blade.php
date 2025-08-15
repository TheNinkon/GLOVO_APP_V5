@extends('layouts/layoutMaster')

@section('title', 'Seleccionar Horario')

@section('page-style')
  @vite('resources/assets/css/pages/app-schedule.css')
@endsection

@section('content')
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Mi Panel /</span> Seleccionar Horario
  </h4>

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  @isset($forecast)
    {{-- NUEVO PANEL DE RESUMEN --}}
    <div class="card mb-4" id="schedule-summary" data-contract-hours="{{ $rider->weekly_contract_hours }}">
      <div class="card-body d-flex justify-content-around">
        <div class="text-center">
          <p class="mb-1">Horas de Contrato</p>
          <h4 class="mb-0">{{ $rider->weekly_contract_hours }}h</h4>
        </div>
        <div class="text-center">
          <p class="mb-1">Horas Seleccionadas</p>
          <h4 class="mb-0"><span id="selected-hours-counter">{{ number_format($myHoursCount, 1) }}</span>h</h4>
        </div>
      </div>
    </div>


    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Horario para la semana del {{ $startOfWeek->format('d/m/Y') }}</h5>
        <p class="card-subtitle">Haz clic en los turnos verdes para seleccionarlos.</p>
      </div>
      <div class="card-body">
        <form action="{{ route('rider.schedule.store') }}" method="POST">
          @csrf
          <div class="table-responsive text-nowrap">
            <table class="table table-bordered schedule-table">
              <thead>
                <tr>
                  <th>Hora</th>
                  @for ($i = 0; $i < 7; $i++)
                    <th>{{ $startOfWeek->clone()->addDays($i)->translatedFormat('D d/m') }}</th>
                  @endfor
                </tr>
              </thead>
              <tbody>
                @php
                  $startTime = \Carbon\Carbon::createFromTimeString('00:00');
                @endphp
                @for ($j = 0; $j < 48; $j++)
                  <tr>
                    <td>{{ $startTime->clone()->addMinutes($j * 30)->format('H:i') }}</td>
                    @for ($i = 0; $i < 7; $i++)
                      @php
                        $currentDate = $startOfWeek->clone()->addDays($i);
                        $currentTime = $startTime->clone()->addMinutes($j * 30);
                        $dayKey = strtolower($currentDate->format('D'));
                        $timeKey = $currentTime->format('H:i');
                        $slotIdentifier = $currentDate->format('Y-m-d') . '_' . $currentTime->format('H:i:s');

                        $demand = $forecast->forecast_data[$dayKey][$timeKey] ?? 0;
                        $booked = $bookedSchedules[$slotIdentifier]['total'] ?? 0;
                        $isMine = isset($mySchedules[$slotIdentifier]);
                        $isFull = $booked >= $demand;
                      @endphp
                      {{-- La lógica de la celda es la misma, pero el contenido cambia --}}
                      <td
                        class="slot @if ($isMine) slot-mine @elseif($isFull || $demand == 0) slot-full @else slot-available @endif">
                        @if ($demand > 0)
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input slot-checkbox" type="checkbox" name="slots[]"
                              value="{{ $slotIdentifier }}" id="slot-{{ $i }}-{{ $j }}"
                              @if ($isMine) checked @endif
                              @if (!$isMine && $isFull) disabled @endif>
                          </div>
                        @else
                          -
                        @endif
                      </td>
                    @endfor
                  </tr>
                @endfor
              </tbody>
            </table>
          </div>
          <div class="mt-4">
            <button type="submit" class="btn btn-primary">Guardar mi horario</button>
          </div>
        </form>
      </div>
    </div>
  @else
    <div class="alert alert-warning">
      No hay un forecast disponible para tu ciudad esta semana. Por favor, contacta con tu administrador.
    </div>
  @endisset
@endsection

{{-- NUEVA SECCIÓN DE SCRIPT --}}
@section('page-script')
  @vite('resources/assets/js/rider/schedule-picker.js')
@endsection
