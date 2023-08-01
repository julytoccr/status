<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('Estadisticas'),
    "sidebar" => 'estadisticas/problemas/problemas',
    "id" => $problema_id
));
$this->end();?>
<div align="center">
	<?php echo $this->Html->image($img_url,array('border'=>0,'alt'=>''))?>
</div>
<table class="tabla_listado">
	<tr>
		<th colspan="2"><?php echo __('pregunta');?></th>
		<th><?php echo __('numero_respuestas');?></th>
		<th><?php echo __('media_resultado');?></th>
		<th><?php echo __('desviacion_resultado');?></th>
	</tr>
<?php foreach ($preguntas as $i=>$p):?>
	<tr>
		<td><?php echo ($i+1)?></td>
		<td><?php echo $p['enunciado']?></td>
		<td><?php if (isset($estadisticas[$i]['num_ejecuciones'])) echo $estadisticas[$i]['num_ejecuciones']?></td>
		<td><?php if (isset($estadisticas[$i]['media_resultado'])) echo $estadisticas[$i]['media_resultado']?></td>
		<td><?php if (isset($estadisticas[$i]['desv_resultado'])) echo $estadisticas[$i]['desv_resultado']?></td>
	</tr>
<?php endforeach;?>
</table>

