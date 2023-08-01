<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('Estadisticas alumnos'),
    "sidebar" => 'estadisticas/estadisticas_alumnos'
));
$this->end();?>

<div align="center">
	<?php echo $this->Html->image($img_url,array('border'=>0,'alt'=>''))?>
</div>
<?php echo $this->CustomPagination->show_links($page_url,$contexto=5) ?>
<table class="tabla_listado">
	<tr>
		<th><?php echo __('posicion');?></th>
		<th><?php echo __('nombre');?></th>
		<th><?php echo __('apellidos');?></th>
		<th><?php echo $valueTitle?></th>
	</tr>
	<?php foreach ($data as $alumno): ?>
	<?php $url='/historial_alumnos/index/' . $alumno['Alumno']['id']; ?>
	<tr>
		<td><?php echo $this->Html->link($alumno[$rankingModel][$rankingField],$url)?></td>
		<td><?php echo $this->Html->link(h($alumno['Usuario']['nombre']),$url)?></td>
		<td><?php echo $this->Html->link(h($alumno['Usuario']['apellidos']),$url)?></td>
		<td><?php echo $this->Html->link($alumno['EstAlumno'][$valueField],$url)?></td>
	</tr>
	<?php endforeach;?>
</table>
