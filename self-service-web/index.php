<?php

function doRebootIfNeeded() : bool {
   $r = filter_var($_GET['reboot'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
   if ($r) {
      shell_exec('sudo shutdown -r 1');
      return true;
   }
   return false;
}

function isLogTypeArchive() : bool {
   $type = strtolower(trim($_GET['logtype'] ?? ''));
   return $type === 'archive';
}

function getLogContent() : string|bool {
   $logfilecontents = file_get_contents('/mnt/log.txt');
   if ($logfilecontents === false || !isLogTypeArchive()) {
      return $logfilecontents;
   }
	$archivelogfilename = "log.txt";
	$zip = new ZipArchive();
	$zipfilename = 'log.zip';
	$zipfilelocation = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $zipfilename;
	if (file_exists($zipfilelocation)) {
		unlink($zipfilelocation);
	}
	if ($zip->open($zipfilelocation, ZipArchive::CREATE) !== TRUE) {
		return 'Failed to create zip archive';
	}
	$zip->addFromString($archivelogfilename, $logfilecontents);
	$zip->close();
	return base64_encode(file_get_contents($zipfilelocation));
}

$response = (object)array(
   'uptime' => shell_exec('who -b'),
   'reboot' => doRebootIfNeeded(),
   'content' => getLogContent(),
   'isContentArchived' => isLogTypeArchive(),
);
header('Content-Type: application/json');
echo json_encode($response);
?>