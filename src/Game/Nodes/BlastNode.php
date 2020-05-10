<?php

namespace App\Game\Nodes;

use App\Game\Direction;
use App\Object\Blast;
use App\Object\Spaceship;

class BlastNode
{
    public array $active = [];

    public function create(Direction $direction, Spaceship $spaceship): bool
    {
        if ($direction->space) {
            $blast = new Blast($spaceship);
            if (empty($this->active)) {
                $this->active[] = $blast;
                return true;
            }
            if (isset($this->active[count($this->active) - 1])) {
                $active = $this->active[count($this->active) - 1];
                if ($blast->sx / $active->sx > 1.2 || $blast->sy / $active->sy > 1.2) {
                    $this->active[] = $blast;
                    return true;
                }
            }
            unset($blast);
        }
        return false;
    }

    public function calculatePosition()
    {
        foreach ($this->active as $key => $value) {
            if ($value->sx > 700 || $value->sx < 0){
                unset($this->active[$key]);
                continue;
            }
            $value->draw();
            $value->calculateBlastPosition();
        }
    }




}