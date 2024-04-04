<?php

namespace App\Http\Requests;

use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation() {
        $this->merge([
            'user_id' => $this->user()->id
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        Validator::extend('unique_for_user', function ($attribute, $value) {
            $currentUser = Auth::user();
            $query = Expense::join('expense_categories', function ($join) {
                $join->on('expenses.category_id', 'expense_categories.id');
            })->where("expenses.".$attribute, $value)->where('expense_categories.user_id', '=', $currentUser->id);
    
            return !$query->count();
        });
        return [
            'name' => 'required|string|max:100|unique_for_user',
            'value' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'description' => 'nullable|string|max:255',
            'notes' => 'string|max:511',
            'done' => 'required|boolean',
            'category_id' => 'required|exists:expense_categories,id'
        ];
    }
}
