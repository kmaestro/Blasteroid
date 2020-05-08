<?php

namespace App\Service\CreateAsteroid;

use AllegroPrimitives\Primitives;
use http\Exception\BadUrlException;

class Asteroid
{
    public static $asteroids = [];

    public static function setAsteroid(\App\Object\Asteroid $asteroid)
    {
        self::$asteroids[] = $asteroid;
    }

    public static function getAsteroid()
    {
        return self::$asteroids;
    }

    public static function generate()
    {

        for ($i = 0; $i < 10; $i++) {
            $a = new \App\Object\Asteroid();
            $a->draw();
            self::$asteroids[] = $a;
        }
    }

    public static function s()
    {
        foreach (self::$asteroids as $v) {
            $v->draw();
            $v->calculateAsteroidPosition();
        }
    }
}