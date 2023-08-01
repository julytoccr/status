<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('editor'),
    "sidebar" => 'editor/menu'
));
$this->end();?>
<div id="aux"></div>
<?php echo $this->Form->create(array('id'=>'EditorForm'))?>
<div align="center">
<?php 
echo $this->element(
	'tree/arboles',
	compact('propias', 'subscritas', 'no_subscritas', 'problemas','action')
);
?>
</div>
<?php echo $this->Form->end()?>
