<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\Request;
use App\Http\Resources\IncomeResource;
use App\Http\Requests\StoreIncomeRequest;
use App\Http\Requests\UpdateIncomeRequest;
use App\Http\Requests\DestroyMultipleIncomesRequest;

class IncomeController extends Controller
{
    public function index(Request $request) {
        $user = $request->user();
        $result = Income::where("user_id", $user->id)->get();
        $total = number_format( $result->sum('value'), 2, '.', '' );
        $collection = IncomeResource::collection($result);
        return response()->json(["total" => $total, "list" => $collection]);
    }
    public function store(StoreIncomeRequest $request, Income $income) {
        $data = $request->validated();
        $result = $income::create($data);
        return new IncomeResource($result);
    }
    public function show(Income $income, Request $request) {
        $user = $request->user();
        if ($user->id !== $income->user_id) {
            return abort(403, "Unauthorized action.");
        }
        return new IncomeResource($income);
    }
    public function update(UpdateIncomeRequest $request, $id) {
        $income = Income::findOrFail($id);
        $income->update($request->validated());
        return new IncomeResource($income);
    }
    public function destroy(Income $income, Request $request) {
        $user = $request->user();
        if ($user->id !== $income->user_id) {
            return abort(403, "Unauthorized action.");
        }
        $income->delete();
        return response('', 204);
    }
    public function destroyMultiple(DestroyMultipleIncomesRequest $request) {
        $user = $request->user();
        $ids = $request->input('ids');
        $incomes = Income::whereIn('id', $ids)->get();
        $incomeUserId = $incomes->first()['user_id'];
        $sameUser = $incomes->every(function ($item) use ($incomeUserId) {
            return $item['user_id'] === $incomeUserId;
        });
        if (!$sameUser) {
            return abort(400, "Deleting incomes from multiple users is not possible.");
        }
        if ($user->id !== $incomeUserId) {
            return abort(403, "Unauthorized action.");
        }
        Income::whereIn('id', $ids)->delete();
        return response('', 204);
    }

}
