<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreZoneRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'width' => 'nullable|integer|min:0',
            'height' => 'nullable|integer|min:0',
            'web_id' => 'required|exists:webs,id',
        ];
    }
}
