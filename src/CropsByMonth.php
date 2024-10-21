<?php

namespace App;

use Symfony\Component\Clock\ClockInterface;
use App\Entity\Crop;

class CropsByMonth
{
    public function __construct(
        private ClockInterface $clock
    ) {
    }

    public function getCropsToPlantInCurrentMonth(): array
    {
        if (10 == $this->clock->now()->format('m')) {
            return [new Crop('Onion'), new Crop('Garlic')];
        }
    }
}
