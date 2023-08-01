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
		echo $this->Form->input('respuesta',array('label'=>__('form_respuesta_a_pregunta'),'options'=>$variables_respuestas_hash,'empty'=>''));
		echo $this->Form->input('sintaxis_parametro',array('label'=>__('form_param_sintaxis')));
		echo $this->Form->input('sintaxis_respuesta_id',array('label'=>__('form_resp_sintaxis')));
		echo $this->Form->input('tipos_respuesta_id',array('label'=>__('form_plantilla_expresiones'),'options'=>$tiposRespuesta));
		echo $this->Form->input('expresiones_parametro',array('label'=>__('form_param_expresiones')));
		echo $this->Form->input('expresiones_respuesta',array('rows'=>10,'cols'=>80,'label'=>__('form_expresiones_resp')));
		echo $this->Form->input('peso',array('label'=>__('form_peso')));
		echo $this->Form->input('ayuda',array('rows'=>5,'cols'=>40,'label'=>__('form_ayuda')));
		echo $this->Form->input('link1',array('label'=>__('form_link','')));
		echo $this->Form->input('link2',array('label'=>__('form_link','')));
		echo $this->Form->input('intentos',array('label'=>__('form_intentos')));
		echo $this->Form->input('mostrar_solucion',array('label'=>__('form_mostrar_solucion'),'type'=>'checkbox'));
		echo $this->Form->input('mensaje_correcto',array('rows'=>5,'cols'=>40,'label'=>__('form_mensaje_correcto')));
		echo $this->Form->input('mensaje_semicorrecto',array('rows'=>5,'cols'=>40,'label'=>__('form_mensaje_regular')));
		echo $this->Form->input('mensaje_incorrecto',array('rows'=>5,'cols'=>40,'label'=>__('form_mensaje_incorrecto')));
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
