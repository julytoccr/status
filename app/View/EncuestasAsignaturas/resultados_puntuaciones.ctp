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
			<th style="text-align: left" width="220px"><?php echo __('opciones');?></th>
			<th><?php echo __('n');?></th>
			<th><?php echo __('numero_ejecuciones') .'/'. __('form_desviacion');?></th>
			<th><?php echo __('nota_media') .'/'. __('form_desviacion');?></th>
			<th><?php echo __('puntuacion') .'/'. __('form_desviacion');?></th>
		</tr>
		<?php foreach ($pregunta['OpcionesEncuesta'] as $opc): ?>
			<tr>
				<th style="text-align: left"><?php echo $opc['OpcionesEncuesta']['contenido']?></th>
				<td><?php echo count($opc['VotacionesEncuesta'])?></td>
				<td><?php echo isset($opc['num_ejecuciones'])? round($opc['num_ejecuciones']['mean'],1)  . ' / ' . round($opc['num_ejecuciones']['stdev'],1): ''?></td>
				<td><?php echo isset($opc['nota_media'])? round($opc['nota_media']['mean'],1)  . ' / ' . round($opc['nota_media']['stdev'],1): ''?></td>
				<td><?php echo isset($opc['puntuacion_total'])? round($opc['puntuacion_total']['mean'],1)  . ' / ' . round($opc['puntuacion_total']['stdev'],1): ''?></td>
			</tr>
		<?php endforeach; ?>
		</table>
	<?php endforeach;?>
</div>
