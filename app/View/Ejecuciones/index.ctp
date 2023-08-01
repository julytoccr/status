<div class="ejecuciones index">
	<h2><?php echo __('Ejecuciones'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('alumno_id'); ?></th>
			<th><?php echo $this->Paginator->sort('agrupacion_id'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th><?php echo $this->Paginator->sort('fecha_limite'); ?></th>
			<th><?php echo $this->Paginator->sort('nota'); ?></th>
			<th><?php echo $this->Paginator->sort('semilla'); ?></th>
			<th><?php echo $this->Paginator->sort('problema_id'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	foreach ($ejecuciones as $ejecucione): ?>
	<tr>
		<td><?php echo h($ejecucione['Ejecucione']['id']); ?>&nbsp;</td>
		<td><?php echo h($ejecucione['Ejecucione']['alumno_id']); ?>&nbsp;</td>
		<td><?php echo h($ejecucione['Ejecucione']['agrupacion_id']); ?>&nbsp;</td>
		<td><?php echo h($ejecucione['Ejecucione']['created']); ?>&nbsp;</td>
		<td><?php echo h($ejecucione['Ejecucione']['fecha_limite']); ?>&nbsp;</td>
		<td><?php echo h($ejecucione['Ejecucione']['nota']); ?>&nbsp;</td>
		<td><?php echo h($ejecucione['Ejecucione']['semilla']); ?>&nbsp;</td>
		<td><?php echo h($ejecucione['Ejecucione']['problema_id']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $ejecucione['Ejecucione']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $ejecucione['Ejecucione']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $ejecucione['Ejecucione']['id']), null, __('Are you sure you want to delete # %s?', $ejecucione['Ejecucione']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>

	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Ejecucione'), array('action' => 'add')); ?></li>
	</ul>
</div>
