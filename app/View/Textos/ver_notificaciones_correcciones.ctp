<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('idiomas'),
    "sidebar" => 'lenguajes/idiomas'
));
$this->end();?>
<div class="textos index">
	<?php echo __('notificaciones_correcciones'); ?><br>
	<?php echo $this->Html->link(__('volver'),'/textos/ver_notificaciones_correcciones');?>
	<?php
	$elements = $correcciones;
	$fields = array(
		'texto_actual' => array('Model'=>'CorreccionTextos','field'=>'texto_actual'),
		'texto_correccion' => array('Model'=>'CorreccionTextos','field'=>'texto_correccion'),
	);
	$actions = array(
		'administrar_notificacion' => array('label' => 'administrar_notificacion'),
	);
	echo $this->element('listado', compact('elements', 'fields', 'actions'));
	?>
</div>
