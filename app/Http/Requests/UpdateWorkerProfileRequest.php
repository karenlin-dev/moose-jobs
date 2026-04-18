<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkerProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'city' => ['sometimes', 'string', 'max:255'],

            'phone' => [
                'sometimes',
                'regex:/^\d{10}$/',
            ],

            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => [
                'file',
                'mimetypes:image/jpeg,image/png,image/webp,video/mp4',
                'max:51200',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Phone number is required.',
            'phone.regex' => 'Phone number must be 10 digits (numbers only).',

            'category_ids.required' => 'Please select at least one category.',
            'category_ids.min' => 'Please select at least one category.',

            'photos.max' => 'You can upload up to 10 photos only.',
            'photos.*.image' => 'Each file must be an image.',
            'photos.*.mimes' => 'Images must be JPG, PNG, or WEBP.',
            'photos.*.max' => 'Each image must be under 5MB.',
        ];
    }       

}
