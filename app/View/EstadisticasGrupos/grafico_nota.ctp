<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('Estadisticas grupos'),
    "sidebar" => 'estadisticas/estadisticas_grupos'
));
$this->end();?>
<div align="center">
	<?php echo $this->Html->image($img_url,array('border'=>0,'alt'=>''))?>
</div>
