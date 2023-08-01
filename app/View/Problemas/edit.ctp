<?php echo $this->Form->create('Problema'); ?>
<fieldset>
<?php
	if($action=='edit')
		echo $this->Form->input('id');
	echo $this->Form->input('nombre',array('size'=>50,'maxlenght'=>100,'label'=>__('form_nombre')));
	echo $this->Form->input('descripcion',array('rows'=>7,'cols'=>50,'label'=>__('form_descripcion')));
	echo $this->Form->input('enunciado',array('style'=>'width:100%;height:200px;','rows'=>4,'cols'=>40,'label'=>__('form_enunciado')));
	echo $this->Form->input('expresiones',array('class'=>'text_fixed','rows'=>13,'cols'=>90,'label'=>__('form_expresiones')));
	echo $this->Form->input('dificultad_id',array('options'=>array(1=>__('facil'),2=>__('medio'),3=>__('dificil'))));
	echo $this->Form->input('expresiones_publicas',array('label'=>__('form_expresiones_publicas'),'type'=>'checkbox'));
	echo $this->Form->input('variables_mostrar',array('type'=>'text','size'=>50,'maxlenght'=>100,'label'=>__('form_variables_mostrar')));
	if($action=='add')
		echo $this->Form->input('carpeta_id',array('type'=>'hidden','value'=>$carpeta_id));
?>
</fieldset>
<span align="center" style="display:block">
<?php 
	if (! isset($visualizar)){
		echo $this->Form->submit(__('form_guardar'),array('div'=>false));
		echo $this->Form->submit(__('Editar preguntas'), array('div'=>false,'name'=>'editar_preguntas'));
		echo $this->Form->submit(__('restaurar_xml'), array('div'=>false,'name'=>'restaurar_xml'));
		if($action=='edit'){
			echo $this->Form->submit(__('generar_xml'), array('div'=>false,'name'=>'generar_xml'));
		}
	}
	echo $this->Form->submit(__('Prueba problema'), array('div'=>false,'name'=>'probar_problema')); 
?>
</span>
