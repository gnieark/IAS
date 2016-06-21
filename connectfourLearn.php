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


//connexion mysql
require_once("config.php");

if (!$lnMySQL=mysqli_connect($config['mysql_host'], $config['mysql_user'], $config['87%!IuQs'])) {
    error(500,'database connexion failed');
    die;
}

mysqli_select_db($lnMySQL,$config['mysql_database']);
mysqli_set_charset($lnMySQL, 'utf8');  

function hash_map($map,$me,$opponent){
  $hashMap = "";
  foreach($map as $line){
    foreach ($line as $cell){
	switch($cell){
	  case $me:
	    $hashMap.="1";
	    break;
	  case $opponnent:
	    $hashMap.="2";
	    break;
	   default: 
	    $hashMap.="0";
	    break;
	}
    }
  }
  
  return base_convert($hashMap, 3, 10);
  
}


function play($map,$colToPlay,$me,$opponnent,$gameid){
  //save the lap on the database and then send the play response
 
  //transform map as string

  

}

function can_win($line,$myChar,$depth=0){
    //retourne la position du caractere a remplacer dans la ligne pour gagner
    $arr=array();
    if($depth == 0){
        if (strpos($line,"+".$myChar.$myChar.$myChar)  !== false ){
            $arr[] = strpos($line,"+".$myChar.$myChar.$myChar);
        }
        if (strpos($line,$myChar."+".$myChar.$myChar)  !== false ){
            $arr[] = strpos($line,$myChar."+".$myChar.$myChar) + 1;
        }
        if (strpos($line,$myChar.$myChar."+".$myChar)  !== false ){
            $arr[] = strpos($line,$myChar.$myChar."+".$myChar) + 2;
        }
        if (strpos($line,$myChar.$myChar.$myChar."+")  !== false ){
            $arr[] = strpos($line,$myChar.$myChar.$myChar."+") + 3;
        }
    }else{
        if (strpos($line,"+".$myChar.$myChar."+")  !== false ){
            $arr[] = strpos($line,"+".$myChar.$myChar."+");
            $arr[] = strpos($line,"+".$myChar.$myChar."+") + 3;
        }
        if (strpos($line,"+".$myChar."+".$myChar)  !== false ){      
            $arr[] = strpos($line,"+".$myChar."+".$myChar);
            $arr[] = strpos($line,"+".$myChar."+".$myChar) + 2;
        }
        if (strpos($line,$myChar."+".$myChar."+")  !== false ){     
            $arr[] = strpos($line,$myChar."+".$myChar."+")  + 1;
            $arr[] = strpos($line,$myChar."+".$myChar."+")  + 3;
        }
    }
    return $arr;
}
function can_loose($line,$hisChar,$depth=0){
    //je pourrai perdre aux 2 prochains tours de jeu
    // retourne la place du caractere à remplacer pour éviter ça
    $arr=array();
    if ($depth == 0){

        if (strpos($line,"+".$hisChar.$hisChar.$hisChar)  !== false ){
            $arr[] = strpos($line,"+".$hisChar.$hisChar.$hisChar);
        }
        if (strpos($line,$hisChar."+".$hisChar.$hisChar)  !== false ){
            $arr[] = strpos($line,$hisChar."+".$hisChar.$hisChar) + 1;
        }
        if (strpos($line,$hisChar.$hisChar."+".$hisChar)  !== false ){
            $arr[] = strpos($line,$hisChar.$hisChar."+".$hisChar) + 2;
        }
        if (strpos($line,$hisChar.$hisChar.$hisChar."+")  !== false ){
	    $arr[] = strpos($line,$hisChar.$hisChar.$hisChar."+") + 3;
        }
        
    }else{
        if (strpos($line,"+".$hisChar.$hisChar."+")  !== false ){
            $arr[] = strpos($line,"+".$hisChar.$hisChar."+");
            $arr[] = strpos($line,"+".$hisChar.$hisChar."+") +3;
        }
        if(strpos($line,"+".$hisChar."+".$hisChar."+")  !== false ){
            $arr[] =  strpos($line,"+".$hisChar."+".$hisChar."+");
	    $arr[] =  strpos($line,"+".$hisChar."+".$hisChar."+") + 2;
	    $arr[] =  strpos($line,"+".$hisChar."+".$hisChar."+") + 4;
        }

    }
    return $arr;
    
}
function array_merge_and_increment_rigth ($arr1,$arr2,$incr){
    foreach($arr2 as $v){
        $arr1[] = $v + $incr;
    }
    return $arr1;
}
function analize($line,$me,$opponent,$isVertical,$decalageX){
    /*
    * Etudie les lignes fournies
    * Joue si une case est gagnante
    * Sinon, "peuple" des variables
    * qui permettront de prendre une décision
    */

    static $colForNoLose = array();
    static $colForNoLose1 = array();
    static $canWinDepth1 = array();
    
    if(count(can_win($line,$me,0)) > 0){
        if($isVertical){
            echo '{"play":'.$decalageX.'}';
        }else{
            echo '{"play":'.(can_win($line,$me,0)[0] + $decalageX).'}';
        }
        die;
        
    }
    
    if (count(can_loose($line,$opponent,0)) > 0){
    
      if($isVertical){
            $colForNoLose[] = $decalageX;
        }else{
            $colForNoLose = array_merge_and_increment_rigth($colForNoLose ,can_loose($line,$opponent,0),$decalageX);
        }
    }
    
    
    if (count(can_loose($line,$opponent,1)) > 0 ){
        if($isVertical){
            $colForNoLose1[] = $decalageX;
        }else{
            $colForNoLose1 = array_merge_and_increment_rigth($colForNoLose1 ,can_loose($line,$opponent,1),$decalageX);
        }
    }
    if(count(can_win($line,$opponent,1)) > 0){
        if($isVertical){
            $canWinDepth1[] = $decalageX;
        }else{
            $canWinDepth1= array_merge_and_increment_rigth($canWinDepth1, can_win($line,$me,1), $decalageX);
        }
    }
    
    return array(
                'colForNoLose'   => $colForNoLose, 
                'colForNoLose1'  => $colForNoLose1, 
                'canWinDepth1'   => $canWinDepth1
                );

}


