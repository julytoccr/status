<?php echo __('tiempo_limite'); ?>:
<?php
if ($expirado)
{
	echo '<span class="alerta_tiempo">'.__('expirado').'</span>';
}
else
{
	$texto='';
	$tiempo = strtotime($fecha_limite) - time();
	$segundos= $tiempo;
	$minutos=floor($segundos / 60);
	$segundos=$segundos - 60*$minutos;
	
	$str = $segundos.' '.__('segundos_abrev');
	
	if($minutos < 60)
	{
		$str = $str = $minutos.' '.__('minutos_abrev').' '. $str;
	}
	else
	{
			$horas =  floor($minutos / 60);
			$minutos = $minutos - 60 * $horas;

			$str = $minutos.' '.__('minutos_abrev').' '. $str;

			if($horas < 24)
			{
				if($horas == 1)
					$str = __('hora', array(1)).' '. $str;
				else
					$str = __('horas', array($horas)).' '. $str;
			}
			else
			{
				$dias = floor($horas / 24);
				$horas = $horas - 24 * $dias;
				
				if($horas == 1)
					$str = __('hora', array(1)).' '. $str;
				else
					$str = __('horas', array($horas)).' '. $str;
				
				if($dias == 1)
					$str = __('dia', array(1)).' '. $str;
				else
					$str = __('dias', array($dias)).' '. $str;
			}
	}
	
	if ($tiempo < 60)
	{
		echo '<span class="alerta_tiempo">';
	}
	
	echo $str;

	if ($tiempo < 60)
	{
		echo '</span>';
	}
	
	
}
?>
