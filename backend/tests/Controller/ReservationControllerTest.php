<?php

namespace App\Tests\Controller;

use App\Document\TestSession;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ReservationControllerTest extends ApiTestCase
{
    private function createTestSession(int $capacity = 10): string
    {
        $session = new TestSession(
            'English',
            new \DateTimeImmutable('+10 days'),
            'Paris',
            $capacity
        );

        $this->dm->persist($session);
        $this->dm->flush();

        return $session->getId();
    }

    public function testCreateReservationSuccess(): void
    {
        $this->registerTestUser('user-create@test.com', 'password');
        $this->authenticateClient('user-create@test.com', 'password');

        $sessionId = $this->createTestSession();

        $this->client->request(
            'POST',
            '/api/reservations',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['sessionId' => $sessionId])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testCannotReserveSameSessionTwice(): void
    {
        $this->registerTestUser('user-twice@test.com', 'password');
        $this->authenticateClient('user-twice@test.com', 'password');

        $sessionId = $this->createTestSession();
        $payload = json_encode(['sessionId' => $sessionId]);

        $this->client->request('POST', '/api/reservations', [], [], ['CONTENT_TYPE' => 'application/json'], $payload);
        $this->client->request('POST', '/api/reservations', [], [], ['CONTENT_TYPE' => 'application/json'], $payload);

        $this->assertResponseStatusCodeSame(Response::HTTP_CONFLICT);
    }

    public function testCannotReserveIfNoSeatsAvailable(): void
    {
        $this->registerTestUser('user-seats1@test.com', 'password');
        $this->registerTestUser('user-seats2@test.com', 'password');

        $sessionId = $this->createTestSession(1);
        $payload = json_encode(['sessionId' => $sessionId]);

        $this->authenticateClient('user-seats1@test.com', 'password');
        $this->client->request('POST', '/api/reservations', [], [], ['CONTENT_TYPE' => 'application/json'], $payload);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $this->authenticateClient('user-seats2@test.com', 'password');
        $this->client->request('POST', '/api/reservations', [], [], ['CONTENT_TYPE' => 'application/json'], $payload);
        
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testListReservationsWithPagination(): void
    {
        $this->registerTestUser('user-list-res@test.com', 'password');
        $this->authenticateClient('user-list-res@test.com', 'password');

        $sessionId1 = $this->createTestSession();
        $sessionId2 = $this->createTestSession();

        $this->client->request('POST', '/api/reservations', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['sessionId' => $sessionId1]));
        $this->client->request('POST', '/api/reservations', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['sessionId' => $sessionId2]));

        $this->client->request(
            'GET', 
            '/api/reservations?page=1&limit=1', 
            [], 
            [], 
            ['HTTP_ACCEPT' => 'application/json']
        );

        $this->assertResponseIsSuccessful();
        
        $response = json_decode($this->client->getResponse()->getContent(), true);
        
        $this->assertArrayHasKey('data', $response); 
        $this->assertCount(1, $response['data']); 
        
        $this->assertArrayHasKey('pagination', $response);
        $this->assertEquals(1, $response['pagination']['page']);
        $this->assertEquals(1, $response['pagination']['limit']);
        $this->assertEquals(2, $response['pagination']['total']);
        $this->assertEquals(2, $response['pagination']['pages']);
    }
}