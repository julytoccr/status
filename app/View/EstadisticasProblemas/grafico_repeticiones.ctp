<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('Estadisticas'),
    "sidebar" => 'estadisticas/problemas/problemas',
    "id" => $problema_id
));
$this->end();?>
<div align="center">
	<?php echo $this->Html->image($img_url,array('border'=>0,'alt'=>''))?>
</div>
