<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('estadisticas'),
    "sidebar" => 'estadisticas/asignaturas'
));
$this->end();?>
<table class="tabla_listado">
	<tr>
		<td><?php echo __('total_problemas_disponibles');?></td>
		<td><?php echo $estadisticas['problemas_disponibles']?></td>
	</tr>

	<tr>
		<td><?php echo __('total_problemas_utilizados');?></td>
		<td><?php echo $estadisticas['problemas_ejecutados']?></td>
	</tr>

	<tr>
		<td><?php echo __('total_ejecuciones');?></td>
		<td><?php echo $estadisticas['numero_ejecuciones']?></td>
	</tr>

	<tr>
		<td><?php echo __('media_ejecuciones_problemas_utilizados');?></td>
		<td><?php echo $estadisticas['media_ejecuciones_por_problema']?></td>
	</tr>

	<tr>
		<td><?php echo __('nota_media_ejecuciones');?></td>
		<td><?php echo $estadisticas['nota_media_ejecuciones']?></td>
	</tr>
</table>
