<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('historial_alumno',''),
    "sidebar" => 'historial/historial',
    "alumno_id" => $alumno_id
));
$this->end();?>
<div align="center">
<?php if (!isset($no_data)): ?>
	<?php echo $this->Html->image($img_url,array('usemap'=>'#map','border'=>0,'alt'=>''))?>
	<?php echo $map?>

<?php endif; ?>

<?php if(isset($no_data)): ?>
	<?php echo __('no_ejecuciones');?>
<?php endif;?>

</div>
