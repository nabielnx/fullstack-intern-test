<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

    public function export(Request $request)
    {
        $query = Todo::query();

        //filter title
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->query('title') . '%');
        }

        //filter assignee
        if ($request->filled('assignee')) {
            $names = array_map('trim', explode(',', $request->query('assignee')));
            $query->whereIn('assignee', $names);
        }

        //filter due date
        if ($request->filled('start')) {
            $query->where('due_date', '>=', $request->query('start'));
        }
        if ($request->filled('end')) {
            $query->where('due_date', '<=', $request->query('end'));
        }

        //filter time tracked
        if ($request->filled('min')) {
            $query->where('time_tracked', '>=', $request->query('min'));
        }
        if ($request->filled('max')) {
            $query->where('time_tracked', '<=', $request->query('max'));
        }

        //filter status
        if ($request->filled('status')) {
            $statuses = array_map('trim', explode(',', $request->query('status')));
            $query->whereIn('status', $statuses);
        }
        
        //filter priority
        if ($request->filled('priority')) {
            $priorities = array_map('trim', explode(',', $request->query('priority')));
            $query->whereIn('priority', $priorities);
        }

        $todos = $query->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray(
            ['Title', 'Assignee', 'Due Date', 'Time Tracked', 'Status', 'Priority'],
            null,
            'A1'
        );

        $row = 2;
        foreach ($todos as $todo) {
            $sheet->fromArray([
                $todo->title,
                $todo->assignee,
                $todo->due_date,
                $todo->time_tracked,
                $todo->status,
                $todo->priority,
            ], null, "A{$row}");

            $row++;
        }

        $sheet->setCellValue("A{$row}", 'Total Data');
        $sheet->setCellValue("B{$row}", $todos->count());
        $sheet->setCellValue("C{$row}", 'Total Waktu');
        $sheet->setCellValue("D{$row}", $todos->sum('time_tracked'));

        return response()->streamDownload(
            fn () => (new Xlsx($spreadsheet))->save('php://output'),
            'todos.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }
}
