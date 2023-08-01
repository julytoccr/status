<html>
<head>
</head>
<body>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="margin-bottom: 50px">
		<input type="text" name="archivo" placeholder="Nombre de archivo">
		<input type="password" name="password" placeholder="Password">
		<input type="submit" value="Enviar">
	</form>
<?php
if (isset($_POST) && !empty($_POST)) {
	if (isset($_POST['password']) && isset($_POST['archivo'])) {
		if ($_POST['password'] == "EIORules") {
			include("graficos.php");
			$rserve = new EstatusRserve();
			$rserve->evaluar_instrucciones("source(\"/home/estatusadmin/aplicaciones/codigo/PFC/app/webroot/test-r/" . $_POST['archivo'] . "\")");
			
			echo "<h3>RESULTADOS:</h3><br />\n";
			echo $rserve->obtener_HTML_variable2("resultado");
		}
	}
}
?>
</body>
</html>