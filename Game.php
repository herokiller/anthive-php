<?php

class Game {
    private $id;
    private $ants;
    private $map;
    private $w;
    private $h;
    private $b;
    private $response;

    const ACTIONS = ["move","eat","load","unload"];
    const DIRECTIONS = ["up","down","right","left"];

    const di = [1, -1, 0, 0];
    const dj = [0, 0, 1, -1];

    public function init($id, $ants, $map, $h, $w) {
        $this->id = $id;
        $this->ants = $ants;
        $this->map = $map;
        $this->w = $w;
        $this->h = $h;
        $this->response = [];
    }


    private function hiveNearby($ant) {
        $y = $ant['y'];
        $x = $ant['x'];

        for ( $i = 0; $i < 4; $i++ )
            if ($this->isMyHive($y + self::di[$i], $x + self::dj[$i]))
                return 1;

        return 0;
    }

    private function checkHive($antId, $y, $x) {

        if ($this->isMyHive($y, $x) && (!$this->isAnt($y, $x))) {

            $x0 = $this->ants[$antId]['x'];
            $y0 = $this->ants[$antId]['y'];

            if (($x - $x0 == 1) && ($y0 == $y)) {
                $this->assign($antId, 'unload', 'right');
                return 1;
            }

            if (($x0 - $x == 1) && ($y0 == $y)) {
                $this->assign($antId, 'unload', 'left');
                return 1;
            }

            if (($y - $y0 == 1) && ($x0 == $x)) {
                $this->assign($antId, 'unload', 'down');
                return 1;
            }

            if (($y0 - $y == 1) && ($x0 == $x)) {
                $this->assign($antId, 'unload', 'up');
                return 1;
            }

            //
            if ($y0 < $y) {
                if (($x0 - $x > 0) && ($this->isEmpty($y0, $x0 - 1))) {
                    $this->assign($antId, 'move', 'left');
                    return 1;
                }
                if (($x - $x0 > 0) && ($this->isEmpty($y0, $x0 + 1))) {
                    $this->assign($antId, 'move', 'right');
                    return 1;
                }
            }

            if ($y0 >= $y) {
                if (($x - $x0 > 0) && ($this->isEmpty($y0, $x0 + 1))) {
                    $this->assign($antId, 'move', 'right');
                    return 1;
                }
                if (($x0 - $x > 0) && ($this->isEmpty($y0, $x0 - 1))) {
                    $this->assign($antId, 'move', 'left');
                    return 1;
                }
            }

            if ($x0 < $x) {
                if (($y - $y0 > 0) && ($this->isEmpty($y0 + 1, $x0))) {
                    $this->assign($antId, 'move', 'down');
                    return 1;
                }
                if (($y0 - $y > 0) && ($this->isEmpty($y0 - 1, $x0))) {
                    $this->assign($antId, 'move', 'up');
                    return 1;
                }
            }

            if ($x0 >= $x) {
                if (($y0 - $y > 0) && ($this->isEmpty($y0 - 1, $x0))) {
                    $this->assign($antId, 'move', 'up');
                    return 1;
                }
                if (($y - $y0 > 0) && ($this->isEmpty($y0 + 1, $x0))) {
                    $this->assign($antId, 'move', 'down');
                    return 1;
                }
            }

            //if straight path is blocked

            if ($x0 == $x){
                if ($y0 > $y) {
                    if ($this->isEmpty($y0, $x0 + 1)) {
                        $this->assign($antId, 'move', 'right');
                        return 1;
                    }
                    if ($this->isEmpty($y0, $x0 - 1)) {
                        $this->assign($antId, 'move', 'left');
                        return 1;
                    }
                } else {
                    if ($this->isEmpty($y0, $x0 - 1)) {
                        $this->assign($antId, 'move', 'left');
                        return 1;
                    }

                    if ($this->isEmpty($y0, $x0 + 1)) {
                        $this->assign($antId, 'move', 'right');
                        return 1;
                    }
                }

            }

            if ($y0 == $y) {
                if ($x0 > $x) {
                    if ($this->isEmpty($y0 - 1, $x0)) {
                        $this->assign($antId, 'move', 'up');
                        return 1;
                    }

                    if ($this->isEmpty($y0 + 1, $x0)) {
                        $this->assign($antId, 'move', 'down');
                        return 1;
                    }
                } else {
                    if ($this->isEmpty($y0 + 1, $x0)) {
                        $this->assign($antId, 'move', 'down');
                        return 1;
                    }
                    if ($this->isEmpty($y0 - 1, $x0)) {
                        $this->assign($antId, 'move', 'up');
                        return 1;
                    }
                }

            }
        }

        return 0;
    }

