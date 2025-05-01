<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Traits\Response;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    use Response;

    public function __construct(protected CacheService $cacheService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Get pagination size from request, default to 10, with max limit 100
            $perPage = $request->input('per_page', 10);
            $perPage =  $perPage > 100 || $perPage < 1 ? 10 : $perPage;
            // Define available sort orders for tasks
            $sortOrder = $request->input('sort_order', 'id_desc');
            $keyword = $request->input('keyword', null);
            $statusId = $request->input('status_id', null);

            $arrSort = [
                'id_desc' => ['id', 'desc'],
                'id_asc' => ['id', 'asc'],
                "name_desc" => ["name", 'desc'],
                "name_asc" => ["name", 'asc'],
                'status_desc' => ['status_id', 'desc'],
                'status_asc' => ['status_id', 'asc'],
            ];

            // Generate a unique cache key based on request filters
            $cacheKey = 'tasks_' . md5(serialize($request->all()));

            $this->cacheService->addKeyToCacheList('tasks', $cacheKey);

            // Attempt to retrieve from cache, otherwise query database and cache result
            $tasks = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request, $perPage, $sortOrder, $keyword, $arrSort, $statusId) {

                $query = Task::with('status');

                // Apply keyword filtering (search by name or description)
                if ($keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('name', 'like', '%' . $keyword . '%')
                            ->orWhere('description', 'like', '%' . $keyword . '%');
                    });
                }

                // Apply status filter if provided
                if ($statusId) {
                    $query->where('status_id', $statusId);
                }

                if (array_key_exists($sortOrder, $arrSort)) {
                    [$field, $direction] = $arrSort[$sortOrder];
                    $query->orderBy($field, $direction);
                } else {
                    $query->orderBy('id', 'desc');
                }

                return $request->has('list') ? $query->get() : $query->paginate($perPage);
            });

            return $this->sendRes(true, 'Tasks retrieved successfully', TaskResource::collection($tasks));

        } catch (\Exception $e) {
            // Log error and return server error response
            Log::error('Task index error: ' . $e->getMessage());
            return $this->sendRes(false, 'Failed to retrieve tasks', null, null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        try {

            Task::create($request->validated());

            // Clear task cache after creating a task
            $this->cacheService->clearItemCache('tasks');

            return $this->sendRes(true, 'Task created successfully', null, null, 201);

        } catch (\Exception $e) {
            // Log error and return server error response
            Log::error('Task store error: ' . $e->getMessage());
            return $this->sendRes(false, 'Failed to create task', null, null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $task->load('status');
       return $this->sendRes(true, 'Task retrieved successfully', new TaskResource($task)); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        try {
            $task->update($request->validated());

            // Clear task cache after updating a task
            $this->cacheService->clearItemCache('tasks');

            $task->load('status');

            return $this->sendRes(true, 'Task updated successfully', new TaskResource($task));

        } catch (\Exception $e) {
            // Log error and return server error response
            Log::error('Task update error: ' . $e->getMessage());
            return $this->sendRes(false, 'Failed to update task', null, null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        try {
            $task->delete();

            // Clear task cache after deleting a task
            $this->cacheService->clearItemCache('tasks');

            return $this->sendRes(true, 'Task deleted successfully', null);
        } catch (\Exception $e) {
            // Log error and return server error response
            Log::error('Task delete error: ' . $e->getMessage());
            return $this->sendRes(false, 'Failed to delete task', null, null, 500);
        }
    }
}
