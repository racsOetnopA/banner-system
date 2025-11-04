<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'banner_id' => 'required|exists:banners,id',
            'zone_id' => 'required|exists:zones,id',
            'site_domain' => 'required|string|max:190',
            'rotation_mode' => 'required|in:random,sequential',
            'weight' => 'required|integer|min:1|max:100',
        ];
    }
}
