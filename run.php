<?php

include 'Game.php';

header('Content-Type: application/json');

$payload = file_get_contents("php://input");

//Hive object from request payload
$hive = json_decode($payload,true);

//print_r($hive['id']);

$game = new Game();
$game->init($hive['ants'], $hive['map']['cells'], $hive['map']['height'], $hive['map']['width']);

$moves = $game->getMoves();
echo json_encode($moves);

?>
