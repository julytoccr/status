<?php echo $this->Form->create('OpcionesEncuesta'); ?>
	<fieldset>
	<?php
		echo $this->Form->input('preguntas_encuesta_id',array('type'=>'hidden','value'=>$preguntas_encuesta_id));
		echo $this->Form->input('contenido',array('label'=>__('form_contenido'),'type'=>'textarea'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('form_guardar')); ?>
