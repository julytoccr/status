<?php
if($this->Session->read('usuario.lenguaje') != 'NOLANG'){
?>
<?php echo $this->Html->link(__('menu_generar_excel_lenguajes'),'/lenguajes/generar_excel_lenguaje')?>
<?php echo $this->Tooltip->show(__('menu_generar_excel_lenguajes'),__('explic_generar_excel_lenguajes'));?>

<?php echo $this->Html->link(__('menu_cargar_excel_lenguajes'),'/lenguajes/alta_excel_lenguajes')?>
<?php echo $this->Tooltip->show(__('menu_cargar_excel_lenguajes'),__('explic_cargar_excel_lenguajes'));?>

<?php echo $this->Html->link(__('menu_eliminar_lenguajes'),'/lenguajes/eliminar_lenguajes')?>
<?php echo $this->Tooltip->show(__('menu_eliminar_lenguajes'),__('explic_eliminar_lenguajes'));?>

<?php echo $this->Html->link(__('menu_cambiar_lenguaje_defecto'),'/lenguajes/cambiar_lenguaje_defecto')?>
<?php echo $this->Tooltip->show(__('menu_cambiar_lenguaje_defecto'),__('explic_cambiar_lenguaje_defecto'));?>

<?php echo $this->Html->link(__('menu_gestion_herencia'),'/lenguajes/gestion_herencia')?>
<?php echo $this->Tooltip->show(__('menu_gestion_herencia'),__('explic_gestion_herencia'));?>

<?php 	
}else{
echo $this->Html->link('Upload excel language registration','/lenguajes/alta_excel_lenguajes');
}
?>
