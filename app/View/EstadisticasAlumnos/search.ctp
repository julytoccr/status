<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('Estadisticas alumnos'),
    "sidebar" => 'estadisticas/estadisticas_alumnos'
));
$this->end();?>
<?php 
	echo $this->Form->create();
	echo $this->Form->input('Alumno.search',array('label'=>__('form_buscar')));
	echo $this->Form->input('Alumno.campos.usuario_nombre_apellidos', array('type'=>'checkbox','label'=>__('nombre')));
	echo $this->Form->input('Alumno.campos.usuario_login', array('type'=>'checkbox','label'=>__('usuario')));
	echo $this->Form->input('Alumno.campos.grupo_nombre', array('type'=>'checkbox','label'=>__('grupo')));
	echo $this->Form->input('Alumno.campos.usuario_email', array('type'=>'checkbox','label'=>__('email')));
	echo $this->Form->end(__('aceptar'));
?>

<?php if (isset($alumnos)): ?>
	<table class="tabla_listado">
		<tr>
			<th><?php echo __('usuario');?></th>
			<th><?php echo __('apellidos');?></th>
			<th><?php echo __('nombre');?></th>
			<th><?php echo __('grupo');?></th>
			<th><?php echo __('email');?></th>
		</tr>
	<?php foreach ($alumnos as $a): $a=$a['ViewBuscarAlumno'] ?>
		<tr>
			<td><?php echo $this->Html->link($a['usuario_login'],'/historial_alumnos/index/' . $a['alumno_id'])?></td>
			<td><?php echo $this->Html->link($a['usuario_apellidos'],'/historial_alumnos/index/' . $a['alumno_id'])?></td>
			<td><?php echo $this->Html->link($a['usuario_nombre'],'/historial_alumnos/index/' . $a['alumno_id'])?></td>
			<td><?php echo $this->Html->link($a['grupo_nombre'],'/historial_alumnos/index/' . $a['alumno_id'])?></td>
			<td><?php echo $this->Html->link($a['usuario_email'],'/historial_alumnos/index/' . $a['alumno_id'])?></td>
		</tr>
	<?php endforeach;?>
	</table>
<?php endif;?>
