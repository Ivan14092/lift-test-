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
                'firstName'    => 'Ivan',
                'lastName'     => 'Petrenko',
                'phoneNumbers' => ['+380971234567', '+380631234567'],
            ])
        );

        $this->assertResponseStatusCodeSame(202);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals('Record is being processed', $data['message']);
    }

    public function testCreateUserRecordValidation(): void
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

        $this->assertResponseStatusCodeSame(400);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
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
}