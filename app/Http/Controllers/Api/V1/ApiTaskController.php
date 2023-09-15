<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ApiTaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return response()->json($tasks, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = $this->_validate($request); // TODO check invalid data

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()],
                Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $task = new Task();
        $this->_fill($validator->validated(), $task);

        return response()->json([
            'message' => Response::$statusTexts[Response::HTTP_CREATED], // TODO analogs for other methods
            'taskId' => $task->id
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, $taskId)
    {
        $validator = $this->_validate($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()],
                Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $task = Task::find($taskId);

        $this->_fill($validator->validated(), $task);

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
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'status' => 'required|boolean',
            'priority' => 'required|in:1,2,3,4,5',
            'title' => 'required|string',
            'description' => 'required|string',
            'createdAt' => 'required|date',
            'completedAt' => 'nullable|date',
        ]);
        return $validator;
    }

    private function _fill($validated, $task)
    {
        $task->user_id = $validated['user_id'];
        $task->status = $validated['status'];
        $task->priority = $validated['priority'];
        $task->title = $validated['title'];
        $task->description = $validated['description'];
        $task->createdAt = $validated['createdAt'];
        $task->completedAt = $validated['completedAt'] ?? null;

        $task->save();
    }
}
