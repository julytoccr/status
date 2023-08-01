<p class="sideBarTitle">
	<span class="sideBarDot">·</span>
	<?php echo __('usuario'); ?>
</p>

<div>
	<?php 
	echo '<p>'.__('saludo_usuario', array($this->Session->read('usuario.username'))).'</p>';
	echo "<p>There are no languages in the system</p>";
	echo $this->Html->link('cerrar_sesion','/usuarios/logout');
	?>
</div>
