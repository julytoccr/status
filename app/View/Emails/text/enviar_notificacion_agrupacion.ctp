<?php echo __('saludo_usuario_problema', array($nombre));?>
<?php echo __('notificacion_problema_info', array(htmlentities($problema['nombre']). $bloque['nombre'])); ?>
<?php echo __('notificacion_problema_desc', array($problema['dificultad_id'])); ?>:<?php echo $problema['descripcion'] ?>
<?php echo $restriccion?>
<br />
