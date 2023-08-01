<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('Estadisticas grupos'),
    "sidebar" => 'estadisticas/estadisticas_grupos'
));
$this->end();?>
<table class="tabla_listado">
	<tr align="right">
		<td><?php echo __('grupo');?></td>
		<td><?php echo __('total_problemas_utilizados');?></td>
		<td><?php echo __('total_ejecuciones');?></td>
		<td><?php echo __('media_ejecuciones_problemas_utilizados');?></td>
		<td><?php echo __('nota_media_ejecuciones');?></td>
		<td><?php echo __('puntuacion_total');?></td>
		<td><?php echo __('total_alumnos');?></td>
		<td><?php echo __('total_usuarios');?></td>
		<td><?php echo '% '.__('porcentaje_usuarios');?></td>
	</tr>
<?php foreach ($estadisticas as $grupo=>$est):?>
	<tr align="right">
		<td><?php echo $this->Html->link($grupo,"/estadisticas_grupos/grupo/$grupo")?>
		<td><?php echo $est['problemas_ejecutados']?></td>
		<td><?php echo $est['numero_ejecuciones']?></td>
		<td><?php echo $est['media_ejecuciones_por_problema']?></td>
		<td><?php echo $est['nota_media_ejecuciones']?></td>
		<td><?php echo $est['puntuacion_total']?></td>
		<td><?php echo $est['numero_alumnos']?></td>
		<td><?php echo $est['numero_usuarios']?></td>
		<td><?php 	if ($est['numero_alumnos']>0)
					{
						echo round( ($est['numero_usuarios'] / $est['numero_alumnos']) * 100,1);
					}
					else echo "&nbsp;"
			?>
		</td>
	</tr>
<?php endforeach;?>
	<tr>
	<td  style="background-color: white;" colspan="9">&nbsp;</td>
	<tr/>

	<tr align="right">
		<th><?php echo __('suma');?></th>
		<td>&nbsp;</td>
		<td><?php echo $total['numero_ejecuciones']?></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><?php echo $total['puntuacion_total']?></td>
		<td><?php echo $total['numero_alumnos']?></td>
		<td><?php echo $total['numero_usuarios']?></td>
		<td>&nbsp;</td>
	</tr>

	<tr align="right">
		<th><?php echo __('media');?></th>
		<td><?php echo $media['problemas_ejecutados']?></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><?php echo $media['nota_media_ejecuciones']?></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><?php echo $media['porcentaje_usuarios']?></td>
	</tr>
</table>
