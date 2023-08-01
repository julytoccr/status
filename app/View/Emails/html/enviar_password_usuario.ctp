<div align="center">

<h2><b>Welcome to e-status!</b></h2><br /><br />
<?php
if (isset($asignatura)) {
echo '<div style="font-size: 12pt">You have been registered in the subject: <u>' . $asignatura['nombre'] . "</u></div><br /><br />";  echo "In order to access the problems enabled to it, ";
} else {
echo "In order to access the system, ";
}
?>
please log in with the following key:<br />
<div style="font-size: 12pt">Your login is: <u><?php echo $usuario['login'] ?></u><br />
Your password is: <u><?php echo $usuario['password'] ?></u></div><br /><br />

Please, contact your teacher if you have any trouble with login.<br />

</div>
