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
		//error_log ("plop");
		foreach($params['board'] as $tail){
		  foreach($tail as $coord){
		    $busyCells[] = new Coords($coord[0],$coord[1]);
		  }
		}

		//get my head coords
		$myCoords =  new Coords($params['board'][$params['player-index']][0][0],$params['board'][$params['player-index']][0][1]);
		
		
		
		$directions = array("x+","x-","y+","y-");
		$maxLenght = -1;
		foreach($directions as $direction){
		  $dir = Direction::make($direction);
		  //compter le nombre de cases libres dans cette direction
		  //boucle sans fin!
		  $count = 0;
		  $tempCoords = $myCoords->addDirection($dir);
		  while(
			  (!in_array_objet($tempCoords,$busyCells))
			  && ($tempCoords !== false)
			  //&& ($tempCoords !== 0) //php sucks
		  ){
		    //error_log($tempCoords->x."|".$tempCoords->y."|".$count);
		    $tempCoords = $tempCoords->addDirection($dir);
		    $count++;
		  }
		  
		  if( $count > $maxLenght ){
		    $bestChoice = $direction;
		    $maxLenght = $count;
		  }  
		}
		echo '{"play":"'.$bestChoice .'"}';
		//error_log($bestChoice);
		
		break;
	default:
		break;
}