<table class="tabla_listado">
	<tr>
		<th><?php echo __('nombre'); ?></th>
		<th><?php echo __('apellidos'); ?></th>
		<th><?php echo __('login'); ?></th>
		<th><?php echo __('grupo'); ?></th>
		<th><?php echo __('estado_alta'); ?></th>
	</tr>
<?php foreach ($estado as $a): ?>
	<tr>
		<td><?php echo h($a['nombre'])?></td>
		<td><?php echo h($a['apellidos']) ?></td>
		<td><?php echo h($a['login']) ?></td>
		<td><?php echo h($a['grupo'])  ?></td>
		<td>
			<?php
				if(is_null($a['result']))
					echo "Error guardando";
			  	else if($a['result'] == -1) 
					echo "El usuario ya existe en esta asignatura";
			  	else
					echo "Correcto";
			?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
