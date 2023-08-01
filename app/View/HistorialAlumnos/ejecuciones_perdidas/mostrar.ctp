<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('historial_alumno',''),
    "sidebar" => 'historial/historial',
    "alumno_id" => $alumno_id
));
$this->end();?>
<div id="aux"></div>
<?php
if (empty($data)) {
	echo __('no_hay_perdidos') . "<br /><br />\n";
}
else {
	foreach($data as $bloque)
	{
		echo "<table class=\"tabla_listado\">\n";
		echo "<tr>\n";
		echo "<th>{$bloque['Bloque']['nombre']}</th>\n";
		//echo '<th align="center">'.__('nota')."</th>\n";
		echo '<th align="center">'.__('fecha')."</th>\n";
		//echo "<th align=\"center\">&nbsp;</th>\n";
		echo "<th align=\"center\">&nbsp;</th>\n";
		echo "<tr/>\n";

		foreach ($bloque['Problemas'] as $problema)
		{
			foreach ($problema as $key=>$ejec) {
				if ($key === "nombre") continue;

				echo "<tr>\n";

				$url1="";
				$url2="";
				if ($es_profe) {
				    //echo '<td align="center">' . $html->link(__('ver'),'/ejecuciones/profesor_mostrar_resultado/' . $ejec['ejecucion_id']) . "</td>\n";
				    $url1 = "/ejecuciones/profesor_mostrar_resultado/" . $ejec['ejecucion_id'];
				    $url2 = '/historial_alumnos/confirmar_recuperacion/' . $ejec['ejecucion_id'] . "/" . $alumno_id;
				}
				else {
				    //echo '<td align="center">' . $html->link(__('ver'),'/ejecuciones/alumno_mostrar_resultado/' . $ejec['ejecucion_id']) . "</td>\n";
				    $url1 = "/ejecuciones/alumno_mostrar_resultado/" . $ejec['ejecucion_id'];
				    $url2 = '/historial_alumnos/confirmar_recuperacion/' . $ejec['ejecucion_id'];
				}

				echo '<td>' . $this->Html->link($problema['nombre'], $url1) . "</td>\n";

				//echo '<td align="center">' . $ejec['nota'] . "</td>\n";
				echo '<td align="center">' . $this->Html->link($ejec['fecha'], $url1) . "</td>\n";

				//echo '<td align="center"><a href="/index.php/ejecuciones/aceptar_nota/' . $ejec['ejecucion_id'] . '" onclick="javascript:return confirm(\'' . preg_replace("/\r{0,1}\n/", "\\n", str_replace("'", "\'", __('confir_recuperar_ejecucion'))) . '\')">' . __('correccion') . "</a></td>\n";
				echo '<td align="center">' . $this->Html->link(__('recuperar'), $url2) . "</td>\n";
				
				echo "</td></tr>\n";
			}
		}
		echo "</table><br/>\n";
	}
}

?>
