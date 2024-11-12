<?php

function doRebootIfNeeded() : bool {
   $r = filter_var($_GET['reboot'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
   if ($r) {
      shell_exec('sudo shutdown -r 1');
      return true;
   }
   return false;
}

function getParamLogType() : string {
   return strtolower(trim($_GET['logtype'] ?? 'unset'));
}

function isLogTypeArchive() : bool {
   return getParamLogType() === 'archive';
}

function isLogTypeLastLine() : bool {
   return getParamLogType() === 'lastline';
}

function isLogTypeNone() : bool {
   return getParamLogType() === 'none';
}

function getLogContent() : string|bool {
   if (isLogTypeNone()) {
	   return '';
   }
   $logfile = '/mnt/log.txt';
   if (!file_exists($logfile)) {
       return false;
   }
   if (isLogTypeLastLine()) {
	return shell_exec("tail -1 $logfile");
   }
	
   $logfilecontents = file_get_contents($logfile);
   if (!isLogTypeArchive()) {
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

function getNodeState() : object|bool {
  try {
       $lufahStateString = shell_exec('lufah -a "." state');
       return json_decode($lufahStateString, flags: JSON_THROW_ON_ERROR);
  } catch (Exception $ex) {
       return false;
   }
}

$response = (object)array(
   'uptime' => shell_exec('who -b'),
   'reboot' => doRebootIfNeeded(),
   'nodeState' => getNodeState(),   
   'logType' => getParamLogType(),
   'content' => getLogContent(),
);
header('Content-Type: application/json');
echo json_encode($response);
?>
