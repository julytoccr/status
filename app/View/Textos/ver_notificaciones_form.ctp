<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('idiomas'),
    "sidebar" => 'lenguajes/idiomas'
));
$this->end();?>
<div class="textos form">
<?php echo $this->Form->create(null,array('url'=>'/textos/ver_notificaciones_correcciones')); ?>
	<fieldset>
	<?php
		echo $this->Form->input('Texto.tipo_busqueda',array('label'=>__('form_seleccione_tipo_busqueda'),'options'=>$tipos));
	?>
	</fieldset>
<?php echo $this->Form->end(__('aceptar')); ?>
</div>
