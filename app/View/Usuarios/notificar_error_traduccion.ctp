<?php $attributes = array('rows'=>4,'cols'=>60)?>
<?php echo __('nota_notificaciones_error_trad')?>
<?php echo $this->Form->create('Usuario'); ?>
	<fieldset>
	<?php
		echo $this->Form->hidden('CorreccionTextos.lenguaje', array('value'=>$lenguaje_actual));
		echo $this->Form->hidden('CorreccionTextos.url', array('value'=>$url_anterior));
		echo $this->Form->hidden('CorreccionTextos.fecha', array('value'=>$fecha));
		echo $this->Form->hidden('CorreccionTextos.mail', array('value'=>$mail));
		echo $this->Form->hidden('CorreccionTextos.login', array('value'=>$login));
		echo $this->Form->input('CorreccionTextos.texto_actual', am(array('label'=>__('form_texto_actual'),'type'=>'textarea'),$attributes));
		echo $this->Form->input('CorreccionTextos.texto_correccion', am(array('label'=>__('form_sugerencia'),'type'=>'textarea'),$attributes));
		echo $this->Form->input('CorreccionTextos.notas_adicionales', am(array('label'=>__('form_notas_adicionales'),'type'=>'textarea'),$attributes));
	?>
	</fieldset>
<?php echo $this->Form->end(__('aceptar')); ?>
