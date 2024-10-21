<?php

namespace App;

use Symfony\Component\Clock\ClockInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Crop;

class CropsByMonth
{
    public function __construct(
        private ClockInterface $clock,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function getCropsToPlantInCurrentMonth(): array
    {
        $crops = $this->entityManager->getRepository(Crop::class)->findBy(
            ['plantingMonth' => $this->clock->now()->format('m')]      
        );
        return $crops;
    }
}
