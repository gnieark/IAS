<?php
/*
Bot returning a 500 error code (in order to test botsarena comportment if a bot fails
by Gnieark https://blog-du-grouik.tinad.fr/ fev 2017 
GNU GPL License
*/
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST'); 
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
$in=file_get_contents('php://input');
$params=json_decode($in, TRUE);

switch($params['action']){
	case "init":
		echo '{"name":"Gnieark"}';
		break;
	case "play-turn":
		header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);	
		break;
	default:
		break;
}



