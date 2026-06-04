<?php

namespace App\Tests\Controller;

use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProfileControllerTest extends ApiTestCase
{
    public function testGetProfileSuccess(): void
    {
        $this->registerTestUser('profile@test.com', 'password123', 'John Profile');
        $this->authenticateClient('profile@test.com', 'password123');

        $this->client->request('GET', '/api/me');

        $this->assertResponseIsSuccessful();
         $responseContent = $this->client->getResponse()->getContent();
        $this->assertJson($responseContent);
    }

    public function testUpdateProfileSuccess(): void
    {
        $this->registerTestUser('update@test.com', 'password123', 'Old Name');
        $this->authenticateClient('update@test.com', 'password123');

        $this->client->request(
            'PUT',
            '/api/me',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'New Name',
                'email' => 'newemail@test.com'
            ])
        );

        $this->assertResponseIsSuccessful();

        $responseContent = $this->client->getResponse()->getContent();
        $this->assertJson($responseContent);
 
    }
}