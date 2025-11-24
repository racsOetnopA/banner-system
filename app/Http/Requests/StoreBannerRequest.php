<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBannerRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'name' => 'required|string|max:150',
            'type' => 'required|in:image,html,video',
            'image' => 'nullable|required_if:type,image|image|max:4096',
            'video' => 'nullable|required_if:type,video|mimes:mp4,webm,ogg|max:51200', // 50MB
            'html_code' => 'nullable|required_if:type,html|string',
            'link_url' => 'nullable|url',
            'active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'zones' => 'required|array|min:1',
            'zones.*' => 'exists:zones,id',
            'principal' => 'boolean',
        ];
    }

}
