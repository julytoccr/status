<div align="center">
<?php echo __('descrip_avatar'); ?>
<br/>
<h1><?php echo __('avatar_actual'); ?></h1>
<br/>
<?php echo $this->element('alumno/avatar',array('avatar'=>$alumno['avatar'])); ?>
<br/>
<br/>
<h1><?php echo __('cambiar_avatar'); ?></h1>
<?php
	echo $this->Form->create(null,array('url'=>'/alumnos/configuracion','type'=>'file'));
	echo $this->Form->input('Alumno.avatar',array('type'=>'file'));
	echo $this->Form->input('Alumno.alias');
	echo $this->Form->end(__('enviar'));
?>
<?php echo __('descrip_formato_avatar', array($avatar_kbmax));?> 
<br/>
<br/>
<?php echo $this->Html->link(__('eliminar_avatar'),'/alumnos/delete_avatar'); ?>

</div>
