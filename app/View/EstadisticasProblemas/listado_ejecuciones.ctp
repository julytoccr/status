<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('Estadisticas'),
    "sidebar" => 'estadisticas/problemas/problemas',
    "id" => $problema_id
));
$this->end();?>
<table class="tabla_listado">
<tr>
	<th width="40"><?php echo __('ejecuciones_problema');?></th>
	<th colspan="3"><?php echo __('alumno');?></th>
	<th><?php echo __('nota');?></th>
	<th><?php echo __('fecha');?></th>
</tr>
<?php foreach($ejecuciones as $e): ?>
	<tr>
		<td><?php echo $this->Html->link($e['Ejecucion']['id'],'/ejecuciones/profesor_mostrar_resultado/' . $e['Ejecucion']['id'])  ?></td>
		<td><?php echo $this->Html->link($e['Usuario']['login'],'/historial_alumnos/index/' . $e['Alumno']['id'])  ?>
		<td><?php echo $e['Usuario']['nombre']?></td>
		<td><?php echo $e['Usuario']['apellidos']?></td>
		<td><?php echo $e['Ejecucion']['nota']?></td>
		<td><?php echo $e['Ejecucion']['created']?></td>
	</tr>
<?php endforeach; ?>
</table>

