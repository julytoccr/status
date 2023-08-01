<?php echo __('indicar_xml_restaurar_problema'); ?>
<br/>
<?php echo __('aviso_sobreescritura_problema'); ?>
<div align="center">
<?php
	echo $this->Form->create("/problemas/restaurar_xml/$problema_id/$carpeta_id",array('enctype'=>'multipart/form-data'));
	echo $this->Form->file('Problema.XML');
	echo $this->Form->end(__('enviar'));
?>
</div>
