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

    <form action="{{ route('rider.schedule.store') }}" method="POST">
      @csrf
      <div class="schedule-grid">
        @for ($i = 0; $i < 7; $i++)
          @php
            $currentDate = $startOfWeek->clone()->addDays($i);
          @endphp
          <div class="day-column">
            <div class="day-header">{{ $currentDate->translatedFormat('D d/m') }}</div>
            <div class="slots-container">
              @php
                $startTime = \Carbon\Carbon::createFromTimeString('00:00');
              @endphp
              @for ($j = 0; $j < 48; $j++)
                @php
                  $currentTime = $startTime->clone()->addMinutes($j * 30);
                  $dayKey = strtolower($currentDate->format('D'));
                  $timeKey = $currentTime->format('H:i');
                  $slotIdentifier = $currentDate->format('Y-m-d') . '_' . $currentTime->format('H:i:s');

                  // --- Lógica de Estado (Refinada) ---
                  // 1. Demanda: ¿Cuántos riders se necesitan según el forecast?
                  $demand = $forecast->forecast_data[$dayKey][$timeKey] ?? 0;
                  // 2. Ocupados: ¿Cuántos riders ya han cogido este turno?
                  $booked = $bookedSchedules[$slotIdentifier]['total'] ?? 0;
                  // 3. ¿Lo tengo yo?: ¿Este turno está en mi horario actual?
                  $isMine = isset($mySchedules[$slotIdentifier]);
                  // 4. ¿Está lleno?: ¿Ya no quedan huecos disponibles?
                  $isFull = $booked >= $demand;
                  // 5. ¿Está disponible?: Solo si hay demanda y no está lleno.
                  $isAvailable = $demand > 0 && !$isFull;
                @endphp

                {{-- Renderizamos el bloque de hora solo si hay demanda para ese turno --}}
                @if ($demand > 0)
                  <label
                    class="slot-item @if ($isMine) selected @elseif(!$isAvailable) unavailable @else available @endif">
                    <input class="slot-checkbox" type="checkbox" name="slots[]" value="{{ $slotIdentifier }}"
                      @if ($isMine) checked @endif {{-- Deshabilitamos si no es mío Y no está disponible --}}
                      @if (!$isMine && !$isAvailable) disabled @endif>
                    <span>{{ $currentTime->format('H:i') }}</span>
                  </label>
                @endif
              @endfor
            </div>
          </div>
        @endfor
      </div>

      <div class="card mt-4">
        <div class="card-body text-center">
          <button type="submit" class="btn btn-primary btn-lg">Guardar mi horario</button>
        </div>
      </div>
    </form>
  @else
    {{-- ... (código de "no hay forecast") ... --}}
  @endisset
@endsection

@section('page-script')
  @vite('resources/assets/js/schedule-picker.js')
@endsection
