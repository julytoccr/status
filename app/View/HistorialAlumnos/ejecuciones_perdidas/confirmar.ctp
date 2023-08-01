<?php
$url="";
if ($es_profe) {
    $url = '/historial_alumnos/ejecuciones_perdidas/' . $alumno_id;
}
else {
    $url = '/historial_alumnos/ejecuciones_perdidas/';
}
?>
<center>
<br />
<br />
<h2><?php echo $problema_nombre; ?></h2>
<h2><?php echo __("nota") . ": " . $nota; ?></h2>
<br />
<br />
<?php echo preg_replace("/\r{0,1}\n/","<br />\n",__('confir_recuperar_ejecucion')); ?>
<br />
<br />
<br />
<?php echo $this->Html->link(__("aceptar"), '/ejecuciones/aceptar_nota/' . $ejecucion_id); ?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php echo $this->Html->link(__("cancelar"), $url); ?>
</center>
