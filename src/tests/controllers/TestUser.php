<?php
// namespace GMarket\src\GControllers\Tests;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
// src/KnpU/CodeBattle/Tests/ProgrammerControllerTest.php

class TestUser extends TestCase
{
    protected $client;

    protected function setUp(): void
    {
    }

    public function testRegister()
    {
        // create our http client (Guzzle)
        $client = new Client(['http_errors' => false]);

        $data = [
            'username' => 'testuser',
            'password' => 'testpassword',
            'email' => 'lerryjay45@gmail.com',
            'telephone' => '+2348182886545',
        ];

        $response = $client->post(
            'http://localhost/GMarket/user/register',
            null,
            json_encode($data)
        );

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('code', $data);
    }

    public function testBadLogin()
    {
        // create our http client (Guzzle)
        $client = new Client(['http_errors' => false]);

        $data = [
            'username' => 'testuser',
            'password' => 'testpassword',
        ];

        $response = $client->post(
            'http://localhost/GMarket/user/login',
            null,
            json_encode($data)
        );

        $this->assertEquals(401, $response->getStatusCode());
        $data = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('code', $data);
    }
    public function testValidLogin()
    {
        // create our http client (Guzzle)
        $client = new Client(['http_errors' => false]);

        $data = [
            'username' => 'lerryjay45@gmail.com',
            'password' => 'Olajireh1@',
        ];

        $response = $client->post(
            'http://localhost/GMarket/user/login',
            null,
            json_encode($data)
        );

        $this->assertEquals(401, $response->getStatusCode());
        $data = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('code', $data);
    }
}
?>
