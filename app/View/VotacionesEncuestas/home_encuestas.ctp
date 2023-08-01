<?php if ($data):?>
	<h2><?php echo __('encuestas'); ?></h2>
	<br/>
<?php endif; ?>

<?php
	foreach ($data as $enc)
	{
		echo $this->element('votaciones/encuesta',array('encuesta'=>$enc));
	}
?>
