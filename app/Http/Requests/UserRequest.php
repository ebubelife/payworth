<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [

            'name' => 'required|string|max:255',
            'email' => 'required|string|email|',
            
        ];
    }

     public function messages():array
    {
        return [
            'email.unique' => 'The email address is already in use.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }

    public function response(array $errors)
    {
        return response()->json(['errors' => $errors], 422);
    }
}
