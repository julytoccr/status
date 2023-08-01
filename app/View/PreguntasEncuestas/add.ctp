<?php echo $this->Form->create('PreguntasEncuesta'); ?>
	<fieldset>
	<?php
		echo $this->Form->input('contenido',array('type'=>'textarea'));
		echo $this->Form->input('encuesta_id',array('label'=>__('form_contenido'),'type'=>'hidden','value'=>$encuesta_id));
	?>
	</fieldset>
<?php echo $this->Form->end(__('form_guardar')); ?>
