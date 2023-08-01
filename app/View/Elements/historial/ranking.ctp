<div align="center">
	<?php echo $this->Html->image($img_url,array('border'=>0,'alt'=>''))?>
</div>

<table class="tabla_listado">
	<tr>
		<th colspan="2" width="150"><?php echo __('posicion');?></th>
		<th><?php echo __('alias');?></th>
		<th><?php echo $valueTitle?></th>
	</tr>
	<?php foreach ($data as $alumno): ?>
	<tr>
		<?php
			if ($alumno[$rankingModel][$rankingField] <= 20)
			{
				echo '<td>' . $alumno[$rankingModel][$rankingField] . '</td>';
				echo '<td>';
				echo $this->element('alumno/avatar',array('avatar'=>$alumno['Alumno']['avatar']));
				echo '</td>';
			}
			else
			{
				echo '<td colspan="2">' . $alumno[$rankingModel][$rankingField] .'</td>';
			}
		?>
		<td><?php echo h($alumno['Alumno']['alias'])?></td>
		<td><?php echo $alumno['EstAlumno'][$valueField]?></td>
	</tr>
	<?php endforeach;?>
</table>
