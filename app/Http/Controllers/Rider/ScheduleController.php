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
        $startOfWeek = Carbon::today()->startOfWeek();

        $forecast = Forecast::where('city', $rider->city)
            ->where('week_start_date', $startOfWeek)
            ->first();

        if (!$forecast) {
            return view('content.rider.schedule.index')->with('error', 'No hay un forecast disponible para tu ciudad esta semana.');
        }

        $bookedSchedules = Schedule::where('forecast_id', $forecast->id)
            ->select('slot_date', 'slot_time', DB::raw('count(*) as total'))
            ->groupBy('slot_date', 'slot_time')
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->slot_date)->format('Y-m-d') . '_' . $item->slot_time;
            });

        $mySchedules = Schedule::where('forecast_id', $forecast->id)
            ->where('rider_id', $rider->id)
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->slot_date)->format('Y-m-d') . '_' . $item->slot_time;
            });

        // Pasamos el recuento de horas ya seleccionadas a la vista
        $myHoursCount = count($mySchedules) * 0.5;

        return view('content.rider.schedule.index', compact('forecast', 'bookedSchedules', 'mySchedules', 'startOfWeek', 'rider', 'myHoursCount'));
    }

    public function store(Request $request)
    {
        $rider = Auth::guard('rider')->user();
        $startOfWeek = Carbon::today()->startOfWeek();

        $forecast = Forecast::where('city', $rider->city)
            ->where('week_start_date', $startOfWeek)
            ->firstOrFail();

        $selectedSlots = $request->input('slots', []);

        // REGLA DE NEGOCIO: Validar el límite de horas del contrato
        $selectedHours = count($selectedSlots) * 0.5; // Cada slot es 0.5 horas
        if ($rider->weekly_contract_hours > 0 && $selectedHours > $rider->weekly_contract_hours) {
             return redirect()->route('rider.schedule.index')
                ->with('error', 'Has seleccionado ' . $selectedHours . ' horas, superando tu límite de contrato de ' . $rider->weekly_contract_hours . ' horas.');
        }

        DB::beginTransaction();
        try {
            Schedule::where('rider_id', $rider->id)->where('forecast_id', $forecast->id)->delete();

            foreach ($selectedSlots as $slot) {
                [$date, $time] = explode('_', $slot);
                $dayKey = strtolower(Carbon::parse($date)->format('D'));
                $timeKey = Carbon::parse($time)->format('H:i');

                $demand = $forecast->forecast_data[$dayKey][$timeKey] ?? 0;
                $bookedCount = Schedule::where('forecast_id', $forecast->id)
                    ->where('slot_date', $date)
                    ->where('slot_time', $time)
                    ->count();

                if ($bookedCount >= $demand) {
                    throw new \Exception("El turno de las {$time} del día {$date} ya está completo. Alguien lo cogió mientras elegías.");
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
