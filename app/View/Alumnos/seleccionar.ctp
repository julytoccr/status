<?php echo $this->Form->create(''); ?>
	<fieldset>
		<?php echo $this->Form->input('asignaturas',array('label'=>__('form_seleccion_asignatura')));?>
	</fieldset>
<?php echo $this->Form->end(__('aceptar')); ?>
