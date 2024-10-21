<?php

namespace App\Entity;

/**
 * Placeholder entity. Will be converted to an ORM entity later.
 */
class Crop
{
    public int $plantingMonth;

    public function __construct(
        public string $title,
    ) {
    }
}
