<?php
/*

Bot for connectfour https://botsarena.tinad.fr/connectFour
by Gnieark https://blog-du-grouik.tinad.fr/ june 2016 
GNU GPL License

    +--+--+--+--+--+--+--+
5   |  |  |  |  |  |  |  |
    +--+--+--+--+--+--+--+
4   |  |  |  |  |  |  |  |
    +--+--+--+--+--+--+--+
3   |  |  |  |  |  |  |  |
    +--+--+--+--+--+--+--+
2   |  |  |  |  |  |  |  |
    +--+--+--+--+--+--+--+
1   |  |  |  |  |  |  |  |
    +--+--+--+--+--+--+--+
0   |  |  |  |  |  |  |  |
    +--+--+--+--+--+--+--+
      0  1  2  3  4  5  6
*/

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST'); 
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

function can_win($line,$myChar){
    //retourne la position du caractere a remplacer dans la ligne pour gagner
    if (strpos($line,"+".$myChar.$myChar.$myChar)  !== false ){
        return strpos($line,"+".$myChar.$myChar.$myChar);
    }
    if (strpos($line,$myChar."+".$myChar.$myChar)  !== false ){
        return strpos($line,$myChar."+".$myChar.$myChar) + 1;
    }
    if (strpos($line,$myChar.$myChar."+".$myChar)  !== false ){
        return strpos($line,$myChar.$myChar."+".$myChar) + 2;
    }
    if (strpos($line,$myChar.$myChar.$myChar."+")  !== false ){
        return strpos($line,$myChar.$myChar.$myChar."+") + 3;
    }
    return false;
}
function can_loose($line,$hisChar,$depth=0;){
    if ($depth == 0){
        //je pourrai perdre aux 2 prochains tours de jeu
        // retourne la place du caractere à remplacer pour éviter ça
        if (strpos($line,"+".$hisChar.$hisChar.$hisChar)  !== false ){
            return strpos($line,"+".$hisChar.$hisChar.$hisChar);
        }
        if (strpos($line,$hisChar."+".$hisChar.$hisChar)  !== false ){
            return strpos($line,$hisChar."+".$hisChar.$hisChar) + 1;
        }
        if (strpos($line,$hisChar.$hisChar."+".$hisChar)  !== false ){
            return strpos($line,$hisChar.$hisChar."+".$hisChar) + 2;
        }
        if (strpos($line,$hisChar.$hisChar.$hisChar."+")  !== false ){
            return strpos($line,$hisChar.$hisChar.$hisChar."+") + 3;
        }
    }else{
        if (strpos($line,"+".$hisChar.$hisChar."+")  !== false ){
            return strpos($line,"+".$hisChar.$hisChar."+");
        }
    }
    return false;
    
}
function should_opponent_win_if_i_play_at($map,$me,$opponent,$colToPlay){
  //j'ouvre l'a possibilité à l'adversaire de jouer au dessus de mon pion
  // est-ce une connerie?
  
  if(($map[4][$colToPlay] == $me) OR ($map[4][$colToPlay] == $opponent)){
    //top of the grid
    return false;
  }
  
  for($y = 0; (($map[$y][$colToPlay] <> "+") && ($map[$y][$colToPlay] <> "-")) && ; $y++){
  }
  
  $map[$y][$colToPlay] = $me;
  $map[$y +1][$colToPlay] = "$opponent";
  $y++;
  if(isset($map[$y +1][$colToPlay])){
    $map[$y +1][$colToPlay] = "+";
  }
  //tester les lignes qui passent pas $y+1,$colToPlay
  
  $loseStr = $opponent.$opponent.$opponent.$opponent;
  //horizontale
  $line="";
  for($x=0; $x < 7; $x++){
    $line.=$map[$y][$x];
  }
  if(strpos($line,$loseStr) !== false){
    return true;
  }
  
  //diagonal /
  $line="";
  if($colToPlay > $y){
    $kx=$colToPlay - $y;
    $ky = 0;
  }else{
    $kx = 0;
    $ky = $y - $colToPlay;
  }
  while(isset($map[$ky][$kx])){
    $line.=$map[$ky][$kx];
    $kx++;
    $ky++;
  }
  
  if(strpos($line,$loseStr) !== false){
    return true;
  }
  
  //diagional \
  $line = "";
  $kx = $colToPlay;
  $ky = $y;
  
  while(isset($map[$ky][$kx])){
    $kx++;
    $ky--;
  }
  while(isset($map[$ky][$kx])){
    $line.=$map[$ky][$kx];
    $kx--;
    $kx++;
  }
  if(strpos($line,$loseStr) !== false){
    return true;
  }
  return false;
}



//replace "" by " ", it will simplify my code.
$in=str_replace('""','"-"',file_get_contents('php://input'));

