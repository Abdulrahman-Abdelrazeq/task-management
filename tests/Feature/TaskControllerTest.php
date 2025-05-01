<?php

namespace Tests\Feature;

use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The authenticated user for tests.
     *
     * @var User
     */
    protected $user;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Run StatusSeeder to populate statuses
        Artisan::call('db:seed', ['--class' => 'StatusSeeder']);

        // Create and authenticate a user
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);
    }

    /**
     * Test that an authenticated user can retrieve paginated tasks.
     *
     * @return void
     */
    public function test_index_retrieves_paginated_tasks()
    {
        // Arrange: Create 15 tasks
        Task::factory()->count(15)->create(['status_id' => Status::where('name', 'Pending')->first()->id]);

        // Act: Request tasks with pagination
        $response = $this->getJson('/api/tasks?per_page=10');

        // Assert: Verify response structure and pagination
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Tasks retrieved successfully',
                 ])
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => [
                         '*' => [
                             'id',
                             'name',
                             'description',
                             'status' => ['id', 'name'],
                             'created_at',
                             'updated_at',
                         ],
                     ],
                     'pagination' => [
                         'current_page',
                         'last_page',
                         'per_page',
                         'total',
                         'next_page_url',
                         'prev_page_url',
                     ],
                 ])
                 ->assertJsonCount(10, 'data') // 10 tasks per page
                 ->assertJsonPath('pagination.per_page', 10)
                 ->assertJsonPath('pagination.current_page', 1)
                 ->assertJsonPath('pagination.total', 15);
    }

    /**
     * Test filtering tasks by status_id in index.
     *
     * @return void
     */
    public function test_index_filters_by_status_id()
    {
        // Arrange: Create tasks with different statuses
        $pendingStatus = Status::where('name', 'Pending')->first();
        $inProgressStatus = Status::where('name', 'In_progress')->first();
        Task::factory()->create(['status_id' => $pendingStatus->id, 'name' => 'Pending Task']);
        Task::factory()->create(['status_id' => $inProgressStatus->id, 'name' => 'In Progress Task']);

        // Act: Filter by pending status_id
        $response = $this->getJson("/api/tasks?status_id={$pendingStatus->id}");

        // Assert: Only tasks with pending status are returned
        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonFragment(['name' => 'Pending Task'])
                 ->assertJsonMissing(['name' => 'In Progress Task']);
    }

     /**
     * Test searching tasks by keyword in index.
     *
     * @return void
     */
    public function test_index_searches_by_keyword()
    {
        // Arrange: Create tasks with specific names and descriptions
        Task::factory()->create([
            'name' => 'Important Task',
            'description' => 'Urgent task',
            'status_id' => Status::where('name', 'Pending')->first()->id
        ]);
        Task::factory()->create([
            'name' => 'Regular Task',
            'description' => 'Normal task',
            'status_id' => Status::where('name', 'Pending')->first()->id
        ]);

        // Act: Search for tasks with keyword "Urgent"
        $response = $this->getJson('/api/tasks?keyword=Urgent');

        // Assert: Only tasks matching the keyword are returned
        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonFragment(['description' => 'Urgent task']);
    }

     /**
     * Test sorting tasks by name in ascending order in index.
     *
     * @return void
     */
    public function test_index_sorts_by_name_ascending()
    {
        // Arrange: Create tasks with different names
        Task::factory()->create([
            'name' => 'Zebra Task',
            'status_id' => Status::where('name', 'Pending')->first()->id
        ]);
        Task::factory()->create([
            'name' => 'Apple Task',
            'status_id' => Status::where('name', 'Pending')->first()->id
        ]);

        // Act: Sort by name_asc
        $response = $this->getJson('/api/tasks?sort_order=name_asc');

        // Assert: Tasks are sorted alphabetically
        $response->assertStatus(200)
                 ->assertJsonPath('data.0.name', 'Apple Task')
                 ->assertJsonPath('data.1.name', 'Zebra Task');
    }

     /**
     * Test handling invalid per_page in index.
     *
     * @return void
     */
    public function test_index_handles_invalid_per_page()
    {
        // Arrange: Create tasks
        Task::factory()->count(15)->create(['status_id' => Status::where('name', 'Pending')->first()->id]);

        // Act: Request with invalid per_page
        $response = $this->getJson('/api/tasks?per_page=invalid');

        // Assert: Defaults to 10 tasks per page
        $response->assertStatus(200)
                 ->assertJsonCount(10, 'data');
    }

    /**
     * Test retrieving all tasks with list parameter in index.
     *
     * @return void
     */
    public function test_index_retrieves_all_tasks_with_list_parameter()
    {
        // Arrange: Create 15 tasks
        Task::factory()->count(15)->create(['status_id' => Status::where('name', 'Pending')->first()->id]);

        // Act: Request all tasks with list parameter
        $response = $this->getJson('/api/tasks?list=1');

        // Assert: Returns all tasks without pagination
        $response->assertStatus(200)
                 ->assertJsonCount(15, 'data')
                 ->assertJsonMissing(['pagination']);
    }

    /**
     * Test cache usage in index for repeated requests.
     *
     * @return void
     */
    public function test_index_uses_cache_for_repeated_requests()
    {
        // Arrange: Create tasks and clear cache
        Task::factory()->count(5)->create(['status_id' => Status::where('name', 'Pending')->first()->id]);
        Cache::flush();

        // Act: First request to populate cache
        $response = $this->getJson('/api/tasks?per_page=5');
        $cacheKey = 'tasks_' . md5(serialize(['per_page' => '5']));
        $cachedData = Cache::get($cacheKey);

        // Assert: Cache contains data
        $this->assertNotNull($cachedData);
        $response->assertStatus(200)
                 ->assertJsonCount(5, 'data');

        // Act: Second request should use cache
        $response = $this->getJson('/api/tasks?per_page=5');

        // Assert: Response matches cached data
        $response->assertStatus(200)
                 ->assertJsonCount(5, 'data');
    }

}