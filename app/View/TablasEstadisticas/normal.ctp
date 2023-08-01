<h1><?php echo __('calculadora_normal'); ?></h1>
<table width="100%">
<tr>
<td>
	<?php echo $this->Form->create(null,array('onSubmit' => 'return false;'));?>
	<?php echo $this->Form->input('TablasEstadisticas.x',array('label'=>__('form_valorx')));?>
	<div id="resultado_normal" style="text-align:center">
	</div>
	<br/>
	<?php
	echo $this->Js->submit(__('calcular'), array(
			'url' => '/tablas_estadisticas/post_normal',
			'update' => '#resultado_normal',
			'buffer' => false
		)
	);
	echo $this->Form->end();
	?>
</td>
<td>
	<?php echo $this->Form->create(null,array('url'=>'/tablas_estadisticas/post_normal_inversa'));?>
	<?php echo $this->Form->input('TablasEstadisticas.p',array('label'=>__('form_pvalor')));?>

	<div id="resultado_normal_inversa" style="text-align:center">
	</div>
	<br/>
	<?php
	echo $this->Js->submit(__('calcular'), array(
			'url' => '/tablas_estadisticas/post_normal_inversa',
			'update' => '#resultado_normal_inversa',
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
