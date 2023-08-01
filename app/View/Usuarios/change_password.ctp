<?php $attributes = array('size'=>10,'maxlenght'=>20)?>
<?php echo $this->Form->create('Usuario'); ?>
	<fieldset>
	<?php
		echo $this->Form->input('password',am(array('label'=>__('form_password_anterior')),$attributes));
		echo $this->Form->input('new_password', am(array('label'=>__('form_password_nuevo'),'type'=>'password'),$attributes));
		echo $this->Form->input('repeat_password', am(array('label'=>__('form_password_repetir'),'type'=>'password'),$attributes));
	?>
	</fieldset>
<?php echo $this->Form->end(__('aceptar')); ?>
