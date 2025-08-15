<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rider;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables; // Importante para la tabla

class RiderController extends Controller
{
    /**
     * Muestra la lista de riders.
     */
    public function index(Request $request)
    {
        // El método authorize se asegura de que el usuario tenga permiso según la política
        $this->authorize('viewAny', Rider::class);

        if ($request->ajax()) {
            $data = Rider::select(['id', 'full_name', 'dni', 'city', 'status', 'email']);
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    // Aquí puedes añadir botones de acción (ver, editar, eliminar)
                    $editUrl = route('admin.riders.edit', $row->id);
                    return '<a href="' . $editUrl . '" class="btn btn-sm btn-icon item-edit"><i class="ti ti-pencil"></i></a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('content.admin.riders.index');
    }

    // NOTA: Para un MVP completo, aquí añadirías los métodos
    // create(), store(), show(), edit(), update() y destroy().
    // Por ahora nos centramos en el listado.
}
