<?php

namespace App\DataFixtures;

use App\Entity\Colour;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $colours = ['red', 'blue', 'white', 'black'];
        foreach ($colours as $colourName) {
            $colour = new Colour();
            $colour->setName($colourName);
            $manager->persist($colour);
        }
        $manager->flush();
    }
}
