<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('idiomas'),
    "sidebar" => 'lenguajes/menu'
));
$this->end();?>
<?php
echo $this->Form->create('Lenguajes',array('action'=>'cambiar_lenguaje_defecto'));
echo $this->Form->input('Lenguaje.id', array('options' => $lenguajes,'label'=>__('form_seleccion_lenguaje')));
echo $this->Form->end(__('aceptar'));
?>
