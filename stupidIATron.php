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
		/*{
		"game-id":"1647",
		"action":"play-turn",
		"game":"tron",
		"board":[
		  [
		    [425,763],[424,763],[423,763],[422,763],[421,763],[420,763],[419,763]
		   ],
		   [
		    [858,501],[857,501],[856,501],[855,501],[854,501],[853,501],[852,501]
		   ]
		 ],
		"player-index":0,
		"players":2}
		*/
		
		//put all non empty coords on array
		$busyCells = array();
		
		foreach($params['board'] as $tail){
		  foreach($tail as $coord){
		    $busyCells[] = $coord[0].",".$coord[1];
		  }
		}
		
		//get my head coords
		$myCoords = end($params['board'][$params['player-index']]);
		
		$x = $myCoords[0];
		$y = $myCoords[1];
		
		$availablesDirs = array();
		if (!in_array(($x + 1).",".$y, $busyCells)){
		  $availablesDirs[] = "x-";
		}
		if (!in_array(($x -1 ).",".$y, $busyCells)){
		  $availablesDirs[] = "x+";
		}
		if (!in_array($x.",".($y + 1), $busyCells)){
		  $availablesDirs[] = "y-";
		}
		if (!in_array($x.",".($y - 1), $busyCells)){
		  $availablesDirs[] = "y+";
		}
		
		if(count($availablesDirs) == 0){
		  echo '{"play":"x+","comment":"I Loose"}';
		}else{
		  shuffle($availablesDirs);
		  echo '{"play":"'.$availablesDirs[0].'"}';
		}
		//error_log(json_encode($availablesDirs));
		break;
	default:
		break;
}