function should_opponent_win_if_i_play_at($map,$me,$opponent,$colToPlay){
  //j'ouvre l'a possibilité à l'adversaire de jouer au dessus de mon pion
  // est-ce une connerie?
  
  if(($map[4][$colToPlay] == $me) OR ($map[4][$colToPlay] == $opponent)){
    //top of the grid
    return false;
  }
  
  for($y = 0; (($map[$y][$colToPlay] <> "+") && ($map[$y][$colToPlay] <> "-")); $y++){
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
  
  while(isset($map[$ky -1][$kx +1])){
    $kx++;
    $ky--;
  }

  while(isset($map[$ky][$kx])){
    $line.=$map[$ky][$kx];
    $kx--;
    $ky++;
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
                    $choice = analize($colStr,$params['you'],$opponent,true,$x);
		}
		
		//horizontales
		for($y = 0; $y <6; $y ++){
                    $lnStr="";
                    for($x = 0; $x <7; $x ++){
                       $lnStr.= $params['board'][$y][$x]; 
                    }
                    $choice = analize($lnStr,$params['you'],$opponent,false,0);
		}
		
		//tester seulement les diagonales >= 4 cases
		
		for ($k = 0; $k < 4; $k ++){
		
                    //diagonale /
                    $diagStr="";
                    for($x=$k , $y=0; isset($params['board'][$y][$x]); $x++, $y++){
                        $diagStr.=$params['board'][$y][$x];
                    }
                    $choice = analize($diagStr,$params['you'],$opponent,false,$k);
                    
                    //diagonale \
                    $diagStr="";
                    for($x=$k , $y=5; isset($params['board'][$y][$x]); $x++, $y--){
                    $diagStr.=$params['board'][$y][$x];
                    }
                    $choice = analize($diagStr,$params['you'],$opponent,false,$k);
                    
		}		
		for ($k = 0; $k < 3; $k ++){
                    //diagonale /
                    $diagStr="";
                    for($x = 0, $y = $k ; isset($params['board'][$y][$x]); $x++, $y++){
                        $diagStr.=$params['board'][$y][$x];
                    }
                    $choice = analize($diagStr,$params['you'],$opponent,false,0);
		}
		for ($k = 3 ; $k < 6 ; $k++){

                    //diagonales \
                    $diagStr="";
                    for($x=0 , $y=$k; isset($params['board'][$y][$x]); $x++, $y--){
                        $diagStr.=$params['board'][$y][$x];
                    }
                     $choice = analize($diagStr,$params['you'],$opponent,false,0);
                }
                           
                //si j'arrive là, je ne gagne pas à ce tour
                
                //liste des cases possible moins celles à éviter
                
                $colAvailable=array();
		for($i=0;$i<7;$i++){
  			if((($params['board'][5][$i] == "+") OR ($params['board'][5][$i] == "-"))
                        AND (!should_opponent_win_if_i_play_at($params['board'],$params['you'],$opponent,$i)))
  			{
    				$colAvailable[]=$i;
  			}
		}
                if(count($colAvailable) == 0){
                    //on risque de perdre au prochain tour
                    for($i=0;$i<7;$i++){		
                            if(($params['board'][5][$i] == "+") OR ($params['board'][5][$i] == "-")){
                                    $colAvailable[]=$i;
                            }
                    }
                }

                if(count($choice['colForNoLose']) > 0){
                
                    //intersection entre $choice['colForNoLose'] et $colAvailable
                    $intersection = array_intersect($choice['colForNoLose'],$colAvailable);
                    if(count($intersection) > 0){
                        shuffle($intersection);
                        echo '{"play":'.$intersection[0].'}';
                        die;
                    }else{
                        //on pourra perdre au prochain tour, tant pis
                        shuffle($choice['colForNoLose']);
                        echo '{"play":'.$choice['colForNoLose'][0].'}';
                        die;                      
                    }
                    
                }
                
                
                
                $colForNoLose1 = array_unique($choice['colForNoLose1']);
                $canWinDepth1 = array_unique($choice['canWinDepth1']);
                
                $intersection = array_intersect($colForNoLose1,$colAvailable,$canWinDepth1);
                if(count($intersection) > 0){
                    shuffle($intersection);
                    echo '{"play":'.$intersection[0].'}';
                    die;
                }
                
                $intersection = array_intersect($colForNoLose1,$colAvailable);
                if(count($intersection) > 0){
                    shuffle($intersection);
                    echo '{"play":'.$intersection[0].'}';
                    die;
                }
                
                $intersection = array_intersect($colForNoLose1,$canWinDepth1);
                if(count($intersection) > 0){
                    shuffle($intersection);
                    echo '{"play":'.$intersection[0].'}';
                    die;
                }
            
                //still there? random
				
		shuffle($colAvailable);
		echo '{"play":'.$colAvailable[0].'}';

		
		break;
	default:
		break;
}