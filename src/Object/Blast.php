<?php

namespace App\Object;

use AllegroPrimitives\Primitives;

class Blast
{
    public float $sx = 0;
    public float $sy = 0;
    public float $heading = 0.01;
    public float $speed = 0.001;
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

	    $this->speed = 2.0;
	    $this->color = Primitives::getInstance()->al_map_rgb(255, 0, 0);
    }

    public function draw()
    {
        $transform = Primitives::getInstance()->new('struct _ALLEGRO_TRANSFORM');
        $primitives = Primitives::getInstance();
	    $primitives->al_identity_transform(Primitives::addr($transform));
	    $primitives->al_rotate_transform(Primitives::addr($transform), $this->heading);
	    $primitives->al_translate_transform(Primitives::addr($transform), $this->sx, $this->sy);
	    $primitives->al_use_transform(Primitives::addr($transform));

	    $primitives->al_draw_line(0, 0, 0, -3, $this->color, 20.0);
    }

    public function calculateBlastPosition()
    {

	$deltaX = sin($this->heading) * $this->speed;
	$deltaY = cos($this->heading) * $this->speed;

	$this->sx += $deltaX;
	$this->sy -= $deltaY;

//	check_if_blast_out_of_bounds(blast);

    }
}