<?php echo $this->Form->create('Bloque'); ?>
	<fieldset>
	<?php
		echo $this->Form->input('nombre',array('label'=>__('form_nombre')));
	?>
	</fieldset>
<?php echo $this->Form->end(__('form_guardar')); ?>
