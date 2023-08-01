<?php
	echo $this->Form->create(null,array('url'=>'/alumnos/alta_excel','type'=>'file'));
	echo $this->Form->input('Alumno.excel',array('type'=>'file'));
	echo $this->Form->input('Usuario.tipo_auth',array('options'=>array('plain'=>__('form_password_plano'),'ldap'=>__('form_ldap'),'fib'=>__('form_alumno_fib'))));
	echo $this->Form->end(__('enviar'));
?>
