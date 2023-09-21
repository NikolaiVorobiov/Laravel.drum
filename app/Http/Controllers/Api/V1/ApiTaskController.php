<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiTaskController extends Controller
{
    public function index(Request $request)
    {
        $statusName = $request->statusName ?? '';

        $priorityStart = $request->priorityStart ?? '';
        $priorityEnd = $request->priorityEnd ?? '';

        $orderField = $request->orderField ?? '';
        $orderDirection = $request->orderDirection ?? '';

        $title = $request->title ?? '';

        $query = Task::query();

        //TODO validator for all fields

        if ($statusName) {
            $query = $query->where('status', Task::STATUSES[$statusName]);
        }

        if ($priorityStart && $priorityEnd) {
            $range = range($priorityStart, $priorityEnd);
            $query = $query->whereIn('priority', $range);
        }

        if ($orderField && $orderDirection) {
            $query = $query->orderBy($orderField, $orderDirection);
        }

        if ($title) {
            $query = $query->where('title', 'LIKE', '%' . $title . '%');
        }

        $tasks = $query->get();
        return response()->json($tasks, Response::HTTP_OK);
    }


    public function store(TaskRequest $request)
    {
        $task = new Task();
        $this->_fill($request, $task);

        return response()->json([
            'message' => Response::$statusTexts[Response::HTTP_CREATED],
            'taskId' => $task->id
        ], Response::HTTP_CREATED);
    }

    public function update(TaskRequest $request, $taskId)
    {
        $task = Task::query()->where('id', $taskId)->where('user_id', $request->user->id)->first();

        if (!$task) {
            return response()->json([
                'message' => Response::$statusTexts[Response::HTTP_NOT_FOUND]
            ], Response::HTTP_NOT_FOUND);
        }

        $this->_fill($request, $task);

        return response()->json([
            'message' => Response::$statusTexts[Response::HTTP_OK],
            'taskId' => $taskId
        ], Response::HTTP_OK);
    }

    public function destroy(Request $request, $taskId)
    {
        $task = Task::query()->find($taskId);
        $userId = $task->user_id;

        if ($request->user->id === $userId && $task->status === 0) {
            Task::destroy($taskId);

            return response()->json([
                'message' => Response::$statusTexts[Response::HTTP_OK],
                'taskId' => $taskId
            ], Response::HTTP_OK);

        } else {
            return response()->json([
                'message' => Response::$statusTexts[Response::HTTP_BAD_REQUEST]
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function updateStatusToDone(Request $request, $taskId)
    {
        $query = Task::query();
        $userId = Task::find($taskId)->user_id ?? '';

        if ($userId && $request->user->id == $userId) {

            $query->where('id', $taskId)->update(['status' => 1]);

            return response()->json([
                'message' => Response::$statusTexts[Response::HTTP_OK],
                'taskId' => $taskId
            ], Response::HTTP_OK);

        } else {
            return response()->json([
                'message' => Response::$statusTexts[Response::HTTP_BAD_REQUEST]
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    private function _fill(TaskRequest $request,$task)
    {
        $validated = $request->validated();

        $task->user_id = $request->user->id;
        $task->status = $validated['status'];
        $task->priority = $validated['priority'];
        $task->title = $validated['title'];
        $task->description = $validated['description'];
        $task->createdAt = $validated['createdAt'];
        $task->completedAt = $validated['completedAt'] ?? null;

        $task->save();
    }

}
