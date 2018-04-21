<!DOCTYPE html>
<html>
<body>
<h2>Dane</h2>
<?php echo "Dane POST: ".$_POST["imie"]." ".$_POST["nazwisko"]; ?>

<h2>Tablica POST</h2>
<?php var_dump($_POST); ?>
<h2>Tablica GET</h2>
<?php var_dump($_GET); ?>
</body>
</html>