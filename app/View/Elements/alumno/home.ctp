<?php

echo $this->requestAction('/votaciones_encuestas/home_encuestas/',array('return'));

foreach($data as $bloque)
{
	if ($bloque['Problema'])
	{
		echo '<table class="tabla_listado">';
		echo '<tr>';
		echo '<th width="32">&nbsp;</th>';
		echo "<th>{$bloque['Bloque']['nombre']}</th>";
		echo '<th align="center">'.__('dificultad_').'</th>';
		echo '<th align="center">'.__('puntuacion').'</th>';
		echo '<th align="center">'.__('nota_media').'</th>';
		echo '<th align="center">'.__('ejecuciones').'</th>';
		//echo '<th align="center">&nbsp;</th>'; no se usa porque ya no hay seccion de descripcion de problema
		echo '<tr/>';

		foreach ($bloque['Problema'] as $problema)
		{
			echo '<tr>';
			if (isset($problema['Estadisticas']))
			{
				$est=$problema['Estadisticas'];
			}
			else
			{
				$est=null;
			}
			if ($est && $est['num_ejecuciones_problema']>0)
			{
				if ($est['nota_media_problema'] < 5)
				{
					$img='incorrecto.png';
				}
				else
				{
					$img='correcto.png';
				}
			}
			else
			{
				$img='semicorrecto.png';
			}


			echo '<td valign="top">';
			echo $this->Html->image("ejecuciones/$img");
			echo '</td>';

			echo '<td width="50%">';
			$id=$problema['Agrupacion']['id'];
			$name=$problema['Problema']['nombre'];

			// Para evitar tentaciones de creacion de plantillas, omitimos descripcion del problema
			//echo $this->Html->link($name,'/problemas/view/' . $problema['Problema']['id'] . '/'. $id);
			echo $this->Html->link($name,'/ejecuciones/iniciar_agrupacion/' . $id);
			
			switch ($problema['Problema']['dificultad_id'])
			{
				case 1:
					$dificultad = __('facil');
					break;
				case 2:
					$dificultad = __('medio');
					break;
				case 3:
					$dificultad = __('dificil');
					break;
				default:
					$dificultad = '';
					break;
			}
			
			echo '<td align="center">' . $dificultad . '</td>';
			
			if (isset($problema['Estadisticas']))
			{
				$est=$problema['Estadisticas'];
				echo '<td align="center">' . round($est['puntuacion_problema'],3) . '</td>';
				echo '<td align="center">' . round($est['nota_media_problema'],3) . '</td>';
				echo '<td align="center">' . $est['num_ejecuciones_problema'] . '</td>';
			}
			else
			{
				echo '<td>&nbsp;</td>';
				echo '<td>&nbsp;</td>';
				echo '<td>&nbsp;</td>';
			}
			// no se usa porque ya no hay seccion de descripcion de problema
			//echo '<td align="center">';
			//echo $this->Html->link(__('resolver'),'/ejecuciones/iniciar_agrupacion/' . $id);
			//echo '</td>';


			echo '</td></tr>';
		}
		echo '</table><br/>';
	}
}

?>
