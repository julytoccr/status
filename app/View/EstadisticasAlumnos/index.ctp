<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('Estadisticas alumnos'),
    "sidebar" => 'estadisticas/estadisticas_alumnos'
));
$this->end();?>
<?php echo $this->CustomPagination->show_links($page_url,$contexto=5) ?>
<table class="tabla_listado">
	<tr>
		<th><?php echo __('usuario');?></th>
		<th><?php echo __('apellidos');?></th>
		<th><?php echo __('nombre');?></th>
		<th><?php echo __('problemas');?></th>
		<th><?php echo __('puntuacion');?></th>
		<th><?php echo __('nota_media');?></th>
	</tr>
<?php foreach ($alumnos as $a): ?>
	<tr>
		<td><?php echo $this->Html->link($a['Usuario']['login'],'/historial_alumnos/index/' . $a['Alumno']['id'])?></td>
		<td><?php echo $this->Html->link($a['Usuario']['apellidos'],'/historial_alumnos/index/' . $a['Alumno']['id'])?></td>
		<td><?php echo $this->Html->link($a['Usuario']['nombre'],'/historial_alumnos/index/' . $a['Alumno']['id'])?></td>
		<td><?php echo $a['Estadisticas']['num_problemas_ejecutados']?></td>
		<td><?php echo $a['Estadisticas']['puntuacion_total']?></td>
		<td><?php echo $a['Estadisticas']['nota_media']?></td>
	</tr>
<?php endforeach;?>
</table>
