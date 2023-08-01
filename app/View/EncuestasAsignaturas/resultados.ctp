<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('resultados_encuesta'),
    "sidebar" => 'encuestas/resultados',
    "encuesta_id" => $encuesta_id
));
$this->end();?>
<div class="encuestas index">
	<b><?php echo $encuesta['nombre']?></b>
	<br/>
	<i><?php echo $encuesta['contenido']?></i>

	<?php foreach ($resultados as $preg):?>
		<h2><?php echo $preg['PreguntasEncuesta']['contenido']?></h2>
		<table class="tabla_listado">
		<tr>
			<th width="40%"><?php echo __('opcion');?></th>
			<th width="30%" style="text-align: center"><?php echo __('numero_votos');?></th>
			<th width="30%" style="text-align: center"><?php echo __('proporcion');?></th>
		</tr>
		<?php foreach ($preg['OpcionesEncuesta'] as $i=>$opc): ?>
			<tr>
				<td><?php echo $opc['OpcionesEncuesta']['contenido']?></td>
				<td style="text-align: center"><?php echo $opc['numero_votos']?></td>
				<td style="text-align: center"><?php echo (isset($opc['porcentaje_votos'])? $opc['porcentaje_votos'] . ' %' : '') ?></td>
			</tr>
		<?php endforeach; ?>
		</table>
	<?php endforeach;?>
</div>
