<?php

namespace App\Object;

use AllegroPrimitives\Primitives;
use App\Game\Direction;

class Spaceship
{
    private const SHIP_DRIFTING_GRADIENT = 0.025;
    private const SHIP_BRAKE_GRADIENT = 0.1;
    private const SHIP_DEGREE_OF_ROTATION_RADIAN = 0.1047;
    private const SHIP_MAX_SPEED = 2.0;
    private const SHIP_ACCELERATION_GRADIENT = 0.5;

    public float $sx = 100;
    public float $sy = 100;
    public float $heading = 10;
    public float $speed = 0.2;
    public int $gone = 0;
    public int $live = 3;
    public bool $is_invincible = false;
    public $color;
    public $bc;
    private bool $is_drifting = true;
    private float $drift_heading = 0.1;

    public function __construct()
    {
        $this->color = Primitives::getInstance()->al_map_rgb(0, 255, 0);
    }

    public function draw()
    {
        $transform = Primitives::getInstance()->new('struct _ALLEGRO_TRANSFORM');
        Primitives::getInstance()->al_identity_transform(Primitives::addr($transform));
        Primitives::getInstance()->al_rotate_transform(Primitives::addr($transform), $this->heading);
        Primitives::getInstance()->al_translate_transform(Primitives::addr($transform), $this->sx, $this->sy);
        Primitives::getInstance()->al_use_transform(Primitives::addr($transform));
        Primitives::getInstance()->al_draw_line(-8, 9, 0, -11, $this->color, 3.0);
        Primitives::getInstance()->al_draw_line(0, -11, 8, 9, $this->color, 3.0);
        Primitives::getInstance()->al_draw_line(-6, 4, -1, 4, $this->color, 3.0);
        Primitives::getInstance()->al_draw_line(6, 4, 1, 4, $this->color, 3.0);

    }

    public function rotate(Direction $direction) {
        if ($direction->left)
            $this->heading -= self::SHIP_DEGREE_OF_ROTATION_RADIAN;
        else if ($direction->right)
            $this->heading += self::SHIP_DEGREE_OF_ROTATION_RADIAN;
    }

    public function accelerate(Direction $direction)
    {
        if ($direction->up) {
            if ($this->speed < self::SHIP_MAX_SPEED)
                $this->speed += self::SHIP_ACCELERATION_GRADIENT;
            if ($this->speed > self::SHIP_MAX_SPEED)
                $this->speed = self::SHIP_MAX_SPEED;

            $this->drift_heading = $this->heading;
            $this->is_drifting = false;
            $this->calculatePosition();
        }

    }

    public function calculatePosition()
    {
        if ($this->is_drifting)
            $current_heading = $this->drift_heading;
        else
            $current_heading = $this->heading;

        $deltaX = sin($current_heading) * $this->speed;
        $deltaY = cos($current_heading) * $this->speed;

        $this->sx += $deltaX;
        $this->sy -= $deltaY;
    }

    public function driftShip()
    {
        if ($this->speed > 0)
            $this->speed -= self::SHIP_DRIFTING_GRADIENT;
        if ($this->speed < 0)
            $this->speed = 0;
        $this->is_drifting = true;
    }

    public function brakeShip()
    {
	    if ($this->speed > 0)
		    $this->speed -= self::SHIP_BRAKE_GRADIENT;
	    if ($this->speed < 0)
		    $this->speed = 0;
}
}