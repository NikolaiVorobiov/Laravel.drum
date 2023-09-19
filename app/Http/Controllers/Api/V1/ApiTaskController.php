<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
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
            'message' => Response::$statusTexts[Response::HTTP_CREATED],
            'taskId' => $task->id
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, $taskId)
    {
        $validator = $this->_validate($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()],
                Response::HTTP_BAD_REQUEST);
        }

        $task = Task::find($taskId);

        $userId = Task::find($taskId)->user_id;
        $currentUserId = $this->_getCurrentUserId($request);

        if ($currentUserId == $userId) {
            $this->_fill($validator->validated(), $task);

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

    public function destroy(Request $request, $taskId)
    {
        $query= Task::query();

        $task = $query->find($taskId);
        $userId = $task->user_id;
        $currentUserId = $this->_getCurrentUserId($request);

        if ($currentUserId == $userId && $task->status == 0) {
            $query->destroy($taskId);

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

        $userId = Task::find($taskId)->user_id;
        $currentUserId = $this->_getCurrentUserId($request);

        if ($currentUserId == $userId) {

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

    private function _getCurrentUserId(Request $request)
    {
        $authorizationHeader = $request->header('Authorization');
        $token = Str::substr($authorizationHeader, 7);
        $users = User::all();

        foreach ($users as $user) {
            if ($user->token == $token) {
                $currentUserId = $user->id;
                break;
            }
        }
        return $currentUserId ?? '';
    }
}
