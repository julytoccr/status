<?php echo $this->Form->create('Usuario'); ?>
	<fieldset>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('login',array('label'=>__('form_login')));
		echo $this->Form->input('nombre',array('label'=>__('form_nombre')));
		echo $this->Form->input('apellidos',array('label'=>__('form_apellidos')));
		echo $this->Form->input('email',array('label'=>__('form_email')));
		echo $this->Form->input('tipo_auth',array('label'=>__('form_tipo_autentificacion'),'options'=>array('plain'=>__('form_password_plano'),'ldap'=>__('form_ldap'),'fib'=>__('form_alumno_fib'))));
	?>
	</fieldset>
<?php echo $this->Form->end(__('form_guardar')); ?>
