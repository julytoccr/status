<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('idiomas'),
    "sidebar" => 'lenguajes/menu'
));
$this->end();?>
<?php
echo $this->Form->create('Lenguajes',array('action'=>'cambiar_lenguaje_herencia'));
echo $this->Form->input('Lenguaje.lenguaje_prioritario', array(
    'options' => $lenguajes2,
    'label'=>__('form_seleccion_lenguaje_heredar')
));
echo $this->Form->input('Lenguaje.lenguaje_prioritario', array(
    'type' => 'hidden',
    'value' => $heredero
));
echo $this->Form->end(__('aceptar'));
?>

