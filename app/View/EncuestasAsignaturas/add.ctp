<?php
$this->start('script');
echo $this->Html->script('jquery-ui-1.8.13.custom.min');
echo $this->Html->script('jquery-ui-timepicker-addon');
$this->end();
$this->start('css');
echo $this->Html->css('jquery-ui/jquery-ui-1.8.13.custom');
$this->end();
?>
<div class="encuestas form">
<?php echo $this->Form->create('EncuestasAsignatura'); ?>
	<fieldset>
	<?php
		echo $this->Form->input('activar_fecha_inicio',array('label'=>__('form_restringir_fecha_inicio'),'type'=>'checkbox','id'=>'activar_fecha_inicio'));
		echo $this->Form->input('fecha_inicio',array('label'=>__('form_fecha_inicio'),'type' => 'text','class'=>'datepicker','div' => array('id'=>'fecha_inicio','style'=>'display:none')));
		echo $this->Form->input('activar_fecha_fin',array('label'=>__('form_restringir_fecha_fin'),'type'=>'checkbox','id'=>'activar_fecha_fin'));
		echo $this->Form->input('fecha_fin',array('label'=>__('form_fecha_fin'),'type' => 'text','class'=>'datepicker','div' => array('id'=>'fecha_fin','style'=>'display:none')));
		echo $this->Form->input('activo',array('label'=>__('form_activo'),'type'=>'checkbox'));
		echo $this->Form->input('encuesta_id',array('type'=>'hidden','value'=>$encuesta_id));
	?>
	</fieldset>
<?php echo $this->Form->end(__('form_guardar')); ?>
</div>
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
	$('.datepicker').datetimepicker({dateFormat: 'yy-mm-dd',timeFormat: 'hh:mm'});
");
echo $this->Js->writeBuffer();
?>
