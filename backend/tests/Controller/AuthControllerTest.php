<?php

namespace App\Tests\Controller;

use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class AuthControllerTest extends ApiTestCase
{
    public function testRegisterSuccess(): void
    {
        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'John Doe',
                'email' => 'john@test.com',
                'password' => 'password123'
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        
        // CORRECTION : On cherche 'john@test.com' car c'est ce que renvoie l'API
        $this->assertStringContainsString('john@test.com', $this->client->getResponse()->getContent());
    }

    public function testRegisterEmailAlreadyUsed(): void
    {
        $this->registerTestUser('john@test.com', 'password123');
        $this->registerTestUser('john@test.com', 'password123'); // Tentative de doublon

        $this->assertResponseStatusCodeSame(Response::HTTP_CONFLICT);
    }

    public function testLoginSuccess(): void
    {
        $this->registerTestUser('john@test.com', 'password123');

        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'john@test.com',
                'password' => 'password123'
            ])
        );

        $this->assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $response);
    }
}