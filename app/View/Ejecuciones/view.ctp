<div class="ejecuciones view">
<h2><?php  echo __('Ejecucione'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($ejecucione['Ejecucione']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Alumno Id'); ?></dt>
		<dd>
			<?php echo h($ejecucione['Ejecucione']['alumno_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Agrupacion Id'); ?></dt>
		<dd>
			<?php echo h($ejecucione['Ejecucione']['agrupacion_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($ejecucione['Ejecucione']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Fecha Limite'); ?></dt>
		<dd>
			<?php echo h($ejecucione['Ejecucione']['fecha_limite']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Nota'); ?></dt>
		<dd>
			<?php echo h($ejecucione['Ejecucione']['nota']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Semilla'); ?></dt>
		<dd>
			<?php echo h($ejecucione['Ejecucione']['semilla']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Problema Id'); ?></dt>
		<dd>
			<?php echo h($ejecucione['Ejecucione']['problema_id']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Ejecucione'), array('action' => 'edit', $ejecucione['Ejecucione']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Ejecucione'), array('action' => 'delete', $ejecucione['Ejecucione']['id']), null, __('Are you sure you want to delete # %s?', $ejecucione['Ejecucione']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Ejecuciones'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Ejecucione'), array('action' => 'add')); ?> </li>
	</ul>
</div>
