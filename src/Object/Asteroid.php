<?php

namespace App\Object;

use AllegroPrimitives\Primitives;

class Asteroid
{
    public float $sx = 110.0;
    public float $sy = 110.0;
    public float $heading = 1.5;
    public float $twist = 0.5;
    public float $speed = 0.5;
    public float $scale = 1.1;
    public float $rot_velocity = 0.03;
    public int $gone = 0;
    public $color;
    public $transform;
    public $bc;

    public function __construct()
    {
        $this->sy = rand(1, 500);
        $this->sx = rand(1, 500);
        $this->scale = rand(50, 100)/100;
        $this->heading = rand(1, 1000)/100;
        $this->speed = rand(1000, 2000)/1000;
        $this->twist = rand(1, 1000)/1000;
        $this->color = Primitives::getInstance()->al_map_rgb(0, 255, 0);
    }

    public function draw()
    {
        $transform = Primitives::getInstance()->new('struct _ALLEGRO_TRANSFORM');
	    Primitives::getInstance()->al_identity_transform(Primitives::addr($transform));
	    Primitives::getInstance()->al_rotate_transform(Primitives::addr($transform), $this->twist);
	    Primitives::getInstance()->al_scale_transform(Primitives::addr($transform), $this->scale, $this->scale);
	    Primitives::getInstance()->al_translate_transform(Primitives::addr($transform), $this->sx, $this->sy);
	    Primitives::getInstance()->al_use_transform(Primitives::addr($transform));
	    $this->transform = $transform;

	    Primitives::getInstance()->al_draw_line(-20, 20, -25, 5, $this->color, 2.0);
	    Primitives::getInstance()->al_draw_line(-25, 5, -25, -10, $this->color, 2.0);
	    Primitives::getInstance()->al_draw_line(-25, -10, -5, -10, $this->color, 2.0);
	    Primitives::getInstance()->al_draw_line(-5, -10, -10, -20, $this->color, 2.0);
	    Primitives::getInstance()->al_draw_line(-10, -20, 5, -20, $this->color, 2.0);
	    Primitives::getInstance()->al_draw_line(5, -20, 20, -10, $this->color, 2.0);
	    Primitives::getInstance()->al_draw_line(20, -10, 20, -5, $this->color, 2.0);
	    Primitives::getInstance()->al_draw_line(20, -5, 0, 0, $this->color, 2.0);
	    Primitives::getInstance()->al_draw_line(0, 0, 20, 10, $this->color, 2.0);
	    Primitives::getInstance()->al_draw_line(20, 10, 10, 20, $this->color, 2.0);
	    Primitives::getInstance()->al_draw_line(10, 20, 0, 15, $this->color, 2.0);
	    Primitives::getInstance()->al_draw_line(0, 15, -20, 20, $this->color, 2.0);
    }

    public function calculateAsteroidPosition()
    {
//        $this->calculateAsteroidTwisting();

	    $deltaX = sin($this->heading) * $this->speed;
	    $deltaY = cos($this->heading) * $this->speed;

	    $this->sx += $deltaX;
	    $this->sy -= $deltaY;
	    $this->calculateAsteroidTwisting();
    }

    public function calculateAsteroidTwisting()
    {
        $this->twist += $this->rot_velocity;

        if ($this->sy < 0)
            $this->sy += SCREEN_HEIGHT;
        if ($this->sy > SCREEN_HEIGHT)
            $this->sy -= SCREEN_HEIGHT;
        if ($this->sx < 0)
            $this->sx += SCREEN_WIDTH;
        if ($this->sx > SCREEN_WIDTH)
            $this->sx -= SCREEN_WIDTH;
    }
}