<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('historial_alumno',''),
    "sidebar" => 'historial/historial',
    "alumno_id" => $alumno_id
));
$this->end();?>
<div align="center">
	<?php echo $this->Html->image($img_url,array('usemap'=>'#map','border'=>0,'alt'=>''))?>
	<?php echo $map?>
</div>
