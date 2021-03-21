<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->postJson('/api-user/user/login', ['email' =>'user1@gmail.com', 'password' => '123456']);

        $response->assertStatus(200 || 201)->assertJson(['is_success' => true]);
    }
}
