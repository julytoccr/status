<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('alumnos'),
    "sidebar" => 'alumnos/menu'
));
$this->end();?>
<div class="alumnos form">
<?php echo $this->Form->create('Alumno'); ?>
	<fieldset>
	<?php
		echo $this->Form->input('Usuario.nombre',array('label'=>__('form_nombre')));
		echo $this->Form->input('Usuario.apellidos',array('label'=>__('form_apellidos')));
		echo $this->Form->input('Usuario.email',array('label'=>__('form_email')));
		echo $this->Form->input('repetidor',array('label'=>__('form_repetidor')));
		echo "<br/>";
		echo $this->Form->input('Grupo.nombre',array('label'=>__('form_grupo')));
		echo $this->Form->input('Usuario.login',array('label'=>__('form_login')));
		echo $this->Form->input('Usuario.tipo_auth',array('options'=>array('plain'=>__('form_password_plano'),'ldap'=>__('form_ldap'),'fib'=>__('form_alumno_fib'))));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
