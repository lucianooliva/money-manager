<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'value',
        'description',
        'notes',
        'done',
        'category_id'
    ];
    protected $casts = [
        'done' => 'boolean',
        'category_id' => 'integer'
    ];

    public function expenseCategory() {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }
}
