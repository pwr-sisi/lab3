<!DOCTYPE html>
<html>
<body>
<h2>JSON data</h2>
<h2>Raw content</h2>
<?php 
print_r(file_get_contents('php://input'));
?>

<h2>Parsed content</h2>
<?php
$result = json_decode(file_get_contents('php://input'), true);
if($result == NULL)
  echo "Badly formatted JSON!\n";
else
  print_r($result);
?>
</body>
</html>