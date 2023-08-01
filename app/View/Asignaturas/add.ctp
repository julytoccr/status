<?php
$this->start('script');
echo $this->Html->script('jquery-ui-1.8.13.custom.min');
echo $this->Html->script('jquery-ui-timepicker-addon');
$this->end();
$this->start('css');
echo $this->Html->css('jquery-ui/jquery-ui-1.8.13.custom');
$this->end();
?>
<?php echo $this->Form->create('Asignatura'); ?>
	<fieldset>
	<?php
		echo $this->Form->input('nombre',array('label'=>__('form_nombre')));
		echo $this->Form->input('descripcion',array('label'=>__('form_descripcion')));
		echo $this->Form->input('asignar_todos_publicados',array('type' => 'checkbox',array('label'=>__('form_asignar_auto_problemas'))));
		echo $this->Form->input('activar_fecha_inicio',array('type'=>'checkbox','id'=>'activar_fecha_inicio',array('label'=>__('form_restringir_fecha_inicio'))));
		echo $this->Form->input('fecha_inicio',array('type' => 'text','class'=>'datepicker','div' => array('id'=>'fecha_inicio','style'=>'display:none'),array('label'=>__('form_fecha_inicio'))));
		echo $this->Form->input('activar_fecha_fin',array('type'=>'checkbox','id'=>'activar_fecha_fin',array('label'=>__('form_restringir_fecha_fin'))));
		echo $this->Form->input('fecha_fin',array('type' => 'text','class'=>'datepicker','div' => array('id'=>'fecha_fin','style'=>'display:none'),array('label'=>__('form_fecha_fin'))));
	?>
	</fieldset>
<?php echo $this->Form->end(__('form_guardar')); ?>
<?php
echo $this->Html->scriptBlock(
	"function activar(checkbox,element){
		if(checkbox.is(':checked'))
			element.show();
		else
			element.hide();
	}
	"
);
$activar_inicio="activar($('#activar_fecha_inicio'),$('#fecha_inicio'));";
$activar_fin="activar($('#activar_fecha_fin'),$('#fecha_fin'));";
echo $this->Js->get('#activar_fecha_inicio')->event('change',$activar_inicio);
echo $this->Js->get('#activar_fecha_fin')->event('change',$activar_fin);
echo $this->Js->buffer("
	if($('#activar_fecha_inicio').is(':checked'))
			$('#fecha_inicio').show();
	if($('#activar_fecha_fin').is(':checked'))
			$('#fecha_fin').show();
	$('.datepicker').datetimepicker({dateFormat: 'yy-mm-dd',timeFormat: 'hh:mm'});
");
echo $this->Js->writeBuffer();
?>
