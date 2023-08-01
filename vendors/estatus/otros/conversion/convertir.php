<?php
	$mysql=mysql_connect("localhost","root","");
	mysql_select_db("estatus_import");

	$mssql=mssql_connect("tarso:1433","estatus","mna4721gh");
	mssql_select_db("estatus");

	$tablas=array(	"administradores",
					"agrupacion",
					"alumnos",
					"asignaciones",
					"asignaturas",
					"bloque",
					"carpetas",
					//"clases",
					"correcciones_parciales",
					"datos_diferidos",
					"datosiniciales",
					"docencia",
					"factor_ponderacion",
					"limite_diferidos",
					"preguntas",
					"problemas",
					//"problemas_visitante",
					"profesores",
					"prueba",
					//"prueba_visitante",
					"restricciones",
					"restricciones_grupos",
					"resultado_diferido",
					"soluciones",
					"subscripciones");
	//limpiar mysql_import
	foreach ($tablas as $tabla)
	{
		$query="delete from $tabla";
		mysql_query($query);
	}


	foreach ($tablas as $tabla)
	{
		echo "ini: $tabla<br/>"; flush();
		$result=mssql_query("select * from $tabla");
		$num_fields = mssql_num_fields ($result);
		$name_fields=array();
		for ( $f = 0 ; $f < $num_fields ; $f++ )
		{
   			$name_fields[$f] = mssql_fetch_field($result, $f);
			$name_fields[$f]=$name_fields[$f]->name;
		}
		while ($row=mssql_fetch_array($result))
		{
			insert_row($tabla,$row,$name_fields);
		}
		echo "OK: $tabla<br/>";flush();
	}
	echo "FIN!!<br/>";flush();


function insert_row($tabla,$row,$name_fields)
{
	$query="insert into $tabla (";

	for($i=0;$i<count($name_fields);$i++)
	{
		$query.=$name_fields[$i];
		if ($i<count($name_fields) - 1) $query.=',';
	}
	$query.=') values (';

	for($i=0;$i<count($name_fields);$i++)
	{
		$valor=mysql_escape_string($row[$i]);
		$query.="'$valor'";
		if ($i<count($name_fields) - 1) $query.=',';
	}
	$query.=')';
	mysql_query($query) or die("ERROR INSERTANDO $query");
}
?>