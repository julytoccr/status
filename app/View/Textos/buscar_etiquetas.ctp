<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('gestionar_terminos'),
    "sidebar" => 'textos/textos'
));
$this->end();?>
<?php echo $this->Form->create('Textos',array('url'=>'buscar_etiquetas')); ?>
	<fieldset>
	<?php
		echo $this->Form->input('Etiqueta.id',array('label'=>__('form_cadena_busqueda'),'type'=>'text'));
		echo $this->Form->input('Etiqueta.tipo',array('label'=>__('form_tipo_busqueda'),'options'=>$tipos));
		echo $this->Form->input('Etiqueta.lenguaje_busqueda',array('label'=>__('form_lenguaje_busqueda'),'options'=>$lenguajes));
		echo $this->Form->input('Etiqueta.keysensitive',array('label'=>__('form_keysensitive'),'type'=>'checkbox'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('aceptar')); ?>
