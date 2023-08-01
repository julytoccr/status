<p class="sideBarTitle">
	<span class="sideBarDot">·</span>
	<?php echo __('usuario'); ?>
</p>

<div>
	<?php 

	echo '<p>'.__('saludo_usuario', array($this->Session->read('usuario.login'))).'</p>';
	echo '<p>'.__('lenguaje_lenguaje', array($this->Session->read('usuario.lenguaje') )).'</p>';
	echo $this->Html->link(__('menu_seleccion_lenguaje'),'/usuarios/seleccionar_lenguaje');
	echo $this->Html->link(__('notificar_error_traduccion'),'/usuarios/notificar_error_traduccion');
	echo $this->Html->link(__('cambiar_password'),'/usuarios/change_password');
	echo $this->Html->link(__('cerrar_sesion'),'/usuarios/logout');
	?>
</div>
