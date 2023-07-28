<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;
    private mixed $apiKey = null;
    private mixed $secretKey = null;
    private array $header = [];

    public function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->apiKey = $user->api_key;
        $this->secretKey = $user->secret_key;

        $this->header = ['api_key' => $this->apiKey, 'secret_key' => $this->secretKey];
    }

    public function test_auth(): void
    {
        $response = $this->postJson('/api/authenticate', $this->header);

        $response
            ->assertSuccessful();

    }


    public function test_users(): void
    {
        $response = $this->get('/api/app/users', $this->header);

        $response
            ->assertSuccessful();

    }

    public function test_users_rate_limit(): void
    {
        for ($i = 0; $i < 70; $i++) {
            $response = $this->get('/api/app/users', $this->header);
        }

        $response->assertStatus(429);
    }

    public function test_api_response_data(): void
    {
        User::factory()->count(9)->create();

        $response = $this->get('/api/app/users', $this->header);

        $response
            ->assertSuccessful()
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email'
                    ]
                ]
            ]);
    }

    public function test_error_handling(): void
    {
        $dataProvider = [
            'apiKeyNull'=> [
                'data' => ['api_key' => null, 'secret_key' => $this->secretKey],
                'expect' => ['message' => 'APIKEY required!']
            ],
            'secretKeyNull'=> [
                'data' => ['api_key' => $this->apiKey, 'secret_key' => null],
                'expect' => ['message' => 'SECRETKEY required!']
            ],
        ];

        $response = $this->get('/api/app/users', $dataProvider['apiKeyNull']['data']);

        $response
            ->assertStatus(401)
            ->assertJson($dataProvider['apiKeyNull']['expect']);

        $response = $this->get('/api/app/users', $dataProvider['secretKeyNull']['data']);

        $response
            ->assertStatus(401)
            ->assertJson($dataProvider['secretKeyNull']['expect']);
    }
}
