<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('Estadisticas alumnos'),
    "sidebar" => 'estadisticas/estadisticas_alumnos'
));
$this->end();?>

<?php 
	echo $this->Form->create();
	echo $this->Form->input('Alumno.grupos',array('label'=>__('form_seleccion')));
	echo $this->Form->input('Alumno.estadisticos.media', array('label'=>__('form_media'),'type'=>'checkbox'));
	echo $this->Form->input('Alumno.estadisticos.max_nota', array('label'=>__('form_maxima'),'type'=>'checkbox'));
	echo $this->Form->input('Alumno.estadisticos.min_nota', array('label'=>__('form_minima'),'type'=>'checkbox'));
	echo $this->Form->input('Alumno.estadisticos.num_ejec_ap', array('label'=>__('form_n_ejecuciones'),'type'=>'checkbox'));
	echo $this->Form->input('Alumno.estadisticos.stdev_nota', array('label'=>__('form_desviacion'),'type'=>'checkbox'));
	echo $this->Form->end(__('aceptar'));
?>
