<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return true;
        }

        // Security check for manager
        $clubIds = $user->managedClubs->pluck('id')->toArray();
        return in_array($this->input('club_id'), $clubIds);
    }

    public function rules(): array
    {
        $rules = [
            'club_id' => 'required|exists:clubs,id',
            'category_id' => 'required|exists:event_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ];

        if (Auth::user()->isAdmin()) {
            $rules['status'] = 'required|in:pending,approved,rejected';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'end_time.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
        ];
    }
}
