<?php

namespace App\Object;

use AllegroPHP\Allegro\Allegro;
use AllegroPHP\Primitives\Primitives;

class Blast
{
    public float $sx = 0;
    public float $sy = 0;
    public float $heading = 0.02;
    public float $speed = 0.01;
    public int $gone = 0;
    public $color;

    public function __construct(Spaceship $spaceship)
    {
        $this->heading = $spaceship->heading;
        $this->gone = false;

        $deltaX = sin($this->heading) * 11;
        $this->sx = $spaceship->sx + $deltaX;
        $deltaY = cos($spaceship->heading) * 11;
        $this->sy = $spaceship->sy - $deltaY;

        $this->speed = 3.0;
        $this->color = Allegro::getInstance()->info->ffi->al_map_rgb(255, 0, 0);
    }

    public function draw(): void
    {
        $transform = Allegro::getInstance()->new('struct _ALLEGRO_TRANSFORM');
        $primitives = Primitives::getInstance()->info->ffi;
        $primitives->al_identity_transform(\FFI::addr($transform));
        $primitives->al_rotate_transform(\FFI::addr($transform), $this->heading);
        $primitives->al_translate_transform(\FFI::addr($transform), $this->sx, $this->sy);
        $primitives->al_use_transform(\FFI::addr($transform));

        $primitives->al_draw_line(0, 0, 0, -3, $this->color, 20.0);
    }

    public function calculateBlastPosition(): void
    {
        $deltaX = sin($this->heading) * $this->speed;
        $deltaY = cos($this->heading) * $this->speed;

        $this->sx += $deltaX;
        $this->sy -= $deltaY;
    }
}