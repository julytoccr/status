<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('historial_alumno',''),
    "sidebar" => 'historial/historial',
    "alumno_id" => $alumno_id
));
$this->end();?>
<?php echo $this->element('historial/ranking',
	array(
			'valueTitle'=>__('numero_ejecuciones'),
			'rankingModel'=>'EstRankingEjecucion',
			'rankingField'=>'posicion_ranking_ejecuciones',
			'valueField'=>'num_ejecuciones'
	));
?>

