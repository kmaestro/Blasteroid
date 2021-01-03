<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Game\Direction;
use Serafim\FFILoader\Loader;

const FPS = 60;

const SCREEN_WIDTH = 640;
const SCREEN_HEIGHT = 480;

$keys = [
    'UP' => 0,
    'DOWN' => 0,
    'LEFT' => 0,
    'RIGHT' => 0,
    'SPACE' => 0,
];

$redraw = 1; // перерисовать
$doexit = 0; // выходить
$score = 0; // количество очков
$asteroid_num = 4; // номер астероида

$acodec = FFI::cdef(
    'extern bool al_init_acodec_addon(void);',
    'liballegro_acodec.so'
);

$font = FFI::cdef(
    'extern bool al_init_font_addon(void);',
    'liballegro_font.so'
);

$ttf = FFI::cdef(
    'extern bool al_init_ttf_addon(void);',
    'liballegro_ttf.so'
);

$audio =  FFI::cdef(
    'extern bool al_install_audio(void);
    extern bool al_reserve_samples(int reserve_samples);
    extern bool al_reserve_samples(int reserve_samples);',
    'liballegro_audio.so'
);

$loader = new Loader();
$allegroInfo = $loader->load(new \Allegro\Library());

$allegroPrimitives = $loader->load(new \AllegroPrimitives\Library())->ffi;
$allegro = \Allegro\Allegro::getInstance()->info->ffi;

$init = $allegro->al_install_system( \Allegro\Allegro::getInstance()->info->version, function (){});

if (!$init) {
    fprintf(STDERR, 'Не удалось инициализировать allegro!' . PHP_EOL);
    return -1;
}

if (!$allegroPrimitives->al_init_primitives_addon()) {
    fprintf(STDERR, 'Не удалось инициализировать allegro primitives!' . PHP_EOL);
    return -1;
}

if (!$acodec->al_init_acodec_addon()) {
    fprintf(STDERR, 'Не удалось инициализировать аудиокодеки!' . PHP_EOL);
    return -1;
}

$font->al_init_font_addon();

if (!$ttf->al_init_ttf_addon()) {
    fprintf(STDERR, 'Не удалось инициализировать ttf!' . PHP_EOL);
    return -1;
}

if (!$allegro->al_install_keyboard()) {
    fprintf(STDERR, 'Не удалось установить keyboard!' . PHP_EOL);
    return -1;
}

if (!$audio->al_install_audio()) {
    fprintf(STDERR, "Не удалось установить аудио!\n");
    return -1;
}

if (!$audio->al_reserve_samples(3)){
    fprintf(STDERR, "failed to reserve samples!\n");
    return -1;
}

$timer = new \App\Timer(FPS);

$display = new \App\Window(SCREEN_WIDTH, SCREEN_HEIGHT);

$event_queue = $allegro->al_create_event_queue();

/* Registering everything in event queue */
$allegro->al_register_event_source($event_queue, $allegro->al_get_keyboard_event_source());
$allegro->al_register_event_source($event_queue, $allegro->al_get_display_event_source($display->display()));
$allegro->al_register_event_source($event_queue, $allegro->al_get_timer_event_source($timer->timer()));


/* Set background black */
$allegroPrimitives->al_clear_to_color($allegroPrimitives->al_map_rgb(0, 0, 0));

$allegroPrimitives->al_flip_display();

$direction = new Direction();
$spaceship = new  \App\Object\Spaceship();
$blast = new \App\Object\Blast($spaceship);
$timer->start();
\App\Service\CreateAsteroid\Asteroid::setAsteroid(new \App\Object\Asteroid());
$blasts = [];
while (!$doexit)
{
    \App\Service\CreateAsteroid\Asteroid::draw();
    $event = $allegro->new('ALLEGRO_EVENT');
    $allegro->al_wait_for_event($event_queue, FFI::addr($event));


    $spaceship->draw();
    $spaceship->driftShip();
    if ($event->type === 30) {
        $redraw = 1;
        if ($keys['UP']) {
            $direction->up = true;
            $spaceship->accelerate($direction);
        }
        if ($keys['RIGHT']) {
            $direction->right = true;
            $spaceship->rotate($direction);
            $direction->right = false;
        }

        if ($keys['LEFT']) {
            $direction->left = true;
            $spaceship->rotate($direction);
            $direction->left = false;
        }

        foreach ($blasts as $key => $blast) {
            $blast->draw();
            $blast->calculateBlastPosition();
            if (
                $blast->sy < 0
                || $blast->sy > SCREEN_HEIGHT
                || $blast->sx < 0
                || $blast->sx > SCREEN_WIDTH
            ) {
                unset($blasts[$key]);
            }
        }


    } elseif ($event->type == 10) {
        switch ($event->keyboard->keycode) {
            case Allegro\Kernel\Keyboard\Key::ALLEGRO_KEY_UP:
                $keys['UP'] = 1;
                break;
            case Allegro\Kernel\Keyboard\Key::ALLEGRO_KEY_DOWN:
                $keys['DOWN'] = 1;
                break;
            case Allegro\Kernel\Keyboard\Key::ALLEGRO_KEY_RIGHT:
                $keys['RIGHT'] = 1;
                break;
            case Allegro\Kernel\Keyboard\Key::ALLEGRO_KEY_LEFT:
                $keys['LEFT'] = 1;
                break;
            case Allegro\Kernel\Keyboard\Key::ALLEGRO_KEY_SPACE:
                if (!$keys['SPACE']) {
                    $blasts[] = new \App\Object\Blast($spaceship);
                }
                $keys['SPACE'] = 1;
                break;
        }
    } elseif ($event->type == 12) {
        switch ($event->keyboard->keycode) {
            case Allegro\Kernel\Keyboard\Key::ALLEGRO_KEY_UP:
                $keys['UP'] = 0;
                break;
            case Allegro\Kernel\Keyboard\Key::ALLEGRO_KEY_DOWN:
                $keys['DOWN'] = 0;
                break;
            case Allegro\Kernel\Keyboard\Key::ALLEGRO_KEY_RIGHT:
                $keys['RIGHT'] = 0;
                break;
            case Allegro\Kernel\Keyboard\Key::ALLEGRO_KEY_LEFT:
                $keys['LEFT'] = 0;
                break;
            case Allegro\Kernel\Keyboard\Key::ALLEGRO_KEY_SPACE:
                $keys['SPACE'] = 0;
                break;
        }
    } elseif ($event->type === 42) {
        $doexit = 1;
    }

    $allegroPrimitives->al_flip_display();
    $allegroPrimitives->al_clear_to_color($allegroPrimitives->al_map_rgb(0, 0, 0));

}

$timer->destroy();
$display->destroy();
$allegro->al_destroy_event_queue($event_queue);

