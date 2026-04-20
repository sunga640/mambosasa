<?php

namespace App\Http\Requests\Admin;

use App\Enums\MaintenanceKind;
use App\Enums\MaintenanceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoomMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kind' => ['required', Rule::in(MaintenanceKind::values())],
            'description' => ['nullable', 'string', 'max:5000'],
            'expenses' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'started_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'status' => ['required', Rule::in(MaintenanceStatus::values())],
        ];
    }
}
