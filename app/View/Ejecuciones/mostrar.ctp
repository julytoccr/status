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

<div id="tiempo_limite" class="estatusr">
	<?php echo $this->requestAction('/ejecuciones/tiempo_limite/' . $ejecucion_id,array('return'))?>
</div>
<?php
echo $this->Ajax->remoteTimer(
	array(
	'url' => '/ejecuciones/tiempo_limite/' . $ejecucion_id,
	'update' => 'tiempo_limite', 'position' => 'html', 'frequency' => 10
	)
);
?>
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
<?php echo $this->Form->create('Ejecucion', array('id'=>'EjecucionForm','url' => array('controller'=>'ejecuciones', 'action' => 'correccion')))
?>

<div id="problema_preguntas">
	<table>
		<?php for($i=0;$i<count($preguntas);$i++):?>
		<?php
			$preg=$preguntas[$i];
			$j=$i+1;
		?>
			<tr class="fila_pregunta">
				<td class="pregunta">
					<?php echo "$j. {$preg['enunciado']}"?>
					<?php
						if ($preg['ayuda']) echo '<br/><span class="ayuda">' . $preg['ayuda'] . '</span>';
						if ($preg['link1']) echo '<br/>' . $this->Html->link($preg['link1'],$preg['link1']);
						if ($preg['link2']) echo '<br/>' . $this->Html->link($preg['link2'],$preg['link2']);
					?>
				</td>
				<td class="respuesta">
					<?php
							if ($reintentos[$i]>0)
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
					?>
				</td>
			</tr>
		<?php endfor;?>
	</table>
</div>

	<div align="center">
		<?php echo $this->Form->submit(__('correccion'))?>
		<?php if (! $ejecucion['alumno_id']): ?>
			<?php echo $this->Form->input('Ejecucion/button',array('label'=>'','Type'=>'Button','class'=>'submit','Value'=>__('menu_editar_problema'),'onClick'=>"javascript: window.location='" . $this->Html->url('/problemas/edit/' . $ejecucion['problema_id']) . "'")); ?>
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
			'url' => '/ejecuciones/guardar_respuestas',
			'complete' => ''
		) 
	); 
	?>
