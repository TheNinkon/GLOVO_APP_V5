<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use App\Models\Forecast;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index()
    {
        $rider = Auth::guard('rider')->user();
        $today = Carbon::today();
        $startOfWeek = $today->startOfWeek();

        // 1. Buscar el forecast para la ciudad del rider y la semana actual
        $forecast = Forecast::where('city', $rider->city)
            ->where('week_start_date', $startOfWeek)
            ->first();

        if (!$forecast) {
            // Si no hay forecast, mostramos un mensaje
            return view('content.rider.schedule.index')->with('error', 'No hay un forecast disponible para tu ciudad esta semana.');
        }

        // 2. Obtener todos los turnos ya reservados para este forecast
        $bookedSchedules = Schedule::where('forecast_id', $forecast->id)
            ->select('slot_date', 'slot_time', DB::raw('count(*) as total'))
            ->groupBy('slot_date', 'slot_time')
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->slot_date)->format('Y-m-d') . '_' . $item->slot_time;
            });

        // 3. Obtener los turnos que YO he reservado
        $mySchedules = Schedule::where('forecast_id', $forecast->id)
            ->where('rider_id', $rider->id)
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->slot_date)->format('Y-m-d') . '_' . $item->slot_time;
            });

        return view('content.rider.schedule.index', compact('forecast', 'bookedSchedules', 'mySchedules', 'startOfWeek'));
    }

    public function store(Request $request)
    {
        $rider = Auth::guard('rider')->user();
        $today = Carbon::today();
        $startOfWeek = $today->startOfWeek();

        $forecast = Forecast::where('city', $rider->city)
            ->where('week_start_date', $startOfWeek)
            ->firstOrFail();

        $selectedSlots = $request->input('slots', []);

        DB::beginTransaction();
        try {
            // Primero, borramos las selecciones previas de esta semana para este rider
            Schedule::where('rider_id', $rider->id)->where('forecast_id', $forecast->id)->delete();

            // Ahora, intentamos insertar las nuevas
            foreach ($selectedSlots as $slot) {
                [$date, $time] = explode('_', $slot);
                $dayKey = strtolower(Carbon::parse($date)->format('D')); // 'mon', 'tue', etc.
                $timeKey = Carbon::parse($time)->format('H:i');

                // Validación CRÍTICA: Asegurarnos de que hay huecos
                $demand = $forecast->forecast_data[$dayKey][$timeKey] ?? 0;
                $bookedCount = Schedule::where('forecast_id', $forecast->id)
                    ->where('slot_date', $date)
                    ->where('slot_time', $time)
                    ->count();

                if ($bookedCount >= $demand) {
                    throw new \Exception("El turno de las {$time} del día {$date} ya está completo.");
                }

                Schedule::create([
                    'rider_id' => $rider->id,
                    'forecast_id' => $forecast->id,
                    'slot_date' => $date,
                    'slot_time' => $time,
                ]);
            }

            DB::commit();
            return redirect()->route('rider.schedule.index')->with('success', 'Tu horario se ha guardado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('rider.schedule.index')->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }
}
