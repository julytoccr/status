<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('idiomas'),
    "sidebar" => 'lenguajes/menu'
));
$this->end();?>
<?php
echo $this->Form->create('Lenguajes',array('action'=>'generar_excel_lenguaje'));
echo $this->Form->input('Lenguaje.id', array(
    'options' => $lenguajes,
    'multiple' => 'multiple',
    'label'=>__('form_seleccion_lenguaje')
));
echo $this->Form->end(__('aceptar'));
?>
