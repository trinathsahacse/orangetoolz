<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskDetails;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use stdClass;
use Tests\TestCase;

class TaskTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_get_active_task_by_id()
    {
        $task_id = Task::where('status', 1)->get()->random()->id;
        $response = $this->get('/api/v1/task/' . $task_id.'/edit')
            ->assertStatus(200)
            ->assertJsonStructure(
                [
                    'message',
                    'data' => [
                        'id',
                        'name',
                        'status',
                        'details' => [
                            '*' => [
                                "id",
                                "name",
                                "description",
                                "create_at",
                                "update_at"
                            ],
                        ]
                    ],
                ]
            );
    }

    public function test_for_delete_task_that_not_exist()
    {
        //review id that not exist in database
        $reviewId = random_int(100000, 999999);
 
        $this->json('DELETE', 'api/v1/task/' . $reviewId)
            ->assertStatus(200)
            ->assertJson([
                'error' => 1,
                'message' => 'Task not found',
            ]);
    }

    public function test_get_all_active_tasks()
    {
        $response = $this->get('/api/v1/task')
            ->assertStatus(200)
            ->assertJsonStructure(
                [
                    'message',
                    'data' =>  [
                        '*' => [
                            "id",
                            "name",
                            "created_at",
                            "updated_at",
                            "status",
                            "details" => [
                                '*' => [
                                    "id",
                                    "name",
                                    "description",
                                    "created_at",
                                    "updated_at"
                                ],
                            ],
                        ],
                    ],
                ]
            );
    }

    public function test_for_add_task()
    {
        $detail_obj = new stdClass();
        $detail_obj->name = "test details";
        $detail_obj->description = "test details description";
        $detail_obj->created_at = Carbon::now();
        $detail_obj->updated_at = Carbon::now();
        $task_d = [];
        array_push($task_d,$detail_obj);

        $details = [
            'name' => rand(),
            'status' => 1,
            'details' => $task_d,
            'created_at' => Carbon::now(),
        ];
 
        $this->json('POST', 'api/v1/task', $details)
            ->assertStatus(201)
            ->assertJson([
                'message' => 'Task Created',
            ]);
    }
}
