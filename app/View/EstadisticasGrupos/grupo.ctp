<table>
<tr>
	<?php foreach ($grupos as $g):?>
		<td>
			<?php
				if ($grupo==$g) echo $g;
				else echo $this->Html->link($g,'/estadisticas_grupos/grupo/' . $g);
			?>
		</td>
	<?php endforeach;?>
</tr>
</table>

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

	<tr>
		<td><?php echo __('puntuacion_total');?></td>
		<td><?php echo $estadisticas['puntuacion_total']?></td>
	</tr>
</table>

<div style="float: left">
	<?php echo $this->CustomPagination->show_links($page_url,$contexto=5) ?>
</div>

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
