<?php echo $this->Form->create('OpcionesEncuesta'); ?>
	<fieldset>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('contenido',array('label'=>__('form_contenido'),'type'=>'textarea'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('form_guardar')); ?>
