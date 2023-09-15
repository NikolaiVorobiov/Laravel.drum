<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiTaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return response()->json($tasks, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $this->_validate($request); // TODO check invalid data

//        $validated = $this->_validate($request);
//        if (!$validated) {
//            // Обработка ошибок валидации
//            return response()->json(['errors' => 'error'], 400);
//        }

        $task = new Task();
        $this->_fill($request, $task);

        return response()->json([
            'message' => Response::$statusTexts[Response::HTTP_CREATED], // TODO analogs for other methods
            'taskId' => $task->id
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, $taskId)
    {
        $task = Task::find($taskId);
        $this->_validate($request);
        $this->_fill($request, $task);

        return response()->json([
            'message' => Response::$statusTexts[Response::HTTP_OK],
            'taskId' => $taskId
        ], Response::HTTP_OK);
    }

    public function destroy($taskId)
    {
       Task::destroy($taskId);

        return response()->json([
            'message' => Response::$statusTexts[Response::HTTP_OK],
            'taskId' => $taskId
        ], Response::HTTP_OK);
    }

    public function updateStatusToDone($taskId)
    {
        Task::query()->where('id', $taskId)->update(['status' => 1]);

        return response()->json([
            'message' => Response::$statusTexts[Response::HTTP_OK],
            'taskId' => $taskId
        ], Response::HTTP_OK);
    }




    private function _validate(Request $request)
    {
       $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|boolean',
            'priority' => 'required|in:1,2,3,4,5',
            'title' => 'required|string',
            'description' => 'required|string',
            'createdAt' => 'required|date',
            'completedAt' => 'nullable|date',
        ],[
            'user_id.required' => 'The :attribute field is required.',
            'user_id.exists' => 'The :attribute must exist in the database.',
            'status.required' => 'The :attribute field is required.',
            'status.boolean' => 'The :attribute must be a boolean (true or false) value.',
            'priority.required' => 'The :attribute field is required.',
            'priority.in' => 'The :attribute must be one of the allowed values (1, 2, 3, 4, 5).',
            'title.required' => 'The :attribute field is required.',
            'title.string' => 'The :attribute must be a string.',
            'description.required' => 'The :attribute field is required.',
            'description.string' => 'The :attribute must be a string.',
            'createdAt.required' => 'The :attribute field is required.',
            'createdAt.date' => 'The :attribute must be a valid date.',
            'completedAt.date' => 'The :attribute must be a valid date if it is present.',
        ]);
    }

    private function _fill(Request $request, $task)
    {
        $task->user_id = $request->input('user_id');
        $task->status = $request->input('status');
        $task->priority = $request->input('priority');
        $task->title = $request->input('title');
        $task->description = $request->input('description');
        $task->createdAt = $request->input('createdAt');
        $task->completedAt = $request->input('completedAt');

        $task->save();
    }
}
