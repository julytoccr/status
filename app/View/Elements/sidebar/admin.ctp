<p class="sideBarTitle">
	<span class="sideBarDot">·</span>
	<?php echo __('administrador'); ?>
</p>

<div>
	<?php
		if ($this->Session->check('admin.asignatura.id')){
			echo '<p id="asig_admin">'.__('asignatura_asignatura',array($this->Session->read('admin.asignatura.name'))).'</p>';
			echo $this->Html->link(__('alumnos'),'/alumnos/');
		}
		else
		{
			echo '<p id="asig_admin">'.__('menu_texto_no_asignatura').'</p>';
		}



	echo $this->Html->link(__('idiomas'),'/lenguajes/idiomas');
	echo $this->Html->link(__('profesores'),'/profesores/');
	echo $this->Html->link(__('problemas'),'/problemas/');
	echo $this->Html->link(__('administradores'),'/administradores/');
	echo $this->Html->link(__('asignaturas'),'/asignaturas/');
	echo $this->Html->link(__('avanzado'),'/sintaxis_respuestas/avanzado');
	?>
</div>

