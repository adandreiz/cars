<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CarTest extends WebTestCase
{
    public function testAddCars(): void
    {
        $client = static::createClient();

        // Test body error request
        $client->jsonRequest('POST', '/cars',[
            'model' => 'Civic',
            'colourId' => 1,
            'buildDate' => '2022-02-12']);
        $this->assertResponseStatusCodeSame(422);
        $this->assertMatchesRegularExpression('/Colour not found/', $client->getResponse()->getContent());

        // Test can't create car without make
        $client->jsonRequest('POST', '/cars', [
            'model' => 'Civic',
            'colourId' => 1,
            'buildDate' => '2022-02-12'
        ]);
        $this->assertResponseStatusCodeSame(422);

        // Test can't create car without model
        $client->jsonRequest('POST', '/cars', [
            'make' => 'Honda',
            'colourId' => 1,
            'buildDate' => '2022-02-12'
        ]);
        $this->assertResponseStatusCodeSame(422);

        // Test can't create car without colour
        $client->jsonRequest('POST', '/cars', [
            'make' => 'Honda',
            'model' => 'Civic',
            'buildDate' => '2022-02-12'
        ]);
        $this->assertResponseStatusCodeSame(422);

        // Test can't create car without buildDate
        $client->jsonRequest('POST', '/cars', [
            'make' => 'Honda',
            'model' => 'Civic',
            'colourId' => 1
        ]);
        $this->assertResponseStatusCodeSame(422);

        // Test can't create car which already exists
        $client->jsonRequest('POST', '/cars', [
            'make' => 'Peugeot',
            'model' => 'e-208',
            'buildDate' => '2023-05-01',
            'colourId' => 4,
        ]);
        $this->assertResponseStatusCodeSame(422);

        // Test can't create car with invalid colour
        $client->jsonRequest('POST', '/cars', [
            'make' => 'Peugeot',
            'model' => 'e-208',
            'buildDate' => '2023-05-01',
            'colourId' => 13,
        ]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertMatchesRegularExpression('/Colour not found/', $client->getResponse()->getContent());

        // Test can't create car older than 4 years
        $client->jsonRequest('POST', '/cars', [
            'make' => 'Peugeot',
            'model' => 'e-208',
            'buildDate' => '2019-05-01',
            'colourId' => 1,
        ]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertMatchesRegularExpression('/buildDate: This value should be greater than or equal/', $client->getResponse()->getContent());


        // Test happy path creating car
        $today = new \DateTimeImmutable();
        $client->jsonRequest('POST', '/cars', [
            'make' => 'Peugeot',
            'model' => 'e-208',
            'buildDate' => $today->format('Y-m-d'),
            'colourId' => 1,
        ]);
        $this->assertResponseStatusCodeSame(201);
    }

    public function testGetCar(): void
    {
        $client = static::createClient();

        // Test car not found
        $client->jsonRequest('GET', '/car/4');
        $this->assertResponseStatusCodeSame(404);

        // Test happy path
        $client->jsonRequest('GET', '/car/1');
        $this->assertResponseStatusCodeSame(200);
    }

    public function testListCars(): void
    {
        $client = static::createClient();

        // Test 200 response code and that response returns 2 cars
        $client->jsonRequest('GET', '/cars');
        $this->assertResponseStatusCodeSame(200);
        $content = json_decode($client->getResponse()->getContent());
        $this->assertCount(2, $content);
    }

    public function testDeleteCar(): void
    {
        $client = static::createClient();

        // Test car not found
        $client->jsonRequest('DELETE', '/cars/3');
        $this->assertResponseStatusCodeSame(404);

        // Test happy path
        $client->jsonRequest('DELETE', '/cars/1');
        $this->assertResponseStatusCodeSame(200);
    }

}