<?php
$this->start('script');
echo $this->Html->script('lenguajes');
$this->end();
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('gestionar_terminos'),
    "sidebar" => 'textos/textos'
));
$this->end();
?>
<?php
echo $this->Html->link(__('alta'),'/textos/guardar_excel_terminos/'.$cont_ses_terminos,null, __('confirmar_alta_terminos'))?> | 
<?php echo $html->link(__('volver_cargar'),'/textos/alta_excel_nuevos_terminos')?>
<br>


<?php
echo '<select name="data[Lenguaje][id][]"   id="LenguajeId" onChange="cambiar_lenguaje_excel_nuevos_terminos('.$cont_ses_terminos.', '.$actual_page.');">';


$i=0;
foreach($lenguajes as $leng):
	if($i==$lenguaje_actual){
		echo '<option selected="selected" value="'.$i.'" >'.$leng['lenguaje_propio'].' ('.$leng['lenguaje_estandard'].')</option>';
	}else{
		echo '<option value="'.$i.'" >'.$leng['lenguaje_propio'].' ('.$leng['lenguaje_estandard'].')</option>';	
	}
$i=$i+1;

endforeach;

?>
</select><br>

<?php

echo $this->CustomPagination->show_links($page_url,$contexto=5);
if($lenguajes[$lenguaje_actual]['lenguaje_hereda']!=''){
echo __('si_no_texto_hereda', array($lenguajes[$lenguaje_actual]['lenguaje_hereda']));
}

?>
<br>

<table class="tabla_listado">
	<tr>
		<th width="20%"><?php echo __('etiqueta'); ?></th>		
		<th width="80%"><?php echo __('texto'); ?></th>
		
		

	</tr>	

		
<?php 

foreach($etiquetas as $e): 

?>
	<tr>
		<td><?php echo $e['cadena'] ?></td>
		
		<td><?php echo $e['textos'][$lenguaje_actual]['texto'] ?></td>
		
		
	</tr>
<?php 
endforeach; ?>
</table>



