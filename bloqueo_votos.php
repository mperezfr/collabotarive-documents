<?php
include_once "lib/functions.php";
include_once "colpr-config.php";

if(isset ($_POST['propuesta'])){

	$propuesta=$_POST['propuesta'];
	$usuario=$_POST['usuario_id'];

	try{
	$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
	$consulta = "SELECT propuesta_voto FROM prog_likes_propuesta 
	WHERE usuario_id= :usuario and propuesta_id = :propuesta;";

    $arrayusuario = array(':usuario'=>$usuario, ':propuesta'=>$propuesta);

	$result = $conn->prepare($consulta);
	$result->execute($arrayusuario);
	//Se crea array vacío
	$output= array();
	foreach($result as $res){
		$like=$res['propuesta_voto'];
	}
	//si el voto es postivo
	if ($like==1){
		$block="prop_favor";
	}
	//si el voto es negativo
	if ($like==-1){
		$block="prop_contra";
	}


	//Array serializado para pasar datos con json.
	$output[] = array('activo' => $block);
	echo json_encode($output);
		
	}catch(PDOException $e ){
		echo $e -> getMessage();
	}
}