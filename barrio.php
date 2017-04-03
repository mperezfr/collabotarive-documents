<?php
include_once "lib/functions.php";

if(isset ($_GET['barrio'])){

$tag = $_GET['barrio'];	
if ($tag=="Oliver-Valdefierro"){
	$etiqueta=$tag;
}elseif ($tag=="Actur-Rey-Fernando"){
	$etiqueta="Actur-Rey Fernando";
}elseif ($tag=="Montanana"){
	$etiqueta="Montañana";
}else{
	$etiqueta = str_replace ( '-' ,' ', $tag);
}

$template = $twig->loadTemplate('propuestas.html');

$arraytag = array(':tag' => $tag);

$consulta = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.positivos, p.negativos
FROM users AS u, prog_propuestas AS p
WHERE  `autor_id` = u.id and p.barrio = :tag
ORDER BY p.sum_likes DESC; ';

$debatidas = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.positivos, p.negativos
FROM users AS u, prog_propuestas AS p
WHERE  `autor_id` = u.id and p.barrio = :tag
ORDER BY p.comentarios DESC; ';

$recientes = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.positivos, p.negativos
FROM users AS u, prog_propuestas AS p
WHERE  `autor_id` = u.id and p.barrio = :tag
ORDER BY p.id DESC; ';

$consensuadas = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.positivos, p.negativos, (LOG(p.positivos+p.negativos)* ((p.positivos-p.negativos) /(p.positivos+p.negativos))) log, (p.positivos /(p.positivos+p.negativos)) porcentaje
FROM users AS u, prog_propuestas AS p
WHERE  `autor_id` = u.id and p.barrio = :tag
ORDER BY log DESC; ';



$datos = array('tag'=>$etiqueta,'user'=>autentificado(),
                'propuestas'=>listarpreparada($arraytag, $consulta), 
                'debatidas'=>listarpreparada($arraytag, $debatidas),
                'recientes'=>listarpreparada($arraytag, $recientes),
                'consensuadas'=>listarpreparada($arraytag, $consensuadas));

echo $template->render($datos);
}