<?php

namespace App;

use Allegro\Allegro;
use AllegroPrimitives\Primitives;
use App\Game\Direction;
use App\Game\Nodes\BlastNode;
use App\Object\Blast;
use App\Object\Spaceship;
use App\Service\CreateAsteroid\Asteroid;
use Allegro\Kernel\Event;
use Allegro\Kernel\Keyboard;

class Game
{
    private bool $running = true;

    private Allegro $allegro;

    private Primitives $primitives;

    public function __construct()
    {
        $this->allegro = \Allegro\Allegro::getInstance();
        $this->primitives = \AllegroPrimitives\Primitives::getInstance();
        $init = $this->allegro->al_install_system( \Allegro\Allegro::getInstance()->info->version, function (){});
    }

    public function run()
    {
        $direction = new Direction();
        $display = $this->allegro->al_create_display(500, 500);
        $this->allegro->al_set_window_title($display, 'blasteroid');
        $queue = $this->allegro->al_create_event_queue();
        $timer = $this->allegro->al_create_timer(1.0 / 60);
        $this->allegro->al_start_timer($timer);

        $this->allegro->al_install_keyboard();
        $this->allegro->al_register_event_source($queue, $this->allegro->al_get_display_event_source($display));
        $this->allegro->al_register_event_source($queue, $this->allegro->al_get_timer_event_source($timer));
        $this->allegro->al_register_event_source($queue, $this->allegro->al_get_keyboard_event_source());

        $spaceship = new Spaceship();
        Asteroid::generate();
        $rotate = false;
        $accelerate = false;
        $blastNode = new BlastNode();
        while ($this->running) {
            $old_time = $this->primitives->al_get_time();
            $event = $this->allegro->new('struct _ALLEGRO_EVENT');
            $this->allegro->al_wait_for_event($queue, \FFI::addr($event));
            $spaceship->draw();
            if ($event->type == 42) {
                $this->running = false;
            }
            $direction->control($event);
            $spaceship->rotate($direction);
            $spaceship->accelerate($direction);
            $blastNode->create($direction, $spaceship);
            $blastNode->calculatePosition();
            \App\Service\CreateAsteroid\Asteroid::s();
            $this->primitives->al_flip_display();
            $this->primitives->al_clear_to_color($this->primitives->al_map_rgb(0, 0, 0));
        }
    }
}