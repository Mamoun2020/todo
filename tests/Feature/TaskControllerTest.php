<?php

namespace Tests\Feature;


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
        $response->dump() ->assertJsonStructure(
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

}
