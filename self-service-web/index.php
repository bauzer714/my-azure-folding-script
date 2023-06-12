<?php

function doRebootIfNeeded() : bool {
   $r = filter_var($_GET['reboot'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
   if ($r) {
      shell_exec('sudo shutdown -r 1');
      return true;
   }
   return false;
}

$response = (object)array(
   'uptime' => shell_exec('who -b'),
   'reboot' => doRebootIfNeeded(),
   'content' => file_get_contents('/mnt/log.txt'),
);
header('Content-Type: application/json');
echo json_encode($response);
?>