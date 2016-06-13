<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST'); 
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

/*
* stupid IA for battle ship
* choose by random a free column
*/

function score($board,$me,$colToPlay){
  
  $newBoard = $board;
  //add cell
  for($y = 0; $board[$y][$colToPlay] <> ""; $y++){
  }
  $newBoard[$y,$colToPlay] = $me;
  
  //do I win?
  
  
  
  

}

function better_col($board,$me){
  $betterScore= -1000;
  $betterCol= -1;
  for( $i = 0; $i < 7; $i++){
    if($board[5][$i] == ""){
      $sc = score($board,$me,$i)
      if( score($board,$me,$i) > $betterScore){
	$betterScore = $sc;
	$betterCol = $i;
      }
    }
  }
  
  return $i;
}

$in=file_get_contents('php://input');
$params=json_decode($in, TRUE);
switch($params['action']){
	case "init":
		echo '{"name":"Gnieark"}';
		break;
	case "play-turn":
		echo '{"play":"'.better_col($params['board'],$params['you']).'"}';
		break;
	default:
		break;
}