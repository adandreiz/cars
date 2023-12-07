<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CarTest extends WebTestCase
{
    public function testAddCars(): void
    {
        $client = static::createClient();

        // Test body error request
        $client->xmlHttpRequest('POST', '/cars', [], [], [],'{
            "make": "Ford",
            "model": "Focus",
            "colourId": 4,
            "buildDate": "2017/07/01",}');
        $this->assertResponseStatusCodeSame(400);

        // Test can't create car without make
        $client->xmlHttpRequest('POST', '/cars', [], [], [], json_encode([
            'model' => 'Civic',
            'colourId' => 2,
            'buildDate' => '2022/02/12'
        ]));
        $this->assertResponseStatusCodeSame(422);

        // Test can't create car without model
        $client->xmlHttpRequest('POST', '/cars', [], [], [], json_encode([
            'make' => 'Honda',
            'colourId' => 2,
            'buildDate' => '2022/02/12'
        ]));
        $this->assertResponseStatusCodeSame(422);

        // Test can't create car without colour
        $client->xmlHttpRequest('POST', '/cars', [], [], [], json_encode([
            'make' => 'Honda',
            'model' => 'Civic',
            'buildDate' => '2022/02/12'
        ]));
        $this->assertResponseStatusCodeSame(422);

        // Test can't create car without buildDate
        $client->xmlHttpRequest('POST', '/cars', [], [], [], json_encode([
            'make' => 'Honda',
            'model' => 'Civic',
            'colourId' => 3
        ]));
        $this->assertResponseStatusCodeSame(422);

        // Test can't create car which already exists
        $client->xmlHttpRequest('POST', '/cars', [], [], [], json_encode([
            'make' => 'Peugeot',
            'model' => 'e-208',
            'buildDate' => '2023-05-01',
            'colourId' => 4
        ]));
        $this->assertResponseStatusCodeSame(422);

        // Test can't create car with invalid colour
        $client->xmlHttpRequest('POST', '/cars', [], [], [], json_encode([
            'make' => 'Peugeot',
            'model' => 'e-208',
            'buildDate' => '2023-05-01',
            'colourId' => 10
        ]));
        $this->assertResponseStatusCodeSame(422);

        // Test can't create car older than 4 years
        $client->xmlHttpRequest('POST', '/cars', [], [], [], json_encode([
            'make' => 'Peugeot',
            'model' => 'e-208',
            'buildDate' => '2019-05-01',
            'colourId' => 1
        ]));
        $this->assertResponseStatusCodeSame(422);

        // Test happy path creating car
        $today = new \DateTimeImmutable();
        $client->xmlHttpRequest('POST', '/cars', [], [], [], json_encode([
            'make' => 'Peugeot',
            'model' => 'e-208',
            'buildDate' => $today->format('Y/m/d'),
            'colourId' => 1
        ]));
        $this->assertResponseStatusCodeSame(201);
    }

    public function testGetCar(): void
    {
        $client = static::createClient();

        // Test car not found
        $client->xmlHttpRequest('GET', '/car/4');
        $this->assertResponseStatusCodeSame(404);

        // Test happy path
        $client->xmlHttpRequest('GET', '/car/1');
        $this->assertResponseStatusCodeSame(200);
    }

    public function testListCars(): void
    {
        $client = static::createClient();

        // Test 200 response code and that response returns 2 cars
        $client->xmlHttpRequest('GET', '/cars');
        $this->assertResponseStatusCodeSame(200);
        $content = json_decode($client->getResponse()->getContent());
        $this->assertCount(2, $content);
    }

    public function testDeleteCar(): void
    {
        $client = static::createClient();

        // Test car not found
        $client->xmlHttpRequest('DELETE', '/cars/3');
        $this->assertResponseStatusCodeSame(404);

        // Test happy path
        $client->xmlHttpRequest('DELETE', '/cars/1');
        $this->assertResponseStatusCodeSame(200);
    }

}