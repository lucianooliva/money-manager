<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $totalDone = $this->whenLoaded('expenses', function() {
            $sum = $this->expenses->where('done', true)->sum('value');
            return number_format($sum, 2, '.', '');
        });
        $totalTodo = $this->whenLoaded('expenses', function() {
            $sum = $this->expenses->where('done', false)->sum('value');
            return number_format($sum, 2, '.', '');
        });
        $total = number_format( $this->expenses->sum('value'), 2, '.', '' );
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "icon" => $this->icon,
            "expenses" => $this->whenLoaded('expenses'),
            "totalDone" => $totalDone,
            "total" => $total,
            "totalTodo" => $totalTodo,
        ];
    }
}
