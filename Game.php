<?php

class Game {
    private $ants;
    private $map;
    private $w;
    private $h;
    private $b;

    const ACTIONS = ["move","eat","load","unload"];
    const DIRECTIONS = ["up","down","right","left"];

    public function init($ants, $map, $h, $w) {
        $this->ants = $ants;
        $this->map = $map;
        $this->w = $w;
        $this->h = $h;
    }

    private function inside($i, $j) {
        return (($i < $this->h) && ($i >= 0) && ($j < $this->w) && ($j >= 0));
    }

    public function isEmpty($i, $j) {
        return ((!isset($this->map[$i][$j]['food'])) && (!isset($this->map[$i][$j]['ant'])));
    }

    public function isFood($i, $j) {
        return (isset($this->map[$i][$j]['food']) && ($this->map[$i][$j]['food'] > 0));
    }

    public function isUnassignedFood($x, $y) {
        return ($this->inside($x, $y) && ($this->isFood($x, $y)) && !$this->b[$x][$y]);
    }

    public function getMoves() {
        //dummy
        $respose = [];
        /*
        foreach ($this->ants as $antId => $ant){
            $response[$antId] = array(
                "act"=>ACTIONS[rand(0,3)],
                "dir"=>DIRECTIONS[rand(0,3)]
            );
        }
        */

        //
        $this->b = [];
        for ( $i = 0; $i < $this->h; $i++ )
            for ( $j = 0; $j < $this->w; $j++ )
                    $this->b[$i][$j] = 0;
        
        foreach ($this->ants as $antId => $ant) {
            for ( $step = 1; $step <= max($this->w, $this->h); $step++ ) {
                if (isset($response[$antId]))
                    break;

                for ($i = 0; $i < $step; $i++ ) {

                    //check down - right
                    $x = $ant['x'] + $i;
                    $y = $ant['y'] + $step - $i;
                    //print_r('checking 1: ' . $x . ' ' . $y . "\n");
                    //print_r($this->map[$x][$y]);
                    if ($this->isUnassignedFood($x, $y)) {
                        if ($step == 1) {
                            $response[$antId] = [
                                'act' => 'load',
                                'dir' => 'right'
                            ];
                        } else {
                            if (($i > 0 ) && ($this->isEmpty($ant['x'] + 1, $ant['y']))) {
                                $response[$antId] = [
                                    'act' => 'move',
                                    'dir' => 'down'
                                ];
                            } else if (($step - $i > 0) && ($this->isEmpty($ant['x'], $ant['y'] + 1))) {
                                $response[$antId] = [
                                    'act' => 'move',
                                    'dir' => 'right'
                                ];
                            }
                        }

                        if (isset($response[$antId])) {
                            $this->b[$x][$y] = 1;
                            break;
                        }
                    }

                    //check down - left
                    $x = $ant['x'] + $step - $i;
                    $y = $ant['y'] - $i;

                    //print_r('checking 2: ' . $x . ' ' . $y . "\n");
                    //print_r($this->map[$x][$y]);

                    if ($this->isUnassignedFood($x, $y)) {
                        if ($step == 1) {
                            $response[$antId] = [
                                'act' => 'load',
                                'dir' => 'down'
                            ];
                        } else {
                            if (($i > 0) && ($this->isEmpty($ant['x'], $ant['y'] - 1))) {
                                $response[$antId] = [
                                    'act' => 'move',
                                    'dir' => 'right'
                                ];
                            } else if (($step - $i > 0) && ($this->isEmpty($ant['x'] + 1, $ant['y']))) {
                                $response[$antId] = [
                                    'act' => 'move',
                                    'dir' => 'down'
                                ];
                            }
                        }
                        if (isset($response[$antId])) {
                            $this->b[$x][$y] = 1;
                            break;
                        }
                    }

                    //check up - right
                    
                    $x = $ant['x'] - $i; 
                    $y = $ant['y'] - $step + $i;
    
                    if ($this->isUnassignedFood($x, $y)) {
                        if ($step == 1) {
                            $response[$antId] = [
                                'act' => 'load',
                                'dir' => 'left'
                            ];
                        } else {
                            if (($i > 0) && ($this->isEmpty($ant['x'] - 1, $ant['y']))) {
                                $response[$antId] = [
                                    'act' => 'move',
                                    'dir' => 'up'
                                ];
                            } else if (($step - $i > 0) && ($this->isEmpty($ant['x'], $ant['y'] - 1))) {
                                $response[$antId] = [
                                    'act' => 'move',
                                    'dir' => 'left'
                                ];
                            }
                        }

                        if (isset($response[$antId])) {
                            $this->b[$x][$y] = 1;
                            break;
                        }
                    }
                    //check up - left

                    $x = $ant['x'] - $step + $i;
                    $y = $ant['y'] + $i;
                    
                    if ($this->isUnassignedFood($x, $y)) {
                        if ($step == 1) {
                            $response[$andId] = [
                                'act' => 'load',
                                'dir' => 'up'
                            ];
                        } else {
                            if (($i > 0) && ($this->isEmpty($ant['x'], $ant['y'] + 1))) {
                                $response[$andId] = [
                                    'act' => 'move',
                                    'dir' => 'right'
                                ];
                            } else if (($step - $i > 0) && ($this->isEmpty($ant['x'] - 1, $ant['y']))) {
                                $response[$antId] = [
                                    'acnt' => 'move',
                                    'dir' => 'up'
                                ];
                            }
                        }

                        if (isset($response[$antId])) {
                            $this->b[$x][$y] = 1;
                            break;
                        }
                    }
                }
            }
        }

        return $response;
    }
}

