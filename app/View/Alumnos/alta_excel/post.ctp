<?php
if (isset($limit)) echo "<div class=\"flash_error\" style=\"width: 100%\">Error: " . $limit . "</div>";
echo "<div class=\"flash_info\" style=\"width: 100%; margin-left: 0%; margin-right: 0%; margin-top: 0%; margin-bottom: 0%\" >Info: " . __('info_max_altes_suhosin', array($readscount, $lastuser)) . "</div>";

echo $this->Form->create(null,array('url'=>'/alumnos/alta_excel_final'));

?>

<table class="tabla_form">
	<tr>
		<th><?php echo __('nombre'); ?></th>
		<th><?php echo __('apellidos'); ?></th>
		<th><?php echo __('email'); ?></th>
		<th><?php echo __('login'); ?></th>
		<th><?php echo __('grupo'); ?></th>
		<th><?php echo __('form_repetidor'); ?></th>
	</tr>
<?php $i = 1; foreach ($alumnos as $a) { ?>
	<tr>
		<td><?php echo $this->Form->hidden('Alumno.tipo_auth_' . $i,array('value'=>$a['tipo_auth'],'size'=>2))  ?>
		<?php echo $this->Form->input('Alumno.nombre_' . $i,array('value'=>$a['nombre'],'size'=>15))  ?></td>
		<td><?php echo $this->Form->input('Alumno.apellidos_' . $i,array('value'=>$a['apellidos'],'size'=>15))  ?></td>
		<td><?php echo $this->Form->input('Alumno.email_' . $i,array('value'=>$a['email'],'size'=>15))  ?></td>
		<td><?php echo $this->Form->input('Alumno.login_' . $i,array('value'=>$a['login'],'size'=>11))  ?></td>
		<td><?php echo $this->Form->input('Alumno.grupo_' . $i,array('value'=>$a['grupo'],'size'=>2))  ?></td>
		<td><?php echo $this->Form->input('Alumno.repetidor_' . $i,array('value'=>$a['repetidor'],'size'=>2))  ?></td>
	</tr>
<?php ++$i; } ?>
</table>
<div align="center">
	<b><?php echo __('form_tipo_autentificacion').':'; ?> <?php echo $tipo_auth?></b>
	<br/>
	<?php echo $this->Form->end(__('aceptar'))?>
</div>
