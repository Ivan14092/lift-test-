<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserRecordControllerTest extends WebTestCase
{
    public function testCreateUserRecord(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/users',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'firstName' => 'Ivan',
                'lastName' => 'Petrenko',
                'phoneNumbers' => ['+380971234567', '+380631234567'],
            ])
        );
        $this->assertResponseStatusCodeSame(202);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals('Record is being processed', $data['message']);
    }

    public function testCreateUserRecordMissingFields(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/users',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'firstName' => 'Ivan',
            ])
        );
        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateUserRecordInvalidJson(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/users',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            'invalid json'
        );
        $this->assertResponseStatusCodeSame(400);
    }

    public function testGetUserRecords(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/users');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testGetUserRecordsWithSorting(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/users?sort=firstName&direction=asc');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testGetUserRecordsWithDescSorting(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/users?sort=createdAt&direction=desc');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJson($client->getResponse()->getContent());
    }
}