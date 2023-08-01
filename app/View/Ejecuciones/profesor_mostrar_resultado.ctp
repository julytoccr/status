<?php
$this->start('css');
echo $this->Html->css('estatusr');
$this->end();
?>
<?php echo $this->Html->link(__('ejecutar_problema_con_datos'),"/ejecuciones/iniciar_problema/{$problema['id']}/45/$semilla")?>
<br/>
<div id="problema_enunciado">

	<?php echo $problema['enunciado'];?>
	<br/>
	<?php
		if ($problema['variables_mostrar'])
		{
			echo $this->Html->link(__('copiar_datos_minitab_excel'),
							'/ejecuciones/minitab/' . $ejecucion_id,
							array('target'=>'_blank'));
		}
	?>
</div>

<div id="problema_preguntas">
	<table>
		<?php for($i=0;$i<count($preguntas);$i++):?>
		<?php
			$preg=$preguntas[$i];
			$j=$i+1;
		?>
			<?php
				if ($resultados[$i]['resultado']==1)
				{
					$img='correcto.png';
				}
				elseif ($resultados[$i]['resultado']==0)
				{
					$img='incorrecto.png';
				}
				else
				{
					$img='semicorrecto.png';
				}
			?>

			<tr class="fila_pregunta">
				<td valign="top"><?php echo $this->Html->image("ejecuciones/$img");?></td>
				<td class="pregunta">
					<?php echo "$j. {$preg['enunciado']}"?>
					<?php
						if ($preg['ayuda']) echo '<br/><span class="ayuda">' . $preg['ayuda'] . '</span>';
						if ($preg['link1']) echo '<br/>' . $this->Html->link($preg['link1'],$preg['link1']);
						if ($preg['link2']) echo '<br/>' . $this->Html->link($preg['link2'],$preg['link2']);
					?>

					<?php
						if ($resultados[$i]['mensaje'])
						{
							echo '<div class="mensaje_respuesta">' . $resultados[$i]['mensaje'] . '</div>';
						}
					?>
					<br/>
					<?php echo __('nota').': '.$resultados[$i]['nota']?><br/>
					<?php echo __('resultado').': '.$resultados[$i]['resultado']?><br/>
					<?php
					echo __('resultado_guardado') .': ';
					echo (isset($resultados_guardados[$i])?$resultados_guardados[$i] : 0);
					 ?><br/>
				</td>


				<td class="respuesta">
					<?php
							echo (isset($respuestas[$i])?$respuestas[$i] :'' );
							if (isset($soluciones[$i]))
							{
								echo '<br/>'.__('solucion').' '.$soluciones[$i];
							}
					?>
				</td>
			</tr>
		<?php endfor;?>
	</table>
</div>

<div align="center" id="puntuacion">
	<table>
		<tr>
			<th colspan="2"><?php echo __('resultado'); ?></th>
		</tr>
		<tr>
			<td><?php echo __('nota'); ?></td>
			<td><?php echo $nota?></td>
		</tr>
		<?php if (isset($puntuacion)):?>
		<tr>
			<td><?php echo __('puntuacion'); ?></td>
			<td><?php echo $puntuacion?></td>
		</tr>
		<?php endif; ?>
	</table>
</div>
