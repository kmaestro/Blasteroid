<?php

require __DIR__ . '/vendor/autoload.php';
$allegro = \Allegro\Allegro::getInstance();
$primitives = \AllegroPrimitives\Primitives::getInstance();
$init = $allegro->al_init();

$display = $allegro->al_create_display(500, 500);
$queue = $allegro->al_create_event_queue();
$timer = $allegro->al_create_timer(1.0 / 60);
$allegro->al_start_timer($timer);

$running = true;

$allegro->al_install_keyboard();
$allegro->al_register_event_source($queue, $allegro->al_get_display_event_source($display));
$allegro->al_register_event_source($queue, $allegro->al_get_timer_event_source($timer));
$allegro->al_register_event_source($queue, $allegro->al_get_keyboard_event_source());
$spaceship = new \App\Object\Spaceship();
$asteroid = new \App\Object\Asteroid();
$rotate = false;
$accelerate = false;
while ($running) {
    $old_time = $primitives->al_get_time();
    $event = $allegro->new('struct _ALLEGRO_EVENT');
    $allegro->al_wait_for_event($queue, FFI::addr($event));
    $spaceship->draw();
    if ($event->type == 42) {
        $running = false;
    }
    if (
        $event->keyboard->keycode == \Allegro\Kernel\Keyboard\Key::ALLEGRO_KEY_LEFT
        || $event->keyboard->keycode == \Allegro\Kernel\Keyboard\Key::ALLEGRO_KEY_RIGHT) {
        $rotate = ($event->type == \Allegro\Kernel\Event\Key::ALLEGRO_EVENT_KEY_CHAR)?$event->keyboard->keycode:false;
        $event->keyboard->keycode = 0;
    }

    if ($event->keyboard->keycode == (\Allegro\Kernel\Keyboard\Key::ALLEGRO_KEY_UP)) {
        $accelerate = ($event->type == \Allegro\Kernel\Event\Key::ALLEGRO_EVENT_KEY_CHAR)?$event->keyboard->keycode:false;
        $event->keyboard->keycode = 0;
    }
    if ($rotate) {
        $spaceship->rotate($rotate);
        $spaceship->calculatePosition();
    }

    if ($accelerate) {
        $spaceship->accelerate($accelerate);
        $spaceship->calculatePosition();
    }
    $asteroid->draw();
    $asteroid->calculateAsteroidPosition();
    $primitives->al_flip_display();
    $primitives->al_clear_to_color($primitives->al_map_rgb(0, 0, 0));
    var_dump($asteroid->sy);
    var_dump($asteroid->sx);
}

unset($display);
unset($queue);