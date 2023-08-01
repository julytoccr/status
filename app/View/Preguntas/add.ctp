<?php
$this->start('script');
echo $this->Html->script('/ckeditor/ckeditor');
$this->end();
?>
<?php echo $this->Form->create('Pregunta'); ?>
	<fieldset>
	<?php
		echo $this->Form->input('problema_id',array('type'=>'hidden','value'=>$problema_id));
		echo $this->Form->input('enunciado',array('class'=>'ckeditor','between' => '<br/>','label'=>__('form_enunciado')));
		echo $this->Form->input('respuesta',array('options'=>$variables_respuestas_hash));
		echo $this->Form->input('sintaxis_parametro');
		echo $this->Form->input('sintaxis_respuesta_id');
		echo $this->Form->input('tipos_respuesta_id',array('options'=>$tiposRespuesta));
		echo $this->Form->input('expresiones_parametro');
		echo $this->Form->input('expresiones_respuesta');
		echo $this->Form->input('peso');
		echo $this->Form->input('ayuda');
		echo $this->Form->input('link1');
		echo $this->Form->input('link2');
		echo $this->Form->input('intentos');
		echo $this->Form->input('mostrar_solucion',array('type'=>'checkbox'));
		echo $this->Form->input('mensaje_correcto');
		echo $this->Form->input('mensaje_semicorrecto');
		echo $this->Form->input('mensaje_incorrecto');
	?>
	</fieldset>
<?php echo $this->Form->end(__('form_guardar')); ?>
<?php
$data = $this->Js->get('#PreguntaTiposRespuestaId')->serializeForm( 
			array( 
				'isForm' => false, 
				'inline' => true) 
			);
$this->Js->get('#PreguntaTiposRespuestaId')->event( 
		  'change', 
		  $this->Js->request( 
			array('action' => 'seleccionar_tipos_respuesta'), 
			array( 
					'update' => '#PreguntaExpresionesRespuesta',
					'data' => $data, 
					'async' => true, 
					'dataExpression'=>true, 
					'method' => 'POST'
				) 
			) 
		);
echo $this->Js->writeBuffer();
?>
