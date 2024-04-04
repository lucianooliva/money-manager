<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Http\Resources\ExpenseCategoryResource;
use App\Http\Requests\StoreExpenseCategoryRequest;
use App\Http\Requests\UpdateExpenseCategoryRequest;
use App\Http\Requests\DestroyMultipleExpenseCategoriesRequest;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request) {
        $user = $request->user();
        $result = ExpenseCategory::where("user_id", $user->id)->with('expenses')->get();
        return ExpenseCategoryResource::collection($result);
    }
    public function store(StoreExpenseCategoryRequest $request, ExpenseCategory $category) {
        $data = $request->validated();
        $result = $category::create($data);
        return new ExpenseCategoryResource($result);
    }
    public function show(ExpenseCategory $expenseCategory, Request $request) {
        $user = $request->user();
        if ($user->id !== $expenseCategory->user_id) {
            return abort(403, "Unauthorized action.");
        }
        return new ExpenseCategoryResource($expenseCategory);
    }
    public function showWithExpenses($expenseCategoryId, Request $request) {
        $user = $request->user();
        $expenseCategory = ExpenseCategory::findOrFail($expenseCategoryId);
        if ($user->id !== $expenseCategory->user_id) {
            return abort(403, "Unauthorized action.");
        }
        $result = ExpenseCategory::with('expenses')->findOrFail($expenseCategoryId);
        return new ExpenseCategoryResource($result);
    }
    public function update(UpdateExpenseCategoryRequest $request, $id) {
        $expenseCategory = ExpenseCategory::findOrFail($id);
        $result = $expenseCategory->update($request->validated());
        return new ExpenseCategoryResource($expenseCategory);
    }
    public function destroy(ExpenseCategory $expenseCategory, Request $request) {
        $user = $request->user();
        if ($user->id !== $expenseCategory->user_id) {
            return abort(403, "Unauthorized action.");
        }
        $expenseCategory->delete();
        return response('', 204);
    }
    public function destroyMultiple(DestroyMultipleExpenseCategoriesRequest $request) {
        $user = $request->user();
        $ids = $request->input('ids');
        $categories = ExpenseCategory::whereIn('id', $ids)->get();
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
        ExpenseCategory::whereIn('id', $ids)->delete();
        return response('', 204);
    }
}
