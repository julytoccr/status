<div class="ejecuciones form">
<?php echo $this->Form->create('Ejecucione'); ?>
	<fieldset>
		<legend><?php echo __('Add Ejecucione'); ?></legend>
	<?php
		echo $this->Form->input('alumno_id');
		echo $this->Form->input('agrupacion_id');
		echo $this->Form->input('fecha_limite');
		echo $this->Form->input('nota');
		echo $this->Form->input('semilla');
		echo $this->Form->input('problema_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Ejecuciones'), array('action' => 'index')); ?></li>
	</ul>
</div>
