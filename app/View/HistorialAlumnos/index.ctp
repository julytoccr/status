<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('historial_alumno',''),
    "sidebar" => 'historial/historial',
    "alumno_id" => $alumno_id
));
$this->end();?>
<div id="aux"></div>
<table class="tabla_listado">
	<tr>
		<td><?php echo __('usuario');?></td>
		<td><?=$alumno['Usuario']['login']?></td>
	</tr>

	<tr>
		<td><?php echo __('nombre');?></td>
		<td><?=$alumno['Usuario']['nombre']?></td>
	</tr>

	<tr>
		<td><?php echo __('apellidos');?></td>
		<td><?=$alumno['Usuario']['apellidos']?></td>
	</tr>

	<tr>
		<td><?php echo __('email');?></td>
		<td><?=$alumno['Usuario']['email']?></td>
	</tr>


	<tr>
		<td><?php echo __('grupo');?></td>
		<td><?=$alumno['Grupo']['nombre']?></td>
	</tr>
</table>

<table class="tabla_listado">
	<tr>
		<td><?php echo __('total_problemas');?></td>
		<td><?=isset($estadisticas['num_problemas_ejecutados'])?$estadisticas['num_problemas_ejecutados'] : ''?></td>
	</tr>

	<tr>
		<td><?php echo __('nota_media');?></td>
		<td><?=isset($estadisticas['nota_media'])? round($estadisticas['nota_media'],2) :''?></td>
	</tr>

	<tr>
		<td><?php echo __('puntuacion_total');?></td>
		<td><?=isset($estadisticas['puntuacion_total'])?$estadisticas['puntuacion_total']:''?></td>
	</tr>

	<tr>
		<td><?php echo __('numero_ejecuciones');?></td>
		<td><?=isset($estadisticas['num_ejecuciones'])?$estadisticas['num_ejecuciones']:''?></td>
	</tr>


	<tr>
		<td><?php echo __('puesto_ranking_ejecuciones');?></td>
		<td><?=isset($estadisticas['posicion_ranking_ejecuciones'])?$estadisticas['posicion_ranking_ejecuciones']:''?></td>
	</tr>

	<tr>
		<td><?php echo __('puesto_ranking_nota');?></td>
		<td><?=isset($estadisticas['posicion_ranking_nota'])?$estadisticas['posicion_ranking_nota']:''?></td>
	</tr>

	<tr>
		<td><?php echo __('puesto_ranking_puntuacion');?></td>
		<td><?=isset($estadisticas['posicion_ranking_puntuacion'])?$estadisticas['posicion_ranking_puntuacion']:''?></td>
	</tr>

</table>

<?php

foreach($data as $bloque)
{
	if (isset($bloque['Problema']) && $bloque['Problema'])
	{
		echo '<table class="tabla_listado">';
		echo '<tr>';
		echo '<th width="32">&nbsp;</th>';
		echo "<th>{$bloque['Bloque']['nombre']}</th>";
		echo '<th align="center">'.__('puntuacion').'</th>';
		echo '<th align="center">'.__('nota_media').'</th>';
		echo '<th align="center">'.__('ejecuciones').'</th>';
		echo '<tr/>';

		foreach ($bloque['Problema'] as $problema)
		{

			if (isset($problema['Estadisticas']))
			{
				$est=$problema['Estadisticas'];
			}
			else
			{
				$est = null;
			}
			if ($est && $est['num_ejecuciones_problema']>0)
			{
				echo '<tr>';
				
				if ($est['nota_media_problema'] < 5)
				{
					$img='incorrecto.png';
				}
				else
				{
					$img='correcto.png';
				}

				echo '<td valign="top">';
				echo $this->Html->image("ejecuciones/$img");
				echo '</td>';

				echo '<td width="50%">';
				$id=$problema['Agrupacion']['id'];
				$name=$problema['Problema']['nombre'];

				if($es_profe)
					echo $this->Html->link($name,'/historial_alumnos/listado_ejecuciones_problema/'. $alumno['Alumno']['id'] . '/' . $problema['Problema']['id']);
				else
					echo $this->Html->link($name,'/historial_alumnos/listado_ejecuciones_problema/'. 0 . '/' . $problema['Problema']['id']);
				//echo $name;
				echo '<td align="center">' . round($est['puntuacion_problema'],3) . '</td>';
				echo '<td align="center">' . round($est['nota_media_problema'],3) . '</td>';
				echo '<td align="center">' . $est['num_ejecuciones_problema'] . '</td>';
				
				echo '</td></tr>';
			}
		}
		echo '</table><br/>';
	}
}

?>
