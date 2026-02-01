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
            'city' => ['required', 'string', 'max:255'],

            'bio' => ['nullable', 'string', 'max:1000'],

            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            // Canada phone: 10 digits
            'phone' => [
                'required',
                'regex:/^\d{10}$/',
            ],

            // skills 不信前端
            'skills' => ['nullable'],

            // categories
            'category_ids' => ['required', 'array', 'min:1', 'max:10'],
            'category_ids.*' => ['integer', 'exists:categories,id'],

            // photos
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
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
