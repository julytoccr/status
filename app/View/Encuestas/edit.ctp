<?php echo $this->Form->create('Encuesta'); ?>
	<fieldset>
	<?php
		echo $this->Form->input('nombre',array('label'=>__('form_nombre')));
		echo $this->Form->input('contenido',array('label'=>__('form_contenido'),'type'=>'textarea',array('label'=>__('form_contenido'))));
	?>
	</fieldset>
<?php echo $this->Form->end(__('form_guardar')); ?>
