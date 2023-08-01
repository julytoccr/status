<p class="sideBarTitle">
	<span class="sideBarDot">·</span>
	<?php echo __('alumno'); ?>
</p>

<div>
	<?php if($this->Session->check('alumno.asignatura.id')): ?>
	<p> <?php __('asignatura') ?> <?php echo $this->Session->read('alumno.asignatura.name')?></p>
	<?php
	echo $this->Html->link(__('menu_historial'),'/historial_alumnos/');
	echo $this->Html->link(__('menu_seleccion_bloque'),'/');
	echo $this->Html->link(__('menu_configuracion'),'/alumnos/configuracion');
else: ?>
<p><?php echo __('menu_texto_no_asignatura'); ?></p>
<?php endif; ?>
<?php echo $this->Html->link(__('seleccionar_asignatura'),'/alumnos/seleccionar'); ?>
</div>


<?php if($this->Session->check('alumno.asignatura.id')): ?>
	<p class="sideBarTitle">
		<span class="sideBarDot">·</span>
		<?php echo __('ultimo_problema_probado'); ?>
	</p>

	<div>
		<?php 
			echo $this->requestAction('/alumnos/avatar_ultimo_problema_aprobado',array('return'));
		?>
	</div>
<?php endif; ?>