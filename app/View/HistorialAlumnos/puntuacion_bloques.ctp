<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('historial_alumno',''),
    "sidebar" => 'historial/historial',
    "alumno_id" => $alumno_id
));
$this->end();?>
<table class="tabla_listado">
<tr>
	<th><?php echo __('nombre_bloque');?></th>
	<th><?php echo __('puntuacion');?></th>
</tr>
<?php foreach ($data as $info): ?>
	<tr>
		<td>
		<?php echo $info['Bloque']['nombre'] ?>
		</td>

		<td>
		<?php echo round($info['ViewPuntuacionBloque']['puntuacion'],3) ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
