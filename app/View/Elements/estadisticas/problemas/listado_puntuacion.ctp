<table class="tabla_listado">
	<tr>
		<th colspan="3" width="300"><?php echo __('problema_mas_10');?></th>
	</tr>
	<tr>
		<th><?php echo __('problema');?></th>
		<th><?php echo __('puntuacion');?></th>
		<th><?php echo __('numero_valoraciones');?></th>
	</tr>
	<?php foreach ($puntuaciones as $punt): ?>
		<?php
			if ($punt['ViewPuntuacionesProblemasAsignatura']['num_valoraciones'] >= 10)
			{
				echo '<tr>';
				echo '<td>' . $this->Html->link($punt['ViewPuntuacionesProblemasAsignatura']['nombre'], $url .  $punt['ViewPuntuacionesProblemasAsignatura']['problema_id']). '</td>';
				echo '<td>' . $punt['ViewPuntuacionesProblemasAsignatura']['puntuacion'] . '</td>';
				echo '<td>' . $punt['ViewPuntuacionesProblemasAsignatura']['num_valoraciones'] . '</td>';
				echo '</tr>';
			}
		?>
	<?php endforeach;?>
</table>

<table class="tabla_listado">
	<tr>
		<th colspan="3" width="300"><?php echo __('problema_menos_10');?></th>
	</tr>
	<tr>
		<th><?php echo __('problema');?></th>
		<th><?php echo __('puntuacion');?></th>
		<th><?php echo __('numero_valoraciones');?></th>
	</tr>
	<?php foreach ($puntuaciones as $punt): ?>
		<?php
			if ($punt['ViewPuntuacionesProblemasAsignatura']['num_valoraciones'] < 10)
			{
				echo '<tr>';
				echo '<td>' . $this->Html->link($punt['ViewPuntuacionesProblemasAsignatura']['nombre'], $url .  $punt['ViewPuntuacionesProblemasAsignatura']['problema_id']). '</td>';
				echo '<td>' . $punt['ViewPuntuacionesProblemasAsignatura']['puntuacion'] . '</td>';
				echo '<td>' . $punt['ViewPuntuacionesProblemasAsignatura']['num_valoraciones'] . '</td>';
				echo '</tr>';
			}
		?>
	<?php endforeach;?>
</table>
