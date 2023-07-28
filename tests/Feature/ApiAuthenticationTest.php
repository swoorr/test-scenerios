<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ApiAuthenticationTest extends TestCase
{
    private $apiKey = null;
    private $secretKey = null;

    public function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->apiKey = $user->api_key;
        $this->secretKey = $user->secret_key;

    }


    /**
     * A basic feature test example.
     */
    public function test_auth(): void
    {


        $response = $this->postJson('/api/authenticate', ['api_key' => $this->apiKey, 'secret_key' => $this->secretKey]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => true,
            ]);
    }


    public function test_users(): void
    {

        $response = $this->post('/api/app/users', data: [], headers: ['api_key' => $this->apiKey, 'secret_key' => $this->secretKey]);


        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => true,
            ]);

    }

    public function test_users_rate_limit()
    {
        for ($i = 0; $i < 70; $i++) {
            $response = $this->post('/api/app/users', data: [], headers: ['api_key' => $this->apiKey, 'secret_key' => $this->secretKey]);
        }

        $response->assertStatus(429);
    }

    public function testApiResponseData()
    {
        $response = $this->post('/api/app/users', data: [], headers: ['api_key' => $this->apiKey, 'secret_key' => $this->secretKey]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',g
                        'email'
                    ]
                ]
            ]);
    }

    public function test_error_handling()
    {
        $response = $this->post('/api/app/users', data: [], headers: ['api_key' => null, 'secret_key' => $this->secretKey]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'status' => false,
                'message' => 'APIKEY and SECRETKEY required!',
            ]);
    }
}
