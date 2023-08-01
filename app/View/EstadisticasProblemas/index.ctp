<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('estadisticas_problema'),
    "sidebar" => 'estadisticas/problemas/problemas',
    "id" => $problema_id
));
$this->end();?>
<table class="tabla_listado">
	<tr align="right">
		<th><?php echo __('nota_media');?></th>
		<th><?php echo __('numero_ejecuciones');?></th>
		<th><?php echo __('numero_alumnos');?></th>

	</tr>

	<tr align="right">
		<td><?php echo round($estadisticas['nota_media'],3)?></td>
		<td><?php echo $estadisticas['numero_ejecuciones']?></td>
		<td><?php echo $estadisticas['numero_alumnos']?></td>
	</tr>
</table>
