<div style="font-size: 14pt"><?php echo __('saludo_usuario_problema', array("<b>".$nombre."</b>")); ?></div><br /><br />
<div style="font-size: 12pt"><?php echo __('notificacion_problema_info', array("<u>" . htmlentities($problema['nombre']) . "</u>", "<u>" . $bloque['nombre'] . "</u>")); ?><br /><br /><br />
<?php echo __('notificacion_problema_desc', array($problema['dificultad_id'])); ?>: <br /><br /><!--<table width="500" style="border: 1px solid #3366CC"><tr><td>--><i><div style="font-size: 11pt"><?php echo $problema['descripcion'] ?></div></i><!--</td></tr></table>--></div><br /><br />
<?php echo $restriccion?>
<br />
