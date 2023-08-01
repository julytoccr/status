<table id="listado" class="tabla_listado">
	<tr>
		<?php if(isset($actions)): ?>
			<th class="actions"><?php echo __('Actions'); ?></th>
		<?php endif; ?>
		
		<?php foreach($fields as $key => $field): ?>
			<th><?php echo __($key); ?></th>
		<?php endforeach; ?>
	</tr>

	<?php
	$par = false;
	foreach ($elements as $elem):
		$par = !$par;
	?>
	<tr class="item <?php if($par) echo 'tabla_listado_altRow'; ?>">

		<td class="actions">
			
			<?php
			if(isset($meta['defaultModel']))
				$defaultModel = $meta['defaultModel'];
			else
				$defaultModel = $field['Model'];
			$lastElement = end($actions);
			foreach($actions as $action => $meta_) {
				$id = $elem[$defaultModel]['id'];
				$label = __($meta_['label']);
					
				if(isset( $meta_['post'] )){
					$confirm = __($meta_['confirm'], $id);
					echo $this->Form->postLink(
						$label, 
						array('action' => $action, $id), 
						null, 
						$confirm
					);
				} else {
					if(isset($meta_['controller']))
						echo $this->Html->link($label, array('controller'=>$meta_['controller'],'action' => $action, $id));
					else
						echo $this->Html->link($label, array('action' => $action, $id));
				}
				if($meta_ != $lastElement)
					echo " | ";

			} ?>

		</td>

		<?php foreach($fields as $key => $field): ?>
		
		<td>
			<?php
				$val = $elem[$field['Model']][$field['field']];
				if(isset($field['formato'])){
					$val = $this->Formato->{$field['formato']}($val);
				} else 
					$val = h($val);
				echo $val; 
			?>
		&nbsp;
		</td>

		<?php endforeach; ?>
		
	</tr>
<?php endforeach; ?>
</table>
<?php if(!isset($paginate)){ ?>
	<p>
	<?php
		echo $this->Paginator->counter(array('format' => __('Página {:page} de {:pages}, mostrando {:current} resultados de {:count} en total')));
	?>
	</p>

	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('anterior '), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ' '));
		echo $this->Paginator->next(__(' siguiente') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
<?php
}
?>