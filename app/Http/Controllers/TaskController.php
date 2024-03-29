<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Get all active data of a specific user token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $tasks = Task::where('user_uuid', $request->get('userToken'))
            ->where('is_expired', false)
            ->get();

        return response()->json([
            'tasks' => $tasks
        ]);
    }

    /**
     * Store a new task
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'task_name' => 'required|max:255'
        ]);

        if ($validated->fails()) {
            return response()->json([
                'error' => $validated->errors()->first()
            ], 400);
        }

        $task = Task::create([
            'user_uuid' => $request->get('userToken'),
            'task_name' => $request->get('task_name')
        ]);

        // Refresh task model to get all data
        $task->refresh();

        return response()->json([
            'message' => 'Successfully added task',
            'task' => $task
        ]);
    }

    /**
     * Save/ update all task status
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $userToken = $request->get('userToken');
        $tasks = collect($request->get('tasks'));
        $taskIds = [];

        // Add not default values to array
        $tasks = $tasks->map(function ($t) use ($userToken) {
            $taskIds[] = $t['id'];

            $task['id'] = $t['id'];
            $task['user_uuid'] = $userToken;
            $task['task_name'] = array_key_exists('task_name', $t) ? $t['task_name'] : "";
            $task['is_complete'] = (isset($t['is_complete']) && $t['is_complete']) ? 1 : 0;
            $task['created_at'] = now();
            $task['updated_at'] = now();

            return $task;
        });

        // Remove tasks that aren't in the list
        Task::where('user_uuid', $userToken)->whereNotIn('id', $taskIds)->delete();

        // Update only is_complete value
        try {
            Task::where('user_uuid', $userToken)->upsert($tasks->toArray(), ['id'], ['is_complete']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Some error occurred, please try later'
            ]);
        }

        return response()->json([
            'message' => 'All tasks updated successfully'
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear(Request $request)
    {
        $userToken = $request->get('userToken');

        Task::where('user_uuid', $userToken)->delete();

        return response()->json([
            'message' => 'All tasks deleted successfully'
        ]);
    }
}
