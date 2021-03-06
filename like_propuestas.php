<?php
include_once "lib/functions.php";

include_once "colpr-config.php";


if(isset ($_POST['cuenta'])){

	$propuesta=$_POST['propuesta'];
	$usuario=$_POST['usuario_id'];
	$cuenta=$_POST['cuenta'];
	
   // Test if the user has done the maximum number of votes
   $queryVotesDone = "select count(*) as v from prog_likes_propuesta where usuario_id = :usuario;";
   // $maxVotesReached = (intval(preparada(array('id'=>userid()),$votesDone)["v"]) >= $maxVotes);
   $votesDone=intval(preparada(array(':usuario'=>userid()),$queryVotesDone)["v"]);
   $maxVotesReached=false; 
   if ($votesDone >= $maxVotes) $maxVotesReached=true;   

   if ($maxVotesReached ) {
   echo "No se puede votar más";
   return;   
   }


	//compruebo que se ha votado ya a esa propuesta:
	try{
	$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
	$consulta = "SELECT propuesta_voto FROM prog_likes_propuesta 
	WHERE propuesta_id = :propuesta AND usuario_id = :usuario;";
	$arrayusuario = array(':propuesta'=>$propuesta, ':usuario'=>$usuario);
	$result = $conn->prepare($consulta);
	$result->execute($arrayusuario);
	foreach($result as $res){
		$propuesta_voto=$res['propuesta_voto'];
	}

   // Si había votado a favor
	if ($propuesta_voto==1){
		$consulta="UPDATE prog_likes_propuesta SET propuesta_voto=-1 
    	WHERE propuesta_id = :propuesta AND usuario_id = :usuario;";
    	$arrayusuario = array(':propuesta'=>$propuesta, ':usuario'=>$usuario);
		listarpreparada($arrayusuario,$consulta);
		$suma="UPDATE prog_propuestas SET sum_likes=sum_likes-2, positivos=positivos-1, negativos=negativos+1 
		WHERE id = :propuesta;";
    	$arrayusuario = array(':propuesta'=>$propuesta);		
		listarpreparada($arrayusuario,$suma);

  // Si había votado en contra
	}elseif ($propuesta_voto==-1){
		$consulta="UPDATE prog_likes_propuesta SET propuesta_voto=1
    	WHERE propuesta_id = :propuesta AND usuario_id = :usuario;";
    	$arrayusuario = array(':propuesta'=>$propuesta, ':usuario'=>$usuario);
		listarpreparada($arrayusuario,$consulta);
		$suma="UPDATE prog_propuestas SET sum_likes=sum_likes+2, positivos=positivos+1, negativos=negativos-1 
		WHERE id = :propuesta;";
    	$arrayusuario = array(':propuesta'=>$propuesta);		
		listarpreparada($arrayusuario,$suma);

	}else{
		//Registro voto
		$consulta="INSERT INTO prog_likes_propuesta(usuario_id, propuesta_id, propuesta_voto) 
		VALUES (:usuario, :propuesta, :cuenta);";
    	$arrayusuario = array(':usuario'=>$usuario,':propuesta'=>$propuesta, ':cuenta'=>$cuenta);
		listarpreparada($arrayusuario,$consulta);
		//sumo voto
		$suma="UPDATE prog_propuestas SET sum_likes=sum_likes+(:cuenta) WHERE id = :propuesta;";
    	$arrayusuario = array(':cuenta'=>$cuenta, ':propuesta'=>$propuesta);
		listarpreparada($arrayusuario,$suma);

		if ($cuenta==1){
			$sumaPos="UPDATE prog_propuestas SET positivos=positivos+1 WHERE id = :propuesta;";
            $arrayusuario = array(':propuesta'=>$propuesta);
    		listarpreparada($arrayusuario,$sumaPos);
		}else{
			$sumaNeg="UPDATE prog_propuestas SET negativos=negativos+1 WHERE id = :propuesta;";
            $arrayusuario = array(':propuesta'=>$propuesta);
    		listarpreparada($arrayusuario,$sumaNeg);
		}
	}
	

	//recuento votos
	$consulta = "SELECT sum_likes FROM prog_propuestas WHERE id = :propuesta;";
    $arrayusuario = array(':propuesta'=>$propuesta);
	$result = $conn->prepare($consulta);
	$result->execute($arrayusuario);
	//Se crea array vacío
	$output= array();
	foreach($result as $res){
		$like=$res['sum_likes'];
	}
	//Array serializado para pasar datos con json.
	$output[] = array('sum_likes' => $like);
	echo json_encode($output);
		
	}catch(PDOException $e ){
		echo $e -> getMessage();
	}	

}