<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('avanzado'),
    "sidebar" => 'avanzado/avanzado'
));
$this->end();?>
