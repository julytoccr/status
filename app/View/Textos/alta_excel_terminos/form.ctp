<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('gestionar_terminos'),
    "sidebar" => 'textos/textos'
));
$this->end();?>
<?php
	echo $this->Form->create('Textos',array('url'=>'alta_excel_nuevos_terminos','type'=>'file'));
	echo $this->Form->input('Termino.excel',array('type'=>'file'));
	echo $this->Form->end(__('enviar'));
?>
