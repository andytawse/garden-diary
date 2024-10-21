<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Crop;

class CropFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $crop = new Crop();
        $crop->setCommonName('Onion');
        $crop->setPlantingMonth(10);
        $manager->persist($crop);

        $crop2 = new Crop();
        $crop2->setCommonName('Garlic');
        $crop2->setPlantingMonth(10);
        $manager->persist($crop2);
        
        $crop3 = new Crop();
        $crop3->setCommonName('Lamb\'s Lettuce');
        $crop3->setPlantingMonth(9);
        $manager->persist($crop3);

        $manager->flush();
    }
}
