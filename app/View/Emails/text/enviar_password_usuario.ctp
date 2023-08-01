Welcome to e-status!
<?php
if (isset($asignatura)) {
echo 'You have been registered in the subject:' . $asignatura['nombre'] .'In order to access the problems enabled to it,';
} else {
echo 'In order to access the system, ';
}
?>
please log in with the following key:
Your login is: <?php echo $usuario['login'] ?>
Your password is: <?php echo $usuario['password'] ?>
Please, contact your teacher if you have any trouble with login.
