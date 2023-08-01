<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('asignaciones'),
    "sidebar" => 'bloques/asignaciones'
));
$this->end();?>
<div id="aux"></div>
<?php echo $this->Form->create()?>
<div align="center">
<?php 
echo $this->element(
	'tree/arboles',
	compact('propias', 'subscritas', 'bloques', 'problemas')
);
?>
</div>
<?php echo $this->Form->end()?>
