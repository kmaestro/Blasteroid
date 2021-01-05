<?php

declare(strict_types=1);

namespace App;

use AllegroPHP\Allegro\Allegro;

class Timer
{
    private $ffi;

    private $timer;

    public function __construct(
        private int $fps
    ) {
        $this->ffi = Allegro::getInstance()->info->ffi;
        $this->timer = $this->ffi->al_create_timer(1.0 / $this->fps);
    }

    public function start(): void
    {
        $this->ffi->al_start_timer($this->timer);
    }

    public function timer()
    {
        return $this->timer;
    }

    public function destroy()
    {
        $this->ffi->al_destroy_timer($this->timer);
    }
}
