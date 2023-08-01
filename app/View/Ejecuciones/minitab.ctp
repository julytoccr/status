<b><?php if ($separador == '&#09;') echo __('nota_copiar_minitab'); ?></b>
<br/>
<i><?php if ($separador == '&#09;') echo __('nota_pegar_excel'); ?></i>
<div id="minitab" class="tabla_minitab">
	<?php echo $tabla_html;	?>
</div>

<?php
	echo $this->Form->create(null,array('url'=>'/ejecuciones/minitab/'.$ejecucion_id));
	echo $this->Form->input('Ejecucion.simbolo_decimal',
		array('options'=>
			array(
				0=>__('form_simbolo_punto'),
				1=>__('form_simbolo_coma')
			),
			'default' => 0
		)
	);
	echo $this->Form->input('Ejecucion.separador_columnas',
		array('options'=>
			array(
				0=>__('form_separador_tabulador'),
				1=>__('form_separador_espacio'),
				2=>__('form_separador_puntocoma'),
				3=>__('form_separador_dospuntos'),
				4=>__('form_separador_dolar'),
				5=>__('form_separador_barra'),
			),
			'default'=>0
		)
	);
	echo $this->Form->submit(__('enviar'));
	echo $this->Form->end();
?>
