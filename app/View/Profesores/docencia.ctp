<?php echo $this->Form->create('Profesor'); ?>

<div align="left">
<?php
	echo $this->Form->hidden('Profesor.id');


	foreach ($asignaturas as $asig)
	{
		echo $this->Form->checkbox('Asignatura.docencia_'.$asig['Asignatura']['id']);
		echo $asig['Asignatura']['nombre'];
		echo '<br/>';
	}
	echo '<br/>';
	echo $this->Form->submit(__('modificar'));

?>
</div>
<?php echo $this->Form->end()?>

