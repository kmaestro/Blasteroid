<?php

namespace App\Game;

use Allegro\Kernel\Event;
use Allegro\Kernel\Keyboard;

class Direction
{
    public bool $up = false;
    public bool $down = false;
    public bool $left = false;
    public bool $right = false;
    public bool $space = false;

    public function control($event)
    {
        switch ($event->keyboard->keycode) {
            case Keyboard\Key::ALLEGRO_KEY_LEFT:
                $this->left = $this->isType($event->type);
                return true;
            case Keyboard\Key::ALLEGRO_KEY_RIGHT:
                $this->right = $this->isType($event->type);
                return true;
            case Keyboard\Key::ALLEGRO_KEY_UP:
                $this->up = $this->isType($event->type);
                return true;
            case Keyboard\Key::ALLEGRO_KEY_SPACE:
                $this->space = $this->isType($event->type);
                return true;
        }
        return false;
    }

    private function isType($type)
    {
        return ($type == Event\Key::ALLEGRO_EVENT_KEY_CHAR);
    }
}