<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTransactionRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'description' => 'required|string|max:255',
            'value' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'type' => ['required', Rule::in(['income', 'expense'])],
            'is_paid' => 'required|boolean',
            'account_id' => ['required', Rule::exists('accounts', 'id')->where('user_id', $this->user()->id)],
            'category_id' => ['required', Rule::exists('categories', 'id')->where('user_id', $this->user()->id)],
            'tag_id' => ['nullable', Rule::exists('tags', 'id')->where('user_id', $this->user()->id)],
            'notes' => 'nullable|string',
            'is_fixed' => 'boolean',
            'is_recurring' => 'boolean',
            'installments' => 'nullable|integer|min:1',
            'installment_period' => ['nullable', Rule::in(['days', 'weeks', 'months', 'years'])],
        ];
    }
}
