<?php echo $this->Form->create('Usuario'); ?>
	<fieldset>
	<?php
		echo $this->Form->input('lenguaje',array('label'=>__('form_seleccion_lenguaje')));
	?>
	</fieldset>
<?php echo $this->Form->end(__('aceptar')); ?>
