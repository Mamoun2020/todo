<?php

namespace Tests\Feature;


use App\Models\Task;
use Database\Factories\TaskFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;
    public function test_can_get_all_tasks(){
        //Arrange
        $tasks = TaskFactory::new()->count(20)->create();
        //Art
        $response =  $this->getJson('api/tasks');
        //Assert
        $response->assertJsonStructure(
          [
              'data' =>[
                [
                    'id',
                    'title',
                    'status'
                ],
              ]

          ])->assertJsonCount(15,'data') ->assertJson(
              [
                  'meta' =>[
                      'total' => 20
                  ]
              ]
        );
    }
    public function test_can_create_new_tasks(){
        $this->postJson('api/tasks',[
            'title' => "example task",

        ])->assertSuccessful()->dump()->assertJson(
            [
                'data' =>[
                    'id' =>1,
                    'title'=>"example task",
                    'status'=>false
                ]
            ]
        );
        //weak test
//        $task = Task::find(1);
//        $this->assertEquals(1,$task->id);
//        $this->assertEquals("example task",$task->title);
//        $this->assertFalse($task->status);
    }
    public function test_task_title_is_required(){
        $this->postJson('api/tasks',[
            'title1' => "example task",

        ])->assertJsonValidationErrorFor('title');
        //weak test
//        $task = Task::find(1);
//        $this->assertEquals(1,$task->id);
//        $this->assertEquals("example task",$task->title);
//        $this->assertFalse($task->status);
    }
    public function test_task_is_incompleted_by_default(){
        $this->postJson('api/tasks',[
            'title' => "example task",
            'status' => true,

        ])->assertSuccessful()->assertJson(
          [
              'data'=>[
                  'id' =>1,
                  'title' => 'example task',
                  'status' => false
              ]
          ]
        );
        //weak test
//        $task = Task::find(1);
//        $this->assertEquals(1,$task->id);
//        $this->assertEquals("example task",$task->title);
//        $this->assertFalse($task->status);
    }
    public function  test_can_update_current_task(){
      $task = TaskFactory::new()->create(
          [
              'title'=> 'old',
              'status'=>false,
          ]
      );
      $this->putJson('api/tasks/1',[
          'title'=>'new',
          'status'=> true,
      ])->assertSuccessful()->assertJson(
          [
              'data'=>[
                  'title'=>'new',
                  'status'=>true,
              ]
          ]
      );
    }
    public function  test_can_update_completed_task(){
      $task = TaskFactory::new()->create(
          [
              'title'=> 'old',
              'status'=>true,
          ]
      );
      $this->putJson('api/tasks/1',[
          'title'=>'new',
          'status'=> false,
      ])->assertSuccessful()->assertJson(
          [
              'data'=>[
                  'title'=>'new',
                  'status'=>false,
              ]
          ]
      );
    }
    public function  test_can_delete_current_task(){
      $task = TaskFactory::new()->create(
          [
              'title'=> 'old',
              'status'=>true,
          ]
      );
      $this->deleteJson('api/tasks/1')
          ->assertSuccessful();
      $this->assertDatabaseCount('tasks',0);

    }
}
