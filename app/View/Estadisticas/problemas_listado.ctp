<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('estadisticas'),
    "sidebar" => 'estadisticas/estadisticas_problemas'
));
$this->end();?>
<table class="tabla_listado">
	<tr>
		<th><?php echo __('problema');?></th>
		<th><?php echo __('numero_ejecuciones');?></th>
		<th><?php echo __('nota_media');?></th>
	</tr>
<?php foreach ($problemas as $prob): ?>
	<tr>
		<td>
			<?php echo $this->Html->link($prob['Problema']['nombre'],'/estadisticas_problemas/index/' . $prob['Problema']['id'])?>
		</td>
		<td>
			<?php echo $prob['Estadisticas']['numero_ejecuciones']?>
		</td>
		<td>
			<?php echo round($prob['Estadisticas']['nota_media'],3)?>
		</td>


	</tr>
<?php endforeach; ?>
</table>
