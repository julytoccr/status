<h1><?php echo __('calculadora_chicuadrado'); ?></h1>
<table width="100%">
<tr>
<td>
	<?php echo $this->Form->create(null,array('onSubmit' => 'return false;'));?>
	<?php echo $this->Form->input('TablasEstadisticas.x',array('label'=>__('form_valorx')));?>
	<?php echo $this->Form->input('TablasEstadisticas.glib',array('label'=>__('form_grados_libertad')));?>
	<div id="resultado_chi2">
	</div>
	<br/>
	<?php
	echo $this->Js->submit(__('calcular'), array(
			'url' => '/tablas_estadisticas/post_chi2',
			'update' => '#resultado_chi2',
			'buffer' => false
		)
	);
	echo $this->Form->end();
	?>
</td>
<td>
	<?php echo $this->Form->create(null,array('onSubmit' => 'return false;'));?>
	<?php echo $this->Form->input('TablasEstadisticas.p',array('label'=>__('form_pvalor')));?>
	<?php echo $this->Form->input('TablasEstadisticas.glib',array('label'=>__('form_grados_libertad')));?>
	<div id="resultado_chi2_inversa">
	</div>
	<br/>
	<?php
	echo $this->Js->submit(__('calcular'), array(
			'url' => '/tablas_estadisticas/post_chi2_inversa',
			'update' => '#resultado_chi2_inversa',
			'buffer' => false
		)
	);
	echo $this->Form->end();
	?>
</td>
</tr>
<script>
<?php 
	echo $this->Js->get('#tablas')->effect('fadeIn');
?>
</script>
<tr>
<td colspan="2" align="center">
	<?php
		echo $this->Js->link('cerrar', '', array('success' => $this->Js->get('#tablas')->effect('slideUp')));
		echo $this->Js->writeBuffer();
	?>
</td>
</tr>
</table>
