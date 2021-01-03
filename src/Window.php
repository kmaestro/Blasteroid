<?php

declare(strict_types=1);

namespace App;

use Allegro\Allegro;

class Window
{
    private $display;

    private $ffi;

    public function __construct(
        private int $width,
        private int $height,
    ) {
        $this->ffi = Allegro::getInstance()->info->ffi;
        var_dump($this->ffi);
        $this->display = $this->ffi->al_create_display($this->width, $this->height);
    }

    public function display()
    {
        return $this->display;
    }

    public function destroy()
    {
        $this->ffi->al_destroy_display($this->display);
    }
}
