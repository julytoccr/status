<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('Estadisticas'),
    "sidebar" => 'estadisticas/asignaturas'
));
$this->end();?>
<?php echo $this->element('estadisticas/problemas/listado_puntuacion', array('url'=>'/estadisticas_problemas/index/'));?>
		
