<?php
$this->start('script');
echo $this->Html->script('jquery-ui-1.8.13.custom.min');
echo $this->Html->script('jquery-ui-timepicker-addon');
$this->end();
$this->start('css');
echo $this->Html->css('jquery-ui/jquery-ui-1.8.13.custom');
$this->end();
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('estadisticas'),
    "sidebar" => 'estadisticas/asignaturas'
));
$this->end();
?>
<?php 
	echo $this->Form->create();
	echo $this->Form->input('Usuario.fecha_inicio',array('label'=>__('form_fecha_inicio'),'class'=>'datepicker'));
	echo $this->Form->input('Usuario.fecha_fin',array('label'=>__('form_fecha_fin'),'class'=>'datepicker'));
	echo $this->Form->end(__('aceptar'));
?>
<?php
echo $this->Js->buffer("$('.datepicker').datetimepicker({dateFormat: 'yy-mm-dd',timeFormat: 'hh:mm'});");
echo $this->Js->writeBuffer();
?>
