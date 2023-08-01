<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('estadisticas'),
    "sidebar" => 'estadisticas/asignaturas'
));
$this->end();?>
<?php
	if (isset($existen_datos))
	{
		echo '<h1>'.__('alumnos_diferentes').'</h1>';
		echo $this->Html->image($img1,array('usemap'=>'#map','border'=>0,'alt'=>''));
		echo $map1;
		echo '<br>';
		echo '<h1>'.__('ejecuciones').'</h1>';
		echo $this->Html->image($img2,array('usemap'=>'#map2','border'=>0,'alt'=>''));
		echo $map2;
	}
	else
	{
		echo '<b>'.__('no_login_sistema').'</b>';
	}

?>
