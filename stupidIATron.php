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
		
		//Input JSON exemple:
		/*
		{"game-id":"1784",
		"action":"play-turn",
		"game":"tron",
		"board":[
			  [[490,937],[489,937],[489,938]],
			  [[349,806],[350,806],[350,805]]
		      ],"player-index":0,"players":2}
		*/
		
		//put all non empty coords on array
		$busyCells = array();
		
		foreach($params['board'] as $tail){
		  foreach($tail as $coord){
		    $busyCells[] = $coord[0].",".$coord[1];
		  }
		}
		
		
		//get my head coords
		$myCoords = $params['board'][$params['player-index']][0];
		
		$x = $myCoords[0];
		$y = $myCoords[1];
		
		$availablesDirs = array();
		if (!in_array(($x + 1).",".$y, $busyCells)){
		  $availablesDirs[] = "x+";
		}
		if (!in_array(($x -1 ).",".$y, $busyCells)){
		  $availablesDirs[] = "x-";
		}
		if (!in_array($x.",".($y + 1), $busyCells)){
		  $availablesDirs[] = "y+";
		}
		if (!in_array($x.",".($y - 1), $busyCells)){
		  $availablesDirs[] = "y-";
		}
		
		if(count($availablesDirs) == 0){
		  echo '{"play":"x+","comment":"I Loose"}';
		  error_log("i ll loose");
		}else{
		  shuffle($availablesDirs);
		  echo '{"play":"'.$availablesDirs[0].'"}';
		}
		//error_log(json_encode($availablesDirs));
		break;
	default:
		break;
}