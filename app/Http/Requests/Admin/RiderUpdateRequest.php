<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RiderUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Usamos la política para verificar si el usuario puede actualizar este rider específico
        $rider = $this->route('rider');
        return $this->user()->can('update', $rider);
    }

    public function rules(): array
    {
        $riderId = $this->route('rider')->id;

        return [
            'full_name' => 'required|string|max:255',
            'dni' => ['required', 'string', Rule::unique('riders')->ignore($riderId)],
            'email' => ['required', 'email', Rule::unique('riders')->ignore($riderId)],
            'city' => 'required|string|max:255',
            'password' => 'nullable|string|min:8', // Opcional al editar
            'status' => 'required|in:active,inactive,blocked',
        ];
    }
}
