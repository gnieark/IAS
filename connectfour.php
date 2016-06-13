<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST'); 
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

/*
* stupid IA for battle ship
* choose by random a free column
*/

function score($board,$me,$opponent,$colToPlay,$depth){
  
  $newBoard = $board;
  //add cell
  for($y = 0; $board[$y][$colToPlay] <> "+"; $y++){
  }
  $newBoard[$y][$colToPlay] = $me;
  
  //do I win?
    
  $searchValue="";
  for($i = 0 ; $i < 4; $i++){
    $searchValue.=$me;
  }
  //horizontaly
  $line="";
  for ($i=0; $i < 7; $i++){
      $line.=$newBoard[$y][$i]; 
  }
  if(strpos($searchValue,$line)  !== false ){
    return 42 - $depth;
  }
   
  //verticaly
  $line="";
  for ($i=0; $i < 6; $i++){
      $line.=$newBoard[$i][$colToPlay];
  }
  //echo $line."p".$searchValue." ".strpos($searchValue,$line)."|\n";
  if(strpos($searchValue,$line) !== false){
    return 42 - $depth;
  }
  
  
  //diagonal \
  $line="";
  $b = $y + $colToPlay;
  
  if($b < 6){
    $ix = 0;
    $iy = $b;
  }else{
    $ix = $b - 5;
    $iy = 5;
  }
  for($jx = $ix, $jy = $iy; ($jx < 7) && ($jy > -1); $jx++, $jy--){
      $line.=$newBoard[$jy][$jx];
  }
  if(strpos($searchValue,$line) !== false ){
      return 42 - $depth;
  }
  //diagonal /
  $b = $y - $colToPlay;
  if($b > -1){
    $ix = 0;
    $iy = $b;
  }else{
    $iy=0;
    $ix = -$b;
  }
  $line="";

  for ($jx = $ix , $jy = $iy ; ($jx < 7) && ($jy < 6) ; $jx++ , $jy++){
   $line.=$newBoard[$jy][$jx];
  }
  if(strpos($searchValue,$line) !== false){
    return 42 - $depth;
  }
  
  //if grid is full
  $full = true;
  for($i = 0; $i < 7; $i++){
    if($newBoard[5][$i] == "+"){
      $full = false;
      break;
    }
  }
  if($full){
   return 0;
  }
  
  if($depth < 6){
   return -better_col($newBoard,$opponent,$me,$depth + 1);
  }else{
    return 0;
  }
}

function better_col($board,$me,$opponent,$depth){
  $betterScore= -1000;
  $betterCol= -1;
  for( $i = 0; $i < 7; $i++){
    if($board[5][$i] == "+"){
      $sc = score($board,$me,$opponent,$i,$depth);
      if( $sc > $betterScore){
	$betterScore = $sc -$depth;
	$betterCol = $i;
	//echo $i.":".$sc."|";
      }
    }
  }  
  return $betterCol;
}

//replace "" by " ", it will simplify my code.
$in=str_replace('""','"+"',file_get_contents('php://input'));

$params=json_decode($in, TRUE);
switch($params['action']){
	case "init":
		echo '{"name":"Gnieark"}';
		break;
	case "play-turn":
		//find $opponent
		for($x = 0; $x < 7 ; $x++){
		  for($y = 0; $y < 6 ; $y++){
		    if(($params['board'][$y][$x] <> "+" ) && ($params['board'][$y][$x] <> $params['you'] )){
		      $opponent= $params['board'][$y][$x];
		    }
		  }
		}
		if((!isset($opponent)) && ($params['you'] == "X")){
		  $opponent="O";
		}elseif(!isset($opponent)){
		  $opponent="X";
		}

	echo '{"play":"'.better_col($params['board'],$params['you'],$opponent,1).'"}';
		break;
	default:
		break;
}
