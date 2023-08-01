<?php 
$head->js('lenguajes');
if($_SESSION['hay_lenguaje']==1)
{
echo $html->link(__('alta'),'/admin_lenguajes/guardar_excel_lenguajes/'.$cont_ses_lenguajes,null,__('confirmar_alta_lenguajes')).' | '; 
echo $html->link(__('volver_cargar'),'/admin_lenguajes/alta_excel_lenguajes');
}else{
echo $html->link('Register','/admin_lenguajes/guardar_excel_lenguajes/'.$cont_ses_lenguajes,null,'Seguro que quieres dar de alta los lenguajes?').' | '; 
echo $html->link('Go back to upload','/admin_lenguajes/alta_excel_lenguajes');
}
?>
<br>
<?php
echo '<select name="data[Lenguaje][id][]"   id="LenguajeId" onChange="cambiar_lenguaje_excel('.$cont_ses_lenguajes.', '.$actual_page.');">';

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
	if($_SESSION['hay_lenguaje']==1)
	{
		echo __('si_no_texto_hereda', array($lenguajes[$lenguaje_actual]['lenguaje_hereda']));
	}else
	{
		echo 'If the text is empty it iherit from: '.$lenguajes[$lenguaje_actual]['lenguaje_hereda'];
	}
}
?>



<table class="tabla_listado">
	<tr>
		<?php
		if($_SESSION['hay_lenguaje']==1)
		{
			echo '<th width="20%">'.__('etiqueta').'</th>';		
			echo '<th width="80%">'.__('texto').'</th>';
		}else{
		
			echo '<th width="20%">Label</th>';		
			echo '<th width="80%">Text</th>';
		}
		?>

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



