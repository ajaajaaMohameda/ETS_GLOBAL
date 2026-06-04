<?php

namespace App\Tests\Controller;

use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class SessionControllerTest extends ApiTestCase
{
    public function testCreateSessionSuccess(): void
    {
        $this->registerTestUser('admin@test.com', 'password', 'Admin', ['ROLE_ADMIN']);
        $this->authenticateClient('admin@test.com', 'password');

        $this->client->request(
            'POST',
            '/api/sessions',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'language' => 'TOEIC English',
                'startsAt' => '2025-10-10 14:00:00',
                'location' => 'Rabat',
                'capacity' => 20
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
  
        $responseContent = $this->client->getResponse()->getContent();
        $this->assertJson($responseContent);

        $responseData = json_decode($responseContent, true);
        $this->assertEquals('TOEIC English', $responseData['language']);
        $this->assertEquals(20, $responseData['capacity']);
    }

public function testListSessionsWithPagination(): void
    {
        $this->registerTestUser('admin-list@test.com', 'password', 'Admin', ['ROLE_ADMIN']);
        $this->authenticateClient('admin-list@test.com', 'password');

        $this->client->request('POST', '/api/sessions', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'language' => 'French', 
            'startsAt' => '2025-10-10 10:00:00', 
            'location' => 'Paris', 
            'capacity' => 10
        ]));
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $this->client->request('POST', '/api/sessions', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'language' => 'Spanish', 
            'startsAt' => '2025-11-11 11:00:00', 
            'location' => 'Madrid', 
            'capacity' => 15
        ]));
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $this->client->request(
            'GET', 
            '/api/sessions?page=1&limit=1', 
            [], 
            [], 
            ['HTTP_ACCEPT' => 'application/json']
        );

        $this->assertResponseIsSuccessful();
        
        $responseContent = $this->client->getResponse()->getContent();
        
    
        $response = json_decode($responseContent, true);
        
        $this->assertArrayHasKey('data', $response, 'Le JSON ne contient pas la clé "data"'); 
        $this->assertCount(1, $response['data']); 

    }
}