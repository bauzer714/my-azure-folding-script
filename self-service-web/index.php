<?php
$response = (object)array(
   "uptime" => shell_exec('who -b'),
   'content' => file_get_contents('/mnt/log.txt'),
);
header('Content-Type: application/json');
echo json_encode($response);
?>