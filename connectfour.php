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
  $newBoard[$y][$colToPlay] = $me;
  
  //do I win?
    
  $searchValue="";
  for($i = 0 ; $i < 4; $i++){
    $searchValue.=$me;
  }
  
  //horizontaly
  $line="";
  for ($i=0; $i < 7; $i++){
    if ($newBoard[$y][$i] == ""){
      $line.=" ";
    }else{
      $line.=$newBoard[$y][$i];
    }
  }
  if(strpos($searchValue,$line) > -1){
    return 42;
  }
   
  //verticaly
  $line="";
  for ($i=0; $i < 6; $i++){
    if ($newBoard[$i][$colToPlay] == ""){
      $line.=" ";
    }else{
      $line.=$newBoard[$i][$colToPlay];
    }
  }
  if(strpos($searchValue,$line) > -1){
    return 42;
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
    if($newBoard[$jy][$jx] == ""){
      $line.=" ";
    }else{
      $line.=$newBoard[$jy][$jx];
    }
  }
  if(strpos($searchValue,$line) > -1){
    return 42;
  }
  
  
  
  /*
    //diagonal / affin function like y=x+b
    b = parseInt(y - x);
    if( b > -1){
        //first point has x=0
        kx = 0;
        ky = b

    }else{
        //first point has y=0
        ky = 0;
        kx = -b;
    }
    
    var line="";
    var lx , ly;
    for (lx = kx , ly = ky ; (lx < 7) && (ly < 6) ; lx++ , ly++){
       if( board[ly][lx] == ""){
            line += " ";
        }else{
            line += board[ly][lx];
        }   
    }
    
    if (line.indexOf(searchValue) > -1){
      wins(currentPlayer);
      return;
    }
    
   */
  
  
  

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

$in=preg_replace('/""/','" "',file_get_contents('php://input');
echo $in;
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