    private function checkFood($antId, $y, $x) {

        if ($this->isMyHive($y, $x))
            return 0;

        if ($this->isUnassignedFood($y, $x)) {
            $x0 = $this->ants[$antId]['x'];
            $y0 = $this->ants[$antId]['y'];

            if (($x - $x0 == 1) && ($y0 == $y)) {
                $this->assign($antId, 'load', 'right');
                return 1;
            }

            if (($x0 - $x == 1) && ($y0 == $y)) {
                $this->assign($antId, 'load', 'left');
                return 1;
            }

            if (($y - $y0 == 1) && ($x0 == $x)) {
                $this->assign($antId, 'load', 'down');
                return 1;
            }

            if (($y0 - $y == 1) && ($x0 == $x)) {
                $this->assign($antId, 'load', 'up');
                return 1;
            }

            //
            if ($y0 > $y) {
                if (($x - $x0 > 0) && ($this->isEmpty($y0, $x0 + 1))) {
                    $this->assign($antId, 'move', 'right');
                    return 1;
                }
                if (($x0 - $x > 0) && ($this->isEmpty($y0, $x0 - 1))) {
                    $this->assign($antId, 'move', 'left');
                    return 1;
                }
            }

            if ($y0 <= $y) {
                if (($x0 - $x > 0) && ($this->isEmpty($y0, $x0 - 1))) {
                    $this->assign($antId, 'move', 'left');
                    return 1;
                }
                if (($x - $x0 > 0) && ($this->isEmpty($y0, $x0 + 1))) {
                    $this->assign($antId, 'move', 'right');
                    return 1;
                }
            }


            if ($x0 < $x) {
                if (($y - $y0 > 0) && ($this->isEmpty($y0 + 1, $x0))) {
                    $this->assign($antId, 'move', 'down');
                    return 1;
                }

                if (($y0 - $y > 0) && ($this->isEmpty($y0 - 1, $x0))) {
                    $this->assign($antId, 'move', 'up');
                    return 1;
                }
            }

            if ($x0 >= $x) {
                if (($y0 - $y > 0) && ($this->isEmpty($y0 - 1, $x0))) {
                    $this->assign($antId, 'move', 'up');
                    return 1;
                }

                if (($y - $y0 > 0) && ($this->isEmpty($y0 + 1, $x0))) {
                    $this->assign($antId, 'move', 'down');
                    return 1;
                }
            }

            //if straight path is blocked
            if ($x0 == $x){
                if ($y0 > $y) {
                    if ($this->isEmpty($y0, $x0 + 1)) {
                        $this->assign($antId, 'move', 'right');
                        return 1;
                    }
                    if ($this->isEmpty($y0, $x0 - 1)) {
                        $this->assign($antId, 'move', 'left');
                        return 1;
                    }
                } else {
                    if ($this->isEmpty($y0, $x0 - 1)) {
                        $this->assign($antId, 'move', 'left');
                        return 1;
                    }

                    if ($this->isEmpty($y0, $x0 + 1)) {
                        $this->assign($antId, 'move', 'right');
                        return 1;
                    }
                }

            }

            if ($y0 == $y) {
                if ($x0 > $x) {
                    if ($this->isEmpty($y0 - 1, $x0)) {
                        $this->assign($antId, 'move', 'up');
                        return 1;
                    }

                    if ($this->isEmpty($y0 + 1, $x0)) {
                        $this->assign($antId, 'move', 'down');
                        return 1;
                    }
                } else {
                    if ($this->isEmpty($y0 + 1, $x0)) {
                        $this->assign($antId, 'move', 'down');
                        return 1;
                    }
                    if ($this->isEmpty($y0 - 1, $x0)) {
                        $this->assign($antId, 'move', 'up');
                        return 1;
                    }

                }
            }
            $this->b[$y][$x] = 1;
        }

        return 0;
    }

    private function assign($antId, $action, $direction) {
        $ant = $this->ants[$antId];

        //small hack temp
        /*
        if (($ant['health'] < 5) && ($action == 'load'))
            $action = 'eat';
        */
        $this->response[$antId] = [
            'act' => $action,
            'dir' => $direction
        ];

        $x = $this->ants[$antId]['x'];
        $y = $this->ants[$antId]['y'];

        if (($action == 'load') || ($action == 'eat')) {
            if ($direction == 'up')
                $this->map[$x - 1][$y]['food']--;
            if ($direction == 'down')
                $this->map[$x + 1][$y]['food']--;
            if ($direction == 'left')
                $this->map[$x][$y - 1]['food']--;
            if ($direction == 'right')
                $this->map[$x][$y + 1]['food']--;
        }
        if (($action == 'load') || ($action == 'unload') || ($action == 'eat')) {
            $this->b[$x][$y] = 1;
        }

        if ($action == 'move') {
            if ($direction == 'up')
                $this->b[$x-1][$y] = 1;

            if ($direction == 'down')
                $this->b[$x+1][$y] = 1;

            if ($direction == 'right')
                $this->b[$x][$y+1] = 1;

            if ($direction == 'left')
                $this->b[$x][$y-1];
        }
    }

    private function inside($i, $j) {
        return (($i < $this->h) && ($i >= 0) && ($j < $this->w) && ($j >= 0));
    }

    public function isMyHive($i, $j) {
        return (isset($this->map[$i][$j]['hive']) && ($this->map[$i][$j]['hive'] == $this->id));
    }

    public function isEmpty($i, $j) {
        return ($this->inside($i, $j) && (!isset($this->map[$i][$j]['food'])) && (!isset($this->map[$i][$j]['ant'])));
    }

