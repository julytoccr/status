<?php
$this->start('css');
echo $this->Html->css('auto_assert/ejecuciones/css');
echo $this->Html->css('estatusr');
$this->end();
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('calculadora'),
    "sidebar" => 'tablas_estadisticas/menu'
));
$this->end();
?>
<div id="tablas"></div>

<?php if ($reintentar): ?>
	<div id="tiempo_limite" class="estatusr">
		<?php echo $this->requestAction('/ejecuciones/tiempo_limite/' . $ejecucion_id,array('return'))?>
	</div>
	<?php
		echo $this->Ajax->remoteTimer(
			array(
			'url' => '/ejecuciones/tiempo_limite/' . $ejecucion_id,
			'update' => 'tiempo_limite', 'position' => 'text', 'frequency' => 10
			)
		);
	?>
<?php endif; ?>


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
<?php if ($reintentar) echo $this->Form->create(null, array('url' => 'correccion','id'=>'EjecucionForm'))
?>

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
						elseif  ($resultados[$i]['mensaje_nota'])
						{
							echo '<div class="mensaje_nota">' . $resultados[$i]['mensaje_nota'] . '</div>';
						}
					?>
					<br/>
					<?php echo __('nota').': '.$resultados[$i]['nota']?>
				</td>


				<td class="respuesta">
					<?php
							if ($reintentar && $reintentos[$i]>0)
							{
								if($sintaxis[$preg['sintaxis_respuesta_id']]['nombre'] == "Vector")
								{
									echo $this->Form->input("Ejecucion.respuesta$i",array('label'=>'',	
																						'size'=>16,
																						'align'=>'right',
																						'maxlength'=>200));
								}
								else
								{
									echo $this->Form->input("Ejecucion.respuesta$i",array('label'=>'',	
																						'size'=>9,
																						'align'=>'right',
																						'maxlength'=>200));
								}
							}
							else
							{
								echo $respuestas[$i];
							}

							if (isset($soluciones[$i]))
							{
								echo '<br/>'.__('solucion').': '.$soluciones[$i];
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
	<?php if (isset($estrellas)):?>
		<div align="left" id="star_rating">
			<?php echo __('pregunta_utilidad_problema'); ?>
			<?php echo $this->StarRating->display($estrellas,'/ejecuciones/puntuar/' . $ejecucion_id)?>
			<span id="star_rating_gracias" style="display:none;">
				<?php echo __('gracias'); ?>
			</span>
		</div>
	<?php endif;?>
</div>

<?php if ($reintentar): ?>
	<div align="center">
		<?php echo $this->Form->submit(__('correccion'),array('div'=>false,'label'=>false,'class'=>'submit'))?>
		<?php if ($ejecucion['alumno_id']): ?>
			<?php echo $this->Form->input('Ejecucion/button',array('div'=>false,'label'=>false,'Type'=>'Button','class'=>'submit','Value'=>__('aceptar_nota'),'onClick'=>"javascript: window.location='" . $this->Html->url('/ejecuciones/aceptar_nota/' . $ejecucion_id ) . "'")); ?>
		<?php endif; ?>
		<?php if (! $ejecucion['alumno_id']): ?>
			<?php echo $this->Form->input('Ejecucion/button',array('div'=>false,'label'=>false,'Type'=>'Button','class'=>'submit','Value'=>__('menu_editar_problema'),'onClick'=>"javascript: window.location='" . $this->Html->url('/problemas/edit/' . $ejecucion['problema_id']) . "'")); ?>
		<?php endif; ?>
	</div>


	<?php echo $this->Javascript->disable_autocomplete('.respuesta input')?>
	<?php echo $this->Javascript->disable_submit('.respuesta input')?>
	<?php echo $this->Js->writeBuffer()?>

	<?php echo $this->Form->hidden('Ejecucion.id',array('default'=>$ejecucion_id))?>
	<?php echo $this->Form->end()?>

	<?php
	echo $this->Ajax->observeForm('EjecucionForm', 
		array(
			'selector' => 'class',
			'url' => '/ejecuciones/guardar_respuestas',
			'complete' => ''
		) 
	); 
	?>
<?php endif;?>

<?php if (! $reintentar && ! $ejecucion['alumno_id']): ?>
	<div align="center">
		<?php echo $this->Form->create(null);?>
		<?php echo $this->Form->input('Ejecucion/button',array('div'=>false,'label'=>false,'Type'=>'Button','class'=>'submit','Value'=>__('menu_editar_problema'),'onClick'=>"javascript: window.location='" . $this->Html->url('/problemas/edit/' . $ejecucion['problema_id']) . "'")); ?>
		<?php echo $this->Form->end();?>
	</div>
<?php endif;?>
