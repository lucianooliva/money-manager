<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseFieldRequest extends FormRequest
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
        $expenseId = $this->route('expense');
        return [
            'name' => 'string|max:100|unique:expenses,name,'.$expenseId,
            'value' => 'numeric|regex:/^\d+(\.\d{1,2})?$/',
            'description' => 'nullable|string|max:255',
            'notes' => 'string|max:511',
            'done' => 'boolean',
            'category_id' => 'exists:expense_categories,id'
        ];
    }
}
