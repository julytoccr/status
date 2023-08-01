<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('idiomas'),
    "sidebar" => 'lenguajes/menu'
));
$this->end();?>
