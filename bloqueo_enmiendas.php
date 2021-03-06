<?php
include_once "lib/functions.php";
include_once "colpr-config.php";

//Para que el usuario no pueda votar 2 veces los comentarios
if(isset ($_POST['usuario_id'])){

	
	$usuario=$_POST['usuario_id'];

	try{
	$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
	$consulta = "SELECT * FROM prog_likes_enmiendas
	WHERE usuario_id= :usuario;";
	$arrayusuario = array(':usuario'=>$usuario);
	$result = $conn->prepare($consulta);
	$result->execute($arrayusuario);
	//Se crea array vacío
	$output= array();
	foreach($result as $res){
		$like=$res['enmienda_voto'];
		$id_enm=$res['enmienda_id'];
		//si el voto es postivo
		if ($like==1){
			$block="enm-fav";
			$id=$id_enm;
		}
		//si el voto es negativo
		if ($like==-1){
			$block="enm-con";
			$id=$id_enm;
		}
	$output[] = array('activo' => $block, 'id' =>$id);
	}
	

	//Array serializado para pasar datos con json.
	
	echo json_encode($output);
		
	}catch(PDOException $e ){
		echo $e -> getMessage();
	}
}