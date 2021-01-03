<?php

declare(strict_types=1);

namespace App\Object;

class Bbox
{
    public function __construct(
        public Point $center,
        public float $heading,
        public $color,
        public float $top,
        public float $right,
        public float $bottom,
        public float $left,
    ) {
    }
}
