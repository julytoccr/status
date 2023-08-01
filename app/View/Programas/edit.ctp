<?php echo $this->Form->create('Programa'); ?>
	<fieldset>
	<?php
		echo $this->Form->input('nombre',array('size'=>50,'maxlenght'=>100, 'label'=>__('form_nombre')));
		echo $this->Form->input('descripcion',array('rows'=>6,'cols'=>50,'label'=>__('form_descripcion')));
		echo $this->Form->input('expresiones',array('rows'=>10,'cols'=>90,'label'=>__('form_expresiones')));
		echo $this->Form->input('publico',array('type'=>'checkbox','label'=>__('form_publico_profesores')));
		for ($i = 1; $i <= 7; $i++) {
			echo $this->Form->input('param'.$i,array('size'=>50,'maxlenght'=>100, 'label'=>__('form_descripcion_parametro','')));
			echo $this->Form->input('tipo_param'.$i,array('options'=>$tipo_params,'label'=>__('form_tipo_parametro','')));
		}
	?>
	</fieldset>
<?php echo $this->Form->end(__('form_guardar')); ?>
<br/>
<b>Ayuda:</b>
<ul>
	<li><b><?php echo __('acceso_parametros'); ?></b><?php echo __('variables_parametros'); ?></li>
	<li><b><?php echo __('consulta_sql'); ?></b> dat=estatus.fetch_all("select * from XXXX");</li>
	<li><b><?php echo __('devolver_mensaje'); ?></b> imprimir("Numero registros");imprimir(length(dat[[1]]))</li>
	<li><b><?php echo __('devolver_grafico'); ?></b> <?php echo __('condicion_grafico'); ?></li>
</ul>
