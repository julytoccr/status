<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('estadisticas'),
    "sidebar" => 'estadisticas/estadisticas_problemas'
));
$this->end();?>
<div align="center">
	<?php echo $this->Html->image($img_url,array('usemap'=>'#map','border'=>0,'alt'=>''))?>
	<?php echo $map?>
</div>
