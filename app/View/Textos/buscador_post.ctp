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
<table width="100%"><tr>
<?php 
if($tipo_busqueda==1){
echo '<td width="90%" valign="top">'.__('busqueda_etiquetas_cadena',array($busqueda)).'</td>';
}
else if($tipo_busqueda==2){
echo '<td width="90%" valign="top">'.__('busqueda_textos_cadena',array($busqueda)).'</td>';
}
else if($tipo_busqueda==3){
echo '<td width="90%" valign="top">'.__('busqueda_todas_entradas').'</td>';
}

?>

<td width="10%" valign="top"><?php echo $this->Html->link(__('volver'),'/textos/buscar_etiquetas',null); ?></td>
</tr></table>
<?php
$busqueda2="'".$busqueda."'";
echo '<select name="data[Lenguaje][id][]"   id="LenguajeId" onChange="cambiar_lenguaje_buscador('.$cont_ses_buscador.', '.$busqueda2.', '.$tipo_busqueda.', '.$actual_page.');">'; ?>
<?php $i=0;
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
if($lenguajes[$lenguaje_actual]['lenguaje_hereda']!=0){
echo __('si_no_texto_hereda', array($lenguajes[$lenguaje_actual]['lenguaje_hereda_texto']));
}
?>
<br>
<table class="tabla_listado">
	<tr>	<th width="20%"></th>
		<th width="20%"><?php echo __('etiqueta'); ?></th>		
		<th width="60%"><?php echo __('texto'); ?></th>
	</tr>
<?php 
foreach($etiquetas as $e):
?>
	<tr>
		<td><?php echo $this->Html->link(__('gestionar_termino'),'/textos/buscador_todos_lenguajes/'.$cont_ses_buscador.'/'.$busqueda.'/'.$tipo_busqueda.'/'.$e['id'].'/'.$lenguaje_actual.'/'.$actual_page); ?></td>
		<td><?php echo $e['cadena'] ?></td>
		<td><?php echo $e['textos'][$lenguaje_actual]['texto'] ?></td>
	</tr>
<?php 
endforeach; ?>
</table>



