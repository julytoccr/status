<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('resultados_encuesta'),
    "sidebar" => 'encuestas/resultados',
    "encuesta_id" => $encuesta_id
));
$this->end();?>
<div class="encuestas index">
	<h1><?php echo $encuesta['nombre']?></h1>
	<br/>
	<i><?php echo $encuesta['contenido']?></i>

	<?php foreach ($resultados as $pregunta):?>
		<h2><?php echo $pregunta['PreguntasEncuesta']['contenido']?></h2>
		<table class="tabla_listado" style="text-align: center">
		<tr>
			<th width="50px"><?php echo __('grupo');?></th>
			<?php foreach ($pregunta['OpcionesEncuesta'] as $i=>$opc): ?>
				<th><?php echo $opc['OpcionesEncuesta']['contenido']?></th>
			<?php endforeach;?>
			<th><?php echo __('porcentaje_obs');?></th>
			<th><?php echo __('porcentaje_real');?></th>

		</tr>
		<?php foreach ($grupos as $nombre_grupo): ?>
			<tr>
				<th width="50px"><?php echo $nombre_grupo?></th>

				<?php foreach ($pregunta['OpcionesEncuesta'] as $i=>$opc): ?>
					<td><?php echo isset($opc['votos_grupos'][$nombre_grupo])? $opc['votos_grupos'][$nombre_grupo] : 0?></td>
				<?php endforeach;?>
				<td><?php echo isset($pregunta['porcentaje_obs'][$nombre_grupo])? $pregunta['porcentaje_obs'][$nombre_grupo] .'%' : 0 . '%'?></td>
				<td><?php echo isset($total_por_grupo[$nombre_grupo])? $total_por_grupo[$nombre_grupo] . '%': 0 . '%'?></td>
			</tr>
		<?php endforeach; ?>

		<tr>
			<th><?php echo __('total');?></th>
			<?php foreach ($pregunta['OpcionesEncuesta'] as $i=>$opc): ?>
				<td><?php echo "{$opc['numero_votos']} ({$opc['porcentaje_votos']} %)" ?></td>
			<?php endforeach;?>
		</tr>
		</table>
	<?php endforeach;?>
</div>
