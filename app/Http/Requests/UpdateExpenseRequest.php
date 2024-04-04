<?php

namespace App\Http\Requests;

use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
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
        Validator::extend('unique_for_user', function ($attribute, $value) {
            $currentUser = Auth::user();
            $query = Expense::join('expense_categories', function ($join) {
                $join->on('expenses.category_id', 'expense_categories.id');
            })->where("expenses.".$attribute, $value)
                ->where("expenses.id", "!=", $this->route('expense')->id)
                ->where('expense_categories.user_id', '=', $currentUser->id);
    
            return !$query->count();
        });
        $required = $this->isMethod('patch') ? "" : "required|";
        $expense = $this->route('expense');
        return [
            'name' => $required.'string|max:100|unique_for_user',
            'value' => $required.'numeric|regex:/^\d+(\.\d{1,2})?$/',
            'description' => 'nullable|string|max:255',
            'notes' => 'string|max:511',
            'done' => $required.'boolean',
            'category_id' => $required.'exists:expense_categories,id'
        ];
    }
}
