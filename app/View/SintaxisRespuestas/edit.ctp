<?php echo $this->Form->create('SintaxisRespuesta'); ?>
	<fieldset>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('nombre',array('label'=>__('form_nombre')));
		echo $this->Form->input('descripcion',array('rows'=>6,'cols'=>50,'label'=>__('form_descripcion')));
		echo $this->Form->input('expresiones',array('rows'=>10,'cols'=>90,'label'=>__('form_expresiones')));
	?>
	</fieldset>
<?php echo $this->Form->end(__('form_guardar')); ?>