$params=json_decode($in, TRUE);
switch($params['action']){
	case "init":
		echo '{"name":"Gnieark"}';
		break;
	case "play-turn":
		//find $opponent and clean grid
		for($x = 0; $x < 7 ; $x++){
		  for($y = 0; $y < 6 ; $y++){
		  
                    //find opponent
		    if(($params['board'][$y][$x] <> "-" ) && ($params['board'][$y][$x] <> $params['you'] )){
		      $opponent= $params['board'][$y][$x];
		    }
		    
		    //tester si la case est jouable (s'il y a un support en dessous)
		    if  ($params['board'][$y][$x] == "-" ){   
                        //AND (($y==0) OR ($params['board'][$y - 1][$x] !== "-"))
                        //){
                        //la case est jouable, je la marque par un "+"
			if($y == 0){
                        	$params['board'][$y][$x] = "+";
			}elseif(($params['board'][$y -1 ][$x] !== "-") AND ($params['board'][$y -1 ][$x] !== "+")){
				$params['board'][$y][$x] = "+";
			}else{}
		    }
		  }
		}
		if((!isset($opponent)) && ($params['you'] == "X")){
		  $opponent="O";
		}elseif(!isset($opponent)){
		  $opponent="X";
		}

		//transformer la grille en lignes horizontales, verticales et diagonales
		
            
		
		//verticales
		for($x = 0; $x <7; $x ++){
                    $colStr="";
                    for($y = 0; $y <6; $y ++){
                        $colStr.= $params['board'][$y][$x];
                    }
                    if(can_win($colStr,$params['you']) !== false){
                        echo '{"play":'.$x.'}';
                        die;
                    }
                    if (can_loose($colStr,$opponent) !== false){
			$colForNoLose = $x;
	             }
                     if (can_loose($colStr,$opponent,1) !== false){
			$colForNoLose1 = $x;
	             }
		}
		
		//horizontales
		for($y = 0; $y <6; $y ++){
                    $lnStr="";
                    for($x = 0; $x <7; $x ++){
                       $lnStr.= $params['board'][$y][$x]; 
                    }
                    if(can_win($lnStr,$params['you']) !== false){
                        echo '{"play":'.can_win($lnStr,$params['you']).'}';
                        die;
                    }
                    if (can_loose($lnStr,$opponent) !== false){
                      $colForNoLose = can_loose($lnStr,$opponent);
                    }
                    if (can_loose($lnStr,$opponent,1) !== false){
                      $colForNoLose1 = can_loose($lnStr,$opponent,1);
                    }
                    
		}
		
		
		//tester seulement les diagonales >= 4 cases
		
		for ($k = 0; $k < 4; $k ++){
		
                    //diagonale /
                    $diagStr="";
                    for($x=$k , $y=0; isset($params['board'][$y][$x]); $x++, $y++){
                        $diagStr.=$params['board'][$y][$x];
                    }
                   if(can_win($diagStr,$params['you']) !== false){
                        echo '{"play":'.($k + can_win($diagStr,$params['you'])).'}';
                        die;
                    }
                    if (can_loose($diagStr,$opponent) !== false){
                     $colForNoLose = $k + can_loose($diagStr,$opponent);
		    }
                    if (can_loose($diagStr,$opponent,1) !== false){
                     $colForNoLose1 = $k + can_loose($diagStr,$opponent,1);
		    }
                    //diagonale \
                    $diagStr="";
                    for($x=$k , $y=5; isset($params['board'][$y][$x]); $x++, $y--){
                    $diagStr.=$params['board'][$y][$x];
                    }
                   if(can_win($diagStr,$params['you']) !== false){
                        echo '{"play":'.($k + can_win($diagStr,$params['you'])).'}';
                        die;
                    }
                    if (can_loose($diagStr,$opponent) !== false){
			$colForNoLose = ($k + can_loose($diagStr,$opponent));
                    } 
                    if (can_loose($diagStr,$opponent,1) !== false){
			$colForNoLose1 = ($k + can_loose($diagStr,$opponent,1));
                    } 
                    
                    
		}		
		for ($k = 0; $k < 3; $k ++){
                    //diagonale /
                    $diagStr="";
                    for($x = 0, $y = $k ; isset($params['board'][$y][$x]); $x++, $y++){
                        $diagStr.=$params['board'][$y][$x];
                    }
                   if(can_win($diagStr,$params['you']) !== false){
                        echo '{"play":'.can_win($diagStr,$opponent).'}';
                        die;
                    }
                    if (can_loose($diagStr,$opponent) !== false){
			 $colForNoLose =  can_loose($diagStr,$opponent);
                    }
                    if (can_loose($diagStr,$opponent,1) !== false){
			 $colForNoLose1 =  can_loose($diagStr,$opponent,1);
                    }
		}
		for ($k = 3 ; $k < 6 ; $k++){

                    //diagonales \
                    $diagStr="";
                    for($x=0 , $y=$k; isset($params['board'][$y][$x]); $x++, $y--){
                        $diagStr.=$params['board'][$y][$x];
                    }
                   if(can_win($diagStr,$params['you']) !== false){
                        echo '{"play":'.can_win($diagStr,$params['you']).'}';
                        die;
                    }
                    if (can_loose($diagStr,$opponent) !== false){
			 $colForNoLose = can_loose($diagStr,$opponent);
                    }
                    if (can_loose($diagStr,$opponent,1) !== false){
			 $colForNoLose1 = can_loose($diagStr,$opponent,1);
                    }
                   
                }

                //si j'arrive là, je ne gagne pas à ce tour
                if(isset($colForNoLose)){
                    echo '{"play":'.$colForNoLose.'}';
                    die;               
                }elseif(isset($colForNoLose1)){
                     echo '{"play":'.$colForNoLose1.'}';
                    die;                    
                }

                //still there? random
		
		$colAvailable=array();
		//dont play on full colomns and where it is dangerous
		for($i=0;$i<7;$i++){
		
  			if((($params['board'][5][$i] == "+") OR ($params['board'][5][$i] == "-"))
                        AND (!should_opponent_win_if_i_play_at($params['board'],$params['you'],$opponent,$i)))
  			{
    				$colAvailable[]=$i;
  			}
		}
		
		if(count($colAvailable) == 0){
		
                    for($i=0;$i<7;$i++){
		
                            if(($params['board'][5][$i] == "+") OR ($params['board'][5][$i] == "-"))
                            {
                                    $colAvailable[]=$i;
                            }
                    }
		
		}
		
		shuffle($colAvailable);
		echo '{"play":'.$colAvailable[0].'}';

		
		break;
	default:
		break;
}
