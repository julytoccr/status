<?php
	vaciar("usuarios");
	vaciar("administradores");
	vaciar("asignaturas");
	vaciar("alumnos");
	vaciar("grupos");
	vaciar("bloques");
	vaciar("profesores");
	vaciar("docencia");
	vaciar("carpetas");
	vaciar("preguntas");
	vaciar("problemas");
	vaciar("carpetas_problemas");
	vaciar("agrupaciones");
	vaciar("ejecuciones");
	vaciar("respuestas_ejecuciones");
	vaciar("restricciones_bloques");


	//administradores
	$result=consultar("select * from administradores");
	while ($row=mysql_fetch_array($result))
	{
		$data=array('nombre'=>$row['nombre'],
					'apellidos'=>$row['apellidos'],
					'email'=>$row['email'],
					'login'=>$row['identificador'],
					'password'=>$row['pass'],
					'tipo_auth'=>'plain');
		$id=insert_row("usuarios",$data,false);
		$data=array('id'=>$id);
		insert_row("administradores",$data);
	}
	ok("administradores");

	//asignaturas
	$asignaturas=array();
	$result=consultar("select * from asignaturas");
	while ($row=mysql_fetch_array($result))
	{
		$data=array('nombre'=>$row['identificador'] . ' ' . $row['nombre'],
					'descripcion'=>$row['descripcion']);

		$id=insert_row("asignaturas",$data);
		$asignaturas[$row['identificador']]=$id;
	}
	ok("asignaturas");


	//alumnos + grupos
	$alumnos=array();
	$result=consultar("select * from alumnos");
	while ($row=mysql_fetch_array($result))
	{
		if ($row['fib']=='no') $tipo_auth='plain';
		else $tipo_auth='fib';

		if ($row['repetidor']=='no') $repetidor=0;
		else $repetidor=1;
		$antiguo_alumno_id=$row['identificador'];


		$data=array('nombre'=>$row['nombre'],
					'apellidos'=>$row['apellidos'],
					'email'=>$row['email'],
					'login'=>$row['identificador'],
					'password'=>$row['password'],
					'tipo_auth'=>$tipo_auth);
		$usuario_id=insert_row("usuarios",$data,false);

		if ($usuario_id==-1)
		{
			$result2=consultar_destino("select * from usuarios where login='{$row['identificador']}'");
			if (!$row2=mysql_fetch_array($result2))
			{
				die("Error en {$row['identificador']}");
			}
			$usuario_id=$row2['id'];
		}


		if (isset($grupos[$row['asignatura']][$row['grupo']]))
		{
			$grupo_id=$grupos[$row['asignatura']][$row['grupo']];
		}
		else
		{
			$data=array('asignatura_id'=>$asignaturas[$row['asignatura']],
						'nombre'=>$row['grupo']);
			$grupo_id=insert_row("grupos",$data);
			$grupos[$row['asignatura']][$row['grupo']]=$grupo_id;
		}

		$data=array(	'usuario_id'=>$usuario_id,
						'alias'=>$row['alias'],
						'repetidor'=>$repetidor,
						'grupo_id'=>$grupo_id);
		$alumno_id=insert_row("alumnos",$data);
		$alumnos[$antiguo_alumno_id . '___' . $row['asignatura']]=$alumno_id;
	}
	ok("alumnos - grupos");


	//bloques
	$result=consultar("select * from bloque");
	while ($row=mysql_fetch_array($result))
	{
		$data=array('id'=>$row['identificador'],
					'nombre'=>$row['nombre'],
					'tipo'=>$row['tipo'],
					'modified'=>convertir_fecha($row['fechamodificacion']),
					'asignatura_id'=>$asignaturas[$row['idasig']]);
		insert_row("bloques",$data);
	}
	ok("bloques");



	//profesores
	$profesores=array();
	$result=consultar("select * from profesores");
	while ($row=mysql_fetch_array($result))
	{
		if ($row['fib']=='no') $tipo_auth='plain';
		else $tipo_auth='fib';

		$data=array('nombre'=>$row['nombre'],
					'apellidos'=>$row['apellidos'],
					'email'=>$row['email'],
					'login'=>$row['identificador'],
					'password'=>$row['password'],
					'tipo_auth'=>$tipo_auth);

		$id=insert_row("usuarios",$data,false);
		if ($id==-1)
		{
			$result2=consultar_destino("select * from usuarios where login='{$row['identificador']}'");
			if (!$row2=mysql_fetch_array($result2))
			{
				die("Error en {$row['identificador']}");
			}
			$id=$row2['id'];
		}
		$profesores[strtolower($row['identificador'])]=$id;

		$data=array('id'=>$id);
		insert_row("profesores",$data);
	}
	ok("profesores");


	//carpetas
	$result=consultar("select * from carpetas");
	while ($row=mysql_fetch_array($result))
	{
		if ($row['compartida']=='S') $compartida=1;
		else $compartida='N';

		$prof=$row['idprofesor'];
		if ($prof)
		{
			$profesor_id=$profesores[strtolower($prof)];


			$data=array('nombre'=>$row['nombre'],
						'id'=>$row['idcarpeta'],
						'profesor_id'=>$profesor_id,
						'compartida'=>$compartida);

			insert_row("carpetas",$data);
		}
	}
	ok("carpetas");

	//docencia
	$result=consultar("select * from docencia");
	while ($row=mysql_fetch_array($result))
	{

		$data=array('profesor_id'=>$profesores[strtolower($row['idprofesor'])],
					'asignatura_id'=>$asignaturas[$row['idasignatura']]);
		insert_row("docencia",$data);
	}
	ok("docencia");

	//problemas
	$result=consultar("select * from problemas");
	while ($row=mysql_fetch_array($result))
	{
		if ($row['verexpresiones']=='S') $expresiones_publicas=1;
		else $expresiones_publicas=0;

		$result2=consultar("select * from datosiniciales where idproblema={$row['identificador']}");
		$datos_iniciales=array();
		while ($row2=mysql_fetch_array($result2))
		{
			$datos_iniciales[]=$row2['nombrecampo'];
		}
		$datos_iniciales=join(',',$datos_iniciales);
		$row['autor']=trim($row['autor']);
		if (!isset($profesores[strtolower($row['autor'])]))
		{
			$row['autor']='jose a gonzalez';
		}

		$data=array(	'id'=>$row['identificador'],
						'nombre'=>$row['nombre'],
						'descripcion'=>$row['descripcion'],
						'enunciado'=>$row['enunciado'],
						'expresiones'=>$row['codigo'],
						'dificultad_id'=>$row['dificultad'] + 1,
						'published'=>convertir_fecha($row['fechapublicacion']),
						'created'=>convertir_fecha($row['fechacreacion']),
						'modified'=>convertir_fecha($row['fechamodificacion']),
						'expresiones_publicas'=>$expresiones_publicas,
						'profesor_id'=>$profesores[strtolower($row['autor'])],
						'variables_mostrar'=>$datos_iniciales);

		insert_row("problemas",$data);

		$result2=consultar("select * from preguntas where idproblema={$row['identificador']}");
		$problema_id=$row['identificador'];
		while ($row2=mysql_fetch_array($result2))
		{
			$expresiones_respuesta="if(estatus.error_relativo(solucion_,respuesta_)<=parametro_)
{
  resultado_<-'1';
}else{
   resultado_<-'0';
}";

			$ayuda=$row2['helpContext'];
			if ($ayuda=' ') $ayuda='';

			$link1=$row2['helpURL1'];
			if ($link1=' ') $link1='';

			$link2=$row2['helpURL2'];
			if ($link2=' ') $link2='';

			$data=array(
						'problema_id'=>$problema_id,
						'orden'=>$row2['orden'],
						'enunciado'=>$row2['enunciado'],
						'respuesta'=>$row2['respuesta'],
						'sintaxis_respuesta_id'=>8, //real
						'sintaxis_parametro'=>'',
						'expresiones_respuesta'=>$expresiones_respuesta,
						'expresiones_parametro'=>$row2['error'],
						'peso'=>$row2['peso'],
						'intentos'=>1,
						'ayuda'=>$ayuda,
						'link1'=>$link1,
						'link2'=>$link2,
						'mostrar_solucion'=>0,
						'mensaje_correcto'=>'',
						'mensaje_incorrecto'=>'',
						'mensaje_semicorrecto'=>'');
			insert_row("preguntas",$data);
		}

	}
	ok("problemas - preguntas");




	//carpetas problemas
	$result=consultar("select * from asignaciones");
	while ($row=mysql_fetch_array($result))
	{
		if ($row['alias']=='N') $es_alias=0; else $es_alias=1;

		$data=array('carpeta_id'=>$row['idcarpeta'],
					'problema_id'=>$row['idproblema'],
					'es_alias'=>$es_alias);
		insert_row("carpetas_problemas",$data);
	}
	ok("carpetas_problemas (asignaciones)");


	//agrupaciones
	$result=consultar("select * from agrupacion");
	while ($row=mysql_fetch_array($result))
	{
		$data=array('bloque_id'=>$row['idbloque'],
					'problema_id'=>$row['idproblema'],
					'limite_minutos'=>$row['tiempo_limite'],
					'visible'=>1,
					'notificado'=>1);
					//created);
		insert_row("agrupaciones",$data);
	}
	ok("agrupaciones");




	//añadir más administradores
	$administradores=array('jose a gonzalez','miguel ros');
	foreach ($administradores as $login)
	{
		$result=consultar_destino("select id from usuarios where login='$login'");
		$row=mysql_fetch_array($result);
		insert_row("administradores",array('id'=>$row['id']));
	}



	//ejecuciones
	$ejecuciones=array();
	$result=consultar("select * from prueba");
	while ($row=mysql_fetch_array($result))
	{
		$asignatura_id=$asignaturas[$row['idasig']];
		$problema_id=$row['idproblema'];

		$result2=consultar_destino("select a.id
									from agrupaciones a, bloques b
									where a.bloque_id=b.id
									and a.problema_id=$problema_id
									and b.asignatura_id=$asignatura_id");
		$row2=mysql_fetch_array($result2);
		if (!$row)
		{
			die("Error buscando agrupacion $asignatura_id $problema_id");
		}
		else $agrupacion_id=$row2['id'];

		if (isset($alumnos[strtolower(trim($row['idalumno'])) . '___' . $row['idasig']]))
		{
			$ejecuciones[$row['identificador']]=1;
			$data=array('id'=>$row['identificador'],
						'alumno_id'=>$alumnos[strtolower(trim($row['idalumno'])) . '___' . $row['idasig']],
						'agrupacion_id'=>$agrupacion_id,
						'created'=>convertir_fecha($row['fecha']),
						'fecha_limite'=>convertir_fecha($row['fecha']),
						'nota'=>$row['calificacion'],
						'semilla'=>0);
						//created);
			insert_row("ejecuciones",$data);
			progreso("ejec {$row['identificador']}");

		}
	}
	ok("ejecuciones");

	//respuestas ejecuciones
	$result=consultar("select * from soluciones");
	while ($row=mysql_fetch_array($result))
	{
		$ejecucion_id=$row['idprueba'];
		progreso("respuestas_ejec $ejecucion_id");

		if (isset($ejecuciones[$ejecucion_id]))
		{
			$numero_pregunta=$row['idpregunta'] - 1;
			$valor='0';
			$intento=1;
			if ($row['calificacion']=='Correcto')
			{
				$resultado=1;
			}
			else
			{
				$matches=array();
				if (preg_match('/^([\d\.]+) Puntos$/',$row['calificacion'],$matches))
				{
					if ($matches[1]!=0) $resultado=1;
					else $resultado=0;
				}
				else $resultado=0;
			}
			$data=array('ejecucion_id'=>$ejecucion_id,
						'numero_pregunta'=>$numero_pregunta,
						'valor'=>$valor,
						'intento'=>$intento,
						'resultado'=>$resultado);
			insert_row("respuestas_ejecuciones",$data);
		}
	}
	ok("respuestas_ejecuciones");



	//restricciones bloques
	$result=consultar("select * from restricciones_grupos");
	while ($row=mysql_fetch_array($result))
	{
		if ($row['activoinicio']=='S') $fecha_inicio=convertir_fecha($row['fechainicio']);
		else $fecha_inicio=null;

		if ($row['activofin']=='S') $fecha_fin=convertir_fecha($row['fechafin']);
		else $fecha_fin=null;

		if ($row['idgrupo']) $grupo=$row['idgrupo'];
		else $grupo=null;

		$data=array('bloque_id'=>$row['idbloque'],
					'grupo'=>$grupo,
					'fecha_inicio'=>$fecha_inicio,
					'fecha_fin'=>$fecha_fin);
		insert_row("restricciones_bloques",$data);
	}

	$result=consultar_destino("select id from bloques where not exists ( select * from restricciones_bloques where bloque_id=bloques.id)");
	while ($row=mysql_fetch_array($result))
	{
		$data=array('bloque_id'=>$row['id'],
					'grupo'=>null,
					'fecha_inicio'=>null,
					'fecha_fin'=>null);
		insert_row("restricciones_bloques",$data);
	}

	ok("restricciones_bloques");


	//modificar problemas, quitar library(est)
	$result=consultar_destino("select id,expresiones from problemas where expresiones like '%library(est)%'");
	while ($row=mysql_fetch_array($result))
	{
		$expresiones=str_replace("library(est)","",$row['expresiones']);
		$expresiones=mysql_escape_string($expresiones);
		$id=$row['id'];

		ejecutar("update problemas set expresiones='$expresiones' where id=$id");
	}
	ok("convertir problemas");



	////////////////////////////////////////////



	function vaciar($tabla)
	{
		$query="delete from $tabla";
		return ejecutar($query);
	}

	function consultar($query)
	{
		$source=mysql_connect("localhost","root","");
		mysql_select_db("estatus_import",$source) or die("No puedo conectar");

		$result=mysql_query($query,$source);
		if (! $result)
		{
			die("ERROR :$query <br/>" . mysql_error());
		}
		return $result;
	}

	function consultar_destino($query)
	{
		$dest=mysql_connect("localhost","root","");
		mysql_select_db("estatus",$dest) or die("No puedo conectar");

		$result=mysql_query($query,$dest);
		if (! $result)
		{
			die("ERROR-DESTINO :$query <br/>" . mysql_error());
		}
		return $result;
	}


	function ejecutar($query,$verificar=true)
	{
		$dest=mysql_connect("localhost","root","");
		mysql_select_db("estatus",$dest) or die("No puedo conectar");

		$result=mysql_query($query,$dest);
		if (! $result && $verificar)
		{
			die("ERROR :$query <br/>" . mysql_error());
		}
		return $result;
	}

	function insert_row($tabla,$data,$fallar=true)
	{
		$query="insert into $tabla (";
		$name_fields=array_keys($data);

		for($i=0;$i<count($name_fields);$i++)
		{
			$query.=$name_fields[$i];
			if ($i<count($name_fields) - 1) $query.=',';
		}
		$query.=') values (';

		for($i=0;$i<count($name_fields);$i++)
		{
			if ($data[$name_fields[$i]]!==null)
			{
				$valor=mysql_escape_string($data[ $name_fields[$i]  ]);
				$query.="'$valor'";
			}
			else
			{
				$query.="NULL";
			}
			if ($i<count($name_fields) - 1) $query.=',';
		}
		$query.=')';
		if ($fallar)
		{
			ejecutar($query,true);
			return mysql_insert_id();
		}
		else
		{
			$result=ejecutar($query,false);
			if (!$result) return -1;
			else return mysql_insert_id();
		}
	}

	function ok($tabla)
	{
		echo "ok: $tabla<br/>";
		flush();
		progreso("ok $tabla");
	}

	function convertir_fecha($fecha)
	{
		if ($fecha)
		{
			return strftime("%Y/%m/%d %H:%M",strtotime($fecha));
		}
		else
		{
			return null;
		}
	}

	function progreso($str)
	{
		$fp=fopen("/tmp/.convertir2.tmp","a");
		fwrite($fp,$str . "\n");
		fclose($fp);
	}


?>