<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ExpenseResource;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Requests\UpdateExpenseFieldRequest;
use App\Http\Requests\DestroyMultipleExpensesRequest;
use App\Http\Requests\UpdateDoneStatusMultipleRequest;

class ExpenseController extends Controller
{
    public function index() {
        $result = Expense::get();
        return ExpenseResource::collection($result);
    }
    public function store(StoreExpenseRequest $request, Expense $category) {
        $data = $request->validated();
        $result = $category::create($data);
        return new ExpenseResource($result);
    }
    public function show(Expense $expense, Request $request) {
        $user = $request->user();
        $category = ExpenseCategory::findOrFail($expense->category_id);
        if ($user->id !== $category->user_id) {
            return abort(403, "Unauthorized action.");
        }
        return new ExpenseResource($expense);
    }
    public function getByCategory(Request $request, $categoryId) {
        $user = $request->user();
        $category = ExpenseCategory::findOrFail($categoryId);
        if ($user->id !== $category->user_id) {
            return abort(403, "Unauthorized action.");
        }
        $expenses = Expense::where("category_id", $categoryId)->get();
        return ExpenseResource::collection($expenses);
    }
    public function update(UpdateExpenseRequest $request, Expense $expense) {
        $expense->update($request->validated());
        return new ExpenseResource($expense);
    }
    public function destroy(Expense $expense, Request $request) {
        $user = $request->user();
        $category = ExpenseCategory::findOrFail($expense->category_id);
        if ($user->id !== $category->user_id) {
            return abort(403, "Unauthorized action.");
        }
        $expense->delete();
        return response('', 204);
    }
    public function destroyMultiple(DestroyMultipleExpensesRequest $request) {
        $user = $request->user();
        $ids = $request->input('ids');
        $expenses = Expense::whereIn('id', $ids)->get();
        $categoryIds = $expenses->pluck('category_id');
        $categories = ExpenseCategory::whereIn('id', $categoryIds)->get();
        $catUserId = $categories->first()['user_id'];
        $sameUser = $categories->every(function ($item) use ($catUserId) {
            return $item['user_id'] === $catUserId;
        });
        if (!$sameUser) {
            return abort(400, "Deleting expenses from multiple users is not possible.");
        }
        if ($user->id !== $catUserId) {
            return abort(403, "Unauthorized action.");
        }
        Expense::whereIn('id', $ids)->delete();
        return response('', 204);
    }
    public function updateDoneStatusMultiple(UpdateDoneStatusMultipleRequest $request) {
        $user = $request->user();
        $ids = $request->input('ids');
        $done = $request->input('done');
        $expenses = Expense::whereIn('id', $ids)->get();
        $categoryIds = $expenses->pluck('category_id');
        $categories = ExpenseCategory::whereIn('id', $categoryIds)->get();
        $catUserId = $categories->first()['user_id'];
        $sameUser = $categories->every(function ($item) use ($catUserId) {
            return $item['user_id'] === $catUserId;
        });
        if (!$sameUser) {
            return abort(400, "Deleting expenses from multiple users is not possible.");
        }
        if ($user->id !== $catUserId) {
            return abort(403, "Unauthorized action.");
        }
        Expense::whereIn('id', $ids)->update(['done' => $done]);
        return ExpenseResource::collection($expenses);
    }
}
