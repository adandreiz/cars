<?php

namespace App\DataFixtures;

use App\Entity\Car;
use App\Entity\Colour;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    public function __construct(private string $env)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $colours = ['red', 'blue', 'white', 'black'];
        foreach ($colours as $colourName) {
            $colour = new Colour();
            $colour->setName($colourName);
            $manager->persist($colour);
        }
        $manager->flush();

        if ($this->env === 'test') {
            $cars = [
                0 => [
                    'make' => 'Renault',
                    'model' => 'Megane',
                    'buildDate' => new \DateTimeImmutable ('2022/10/01')
                ],
                1 => [
                    'make' => 'Peugeot',
                    'model' => 'e-208',
                    'buildDate' => new \DateTimeImmutable('2023/05/01')
                ]
            ];
            foreach ($cars as $carFixture) {
                $car = new Car();
                $car->setMake($carFixture['make'])
                    ->setModel($carFixture['model'])
                    ->setBuildDate($carFixture['buildDate'])
                    ->setColour($colour);
                $manager->persist($car);
            }
            $manager->flush();
        }

    }
}
