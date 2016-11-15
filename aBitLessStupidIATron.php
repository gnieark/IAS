<?php

/*
* stupid IA for tron
* but less stupid in order to test the arena code
*
* Copy left Gnieark https://blog-du-grouik.tinad.fr 2016
* GNU GPL V3 license
*/

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST'); 
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');


//load classes
include ("incTron/Coords.php");
include ("incTron/Direction.php");


$in=file_get_contents('php://input');

$params=json_decode($in, TRUE);
function in_array_objet($searched,$array){
  /*
  *Because( in_array php function doesn't works if array contains objects)
  */
  
  foreach($array as $obj){
    if ($searched == $obj) return true;
  }
  return false;
}

function get_available_dirs($busyCells,$myCoords){

  $directions =  array(
    Direction::make("x+"),
    Direction::make("x-"),
    Direction::make("y+"),
    Direction::make("y-")
  );

  $availablesDirs = array();
  foreach ($directions as $dirObj){
    if(!in_array_objet($myCoords->addDirection($dirObj),$busyCells, TRUE)){
      $availablesDirs[] = $dirObj;
    }
  }
  
  return $availablesDirs;
}

function scoreDirection($busyCells,$headPOS,$dir){
  $newBusyCells = $busyCells;
  $newBusyCells[] = $headPOS->addDirection($dir);
  return count(get_available_dirs($newBusyCells,$headPOS->addDirection($dir)));
}


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
		    $busyCells[] = new Coords($coord[0],$coord[1]);
		  }
		}

		//get my head coords
		$myCoords =  new Coords($params['board'][$params['player-index']][0][0],$params['board'][$params['player-index']][0][1]);
		$availablesDirs = get_available_dirs($busyCells,$myCoords);
		
		
		//score them
		$majoredAvailableDirs = array();
		foreach($availablesDirs as $dir){
		  $score = scoreDirection($busyCells,$myCoords,$dir);
		  for($i = 0; $i < $score * 10; $i++){
		    $majoredAvailableDirs[] = $dir;
		  }
		}
		
		
		if(count($majoredAvailableDirs) == 0){
		  echo '{"play":"x+","comment":"I Loose"}';
		  error_log("i ll loose");
		}else{
		  shuffle($majoredAvailableDirs);
		  echo '{"play":"'.$majoredAvailableDirs[0].'"}';
		  
		  //error_log(json_encode($majoredAvailableDirs));
		}
		
		break;
	default:
		break;
}