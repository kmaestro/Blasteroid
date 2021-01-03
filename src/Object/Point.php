<?php

declare(strict_types=1);

namespace App\Object;

class Point
{
    public function __construct(
        public float $x,
        public float $y,
    )
    {
    }
}
