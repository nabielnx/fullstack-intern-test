<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'assignee' => 'nullable|string|max:255',
            'due_date' => 'required|date|after_or_equal:today',
            'time_tracked' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:pending,open,in_progress,completed',
            'priority' => 'required|in:low,medium,high',
        ]);

        $todo = Todo::create($data);

        return response()->json($todo, 201);
    }
}
