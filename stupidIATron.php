<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST'); 
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

/*
* stupid IA for tron
*/
$in=file_get_contents('php://input');
$params=json_decode($in, TRUE);
switch($params['action']){
	case "init":
		echo '{"name":"Stupid AI"}';
		break;
	case "play-turn":
		//to do
		//{"game-id":"","action":"play-turn","game":"tron","board":[[[619,240]],[[329,353]]],"player-index":0,"players":2}
		
		//put all non empty coords on array
		$busyCells = array();
		
		foreach($params['board'] as $tails){
		  foreach($tails as $coords){
		    $busyCells[] = $coords;
		  }
		}
		//get my head coords
		$myCoords = end($params['board'][$params['player-index']]);
		list($x,$y) = explode(",",$myCoords);
		
		$availablesDirs = array();
		if in_array((($x + 1).",".$y), $busyCells){
		  $availablesDirs[] = "x+";
		}
		if in_array((($x -1 ).",".$y), $busyCells){
		  $availablesDirs[] = "x-";
		}
		if in_array(($x.",".($y + 1)), $busyCells){
		  $availablesDirs[] = "y+";
		}
		if in_array(($x.",".($y - 1)), $busyCells){
		  $availablesDirs[] = "y-";
		}
		
		if(count($availablesDirs) == 0){
		  echo '{"play":"x+","comment":"I Loose"}';
		}else{
		  shuffle($availablesDirs);
		  echo '{"play":"'.$availablesDirs[0].'","comment":"I Loose"}';
		}
		
		break;
	default:
		break;
}
