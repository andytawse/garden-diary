<?php

namespace App;

use Symfony\Component\Clock\ClockInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Crop;

/**
 * A class to try out services in Symphony
 *
 * Maybe this could be combined with a repository?
 */
class CropsByMonth
{
    public function __construct(
        private ClockInterface $clock,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Return crops that can be planted in the current month.
     *
     * @return array
     *   An array of Crop objects,
     */ 
    public function getCropsToPlantInCurrentMonth(): array
    {
        $crops = $this->entityManager->getRepository(Crop::class)->findBy(
            ['plantingMonth' => $this->clock->now()->format('m')]      
        );
        return $crops;
    }
}
