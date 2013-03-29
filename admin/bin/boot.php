<?php
define('ROOT_GROUP',dirname(__DIR__));
require_once(dirname(dirname(__DIR__)).'/vendor/autoload.php');
require_once('lss_boot.php');
__boot();

use \LSS\Config;

function getLock($level=0,$timeout=30){
	$lockfile = Config::get('admin','lockfile');
	$wait = true;
	$waited = 0;
	if($level === 0){
		if(!is_writable(dirname($lockfile)) || file_exists($lockfile) && !is_writable($lockfile)){
			dolog('Cannot write to lockfile: '.$lockfile,LOG_ERROR);
			exit;
		}
		file_put_contents($lockfile,0);
		$wait = false;
	}
	while($wait){
		$wait = false;
		if(($level > 0) && (!file_exists($lockfile)))
			$wait = true;
		if(file_exists($lockfile)){
			if(!is_readable($lockfile)){
				dolog('Cannot read from lockfile: '.$lockfile,LOG_ERROR);
				exit;
			}
			if($current_level = file_get_contents($lockfile)){
				$current_level += 0; //force int
			}
			if($current_level != $level)
				$wait = true;
		}
		if($wait){
			if($waited > $timeout){
				dolog('Could not obtain lock in '.$timeout.' seconds',LOG_ERROR);
				exit;
			}
			dolog('Waiting for lock level '.$level);
			sleep(1);
			$waited++;
		}
	}
	dolog('Obtained lock level '.$level);
}

function stepLock(){
	$lockfile = Config::get('admin','lockfile');
	if(!is_readable($lockfile)){
		dolog('Cannot read from lockfile: '.$lockfile,LOG_ERROR);
		exit;
	}
	$current_level = file_get_contents($lockfile);
	if(is_numeric($current_level)){
		if(!is_writable(dirname($lockfile)) || file_exists($lockfile) && !is_writable($lockfile)){
			dolog('Cannot write to lockfile: '.$lockfile,LOG_ERROR);
			exit;
		}
		if(file_put_contents($lockfile,sprintf("%d",$current_level+1))){
			dolog('Step lock level from '.($current_level+0).' to '.($current_level+1));
			return;
		}
	}
	dolog('Step lock failed');
}

function clearLock(){
	$lockfile = Config::get('admin','lockfile');
	while(file_exists($lockfile)) unlink($lockfile);
	dolog('Released lockfile');
}