    public function isAnt($i, $j) {
        return ($this->inside($i, $j) && (isset($this->map[$i][$j]['ant'])));
    }

    public function isFood($i, $j) {
        return (isset($this->map[$i][$j]['food']) && ($this->map[$i][$j]['food'] > 0));
    }

    public function isUnassignedFood($i, $j) {
        return ($this->inside($i, $j) && ($this->isFood($i, $j)) && !$this->b[$i][$j]);
    }

    //for future
    public function moveTowards($andId, $x, $y) {

    }

    public function hiveSize() {
        $size = 0;
        for ($i = 0; $i < $this->h; $i++ )
            for ($j = 0; $j < $this->w; $j++ ) {
                if (isset($this->map[$i][$j]['hive']) && ($this->map[$i][$j]['hive'] == $this->id))
                    $size++;
            }
        return $size;
    }

    public function foodLeft() {
        $food = 0;
        for ($i = 0; $i < $this->h; $i++ )
            for ($j = 0; $j < $this->w; $j++ )
                if (isset($this->map[$i][$j]['food']) && ($this->map[$i][$j]['food'] > 0))
                    $food += $this->map[$i][$j]['food'];
        return $food;
    }

    public function getMoves() {
        $this->b = [];
        for ( $i = 0; $i < $this->h; $i++ )
            for ( $j = 0; $j < $this->w; $j++ )
                    $this->b[$i][$j] = 0;

        $size = $this->hiveSize();
        $food = $this->foodLeft();

        $counter = 0;
        foreach ($this->ants as $antId => $ant) {
            $counter++;

            if (($ant['payload'] == 9) || (($ant['payload'] > 0) && ($this->hiveNearby($ant))) || ($food == 0)) {
                //if standing on the hive itself TODO refactor?
                if (($size == 1) && ($this->isMyHive($ant['y'], $ant['x']))) {
                    if ( $this->isEmpty($ant['y'] + 1, $ant['x']) ) {
                        $this->response[$antId] = [
                            'act' => 'move',
                            'dir' => 'down'
                        ];
                        $this->b[$ant['y']+1][$ant['x']] = 1;
                        continue;
                    }

                    if ( $this->isEmpty($ant['y'], $ant['x'] + 1) ) {
                        $this->response[$antId] = [
                            'act' => 'move',
                            'dir' => 'right'
                        ];
                        $this->b[$ant['y']][$ant['x'] + 1] = 1;
                        continue;
                    }

                    if ( $this->isEmpty($ant['y'] - 1, $ant['x']) ) {
                        $this->response[$antId] = [
                            'act' => 'move',
                            'dir' => 'up'
                        ];
                        $this->b[$ant['y'] - 1][$ant['x']] = 1;
                        continue;
                    }

                    if ( $this->isEmpty($ant['y'], $ant['x'] - 1) ) {
                        $this->response[$antId] = [
                            'act' => 'move',
                            'dir' => 'left'
                        ];
                        $this->b[$ant['y']][$ant['x'] - 1] = 1;
                        continue;
                    }
                }

                for ( $step = 1; $step <= $this->w + $this->h; $step++ ) {
                    if (isset($this->response[$antId]))
                        break;


                    for ( $i = 0; $i < $step; $i++ ) {

                        $x = $ant['x'] + $i;
                        $y = $ant['y'] + $step - $i;


                        if ($this->checkHive($antId, $y, $x))
                            break;

                        $x = $ant['x'] + $step - $i;
                        $y = $ant['y'] - $i;

                        if ($this->checkHive($antId, $y, $x))
                            break;

                        $x = $ant['x'] - $i;
                        $y = $ant['y'] - $step + $i;

                        if ($this->checkHive($antId, $y, $x))
                            break;

                        $x = $ant['x'] - $step + $i;
                        $y = $ant['y'] + $i;

                        if ($this->checkHive($antId, $y, $x))
                            break;
                    }
                }
            }
        }

        $counter = 0;
        foreach ($this->ants as $antId => $ant) {

            $counter++;

            /*
            if ($counter > 1)
                break;
            */

            for ( $step = 1; $step <= $this->w + $this->h; $step++ ) {
                if (isset($this->response[$antId]) || ($ant['payload'] == 9))
                    break;

                for ($i = 0; $i < $step; $i++ ) {
                    //check down - right
                    $x = $ant['x'] + $i;
                    $y = $ant['y'] + $step - $i;

                    if ($this->checkFood($antId, $y, $x))
                        break;

                    //check down - left
                    $x = $ant['x'] + $step - $i;
                    $y = $ant['y'] - $i;

                    if ($this->checkFood($antId, $y, $x))
                        break;

                    //check up - right

                    $x = $ant['x'] - $i;
                    $y = $ant['y'] - $step + $i;

                    if ($this->checkFood($antId, $y, $x))
                        break;
                    //check up - left

                    $x = $ant['x'] - $step + $i;
                    $y = $ant['y'] + $i;

                    if ($this->checkFood($antId, $y, $x))
                        break;
                }
            }
        }

        return $this->response;
    }
}
