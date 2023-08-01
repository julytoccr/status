<p class="sideBarTitle">
	<span class="sideBarDot">·</span>
	<?php echo __('profesor'); ?>
</p>

<div>
	<?php
	if ($this->Session->check('profesor.asignatura.id')){
		echo '<p>'.__('asignatura_asignatura', array($this->Session->read('profesor.asignatura.name'))).'</p>';
		echo $this->Html->link(__('estadisticas'),'/estadisticas/asignatura');
		echo $this->Html->link(__('encuestas'),'/encuestas/');
		echo $this->Html->link(__('gestionar_bloques'),'/bloques/');
	} else {
		echo '<p>'.__('menu_texto_no_asignatura').'</p>';
	}

	echo $this->Html->link(__('editor'),'/editor/');
	echo $this->Html->link(__('programas'),'/programas/');
	echo $this->Html->link(__('seleccionar_asignatura'),'/profesores/seleccionar');
	?>
</div>
