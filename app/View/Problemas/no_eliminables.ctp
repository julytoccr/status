<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('problemas'),
    "sidebar" => 'problemas/problemas'
));
$this->end();?>
<div class="problemas index">
	<?php
	$elements = $problemas;
	$fields = array(
		'form_buscar' => array('Model'=>'Problema','field'=>'nombre'),
		'form_buscar_campos' => array('Model'=>'Problema','field'=>'descripcion'),
	);
	$actions = array(
		'view' => array('label' => 'ver'),
	);
	echo $this->element('listado', compact('elements', 'fields', 'actions'));
	?>
</div>

