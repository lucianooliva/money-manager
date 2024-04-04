<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'user_id'
    ];
    
    public function expenses() {
        return $this->hasMany(Expense::class, "category_id", "id");
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
