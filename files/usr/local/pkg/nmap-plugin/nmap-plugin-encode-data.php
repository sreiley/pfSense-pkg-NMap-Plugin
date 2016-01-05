#!/usr/local/bin/php
PATH=/bin:/usr/bin:/usr/local/bin:/sbin:/usr/sbin:/usr/local/sbin
<?php
require_once("globals.inc");
require_once("functions.inc");
require_once("config.inc");
require_once("util.inc");

if (!isset($config['nmapdata'])) {
	$config['nmapdata'];
	write_config();
}

$list_of_hosts = preg_grep('/^([^.])/', scandir('/usr/local/pkg/nmap-plugin/hosts', 1));
foreach ($list_of_hosts as $host) {
	$macaddress = str_replace(":","-","$host");
	if (!file_exists("/usr/local/pkg/nmap-plugin/hosts/$host/NMap-Scan-Original_grepable.txt")) {
		continue;
	} elseif (file_exists("/usr/local/pkg/nmap-plugin/hosts/$host/NMap-Scan-Original_grepable.txt")) {
		$NmapScanData = file_get_contents("/usr/local/pkg/nmap-plugin/hosts/$host/NMap-Scan-Original_grepable.txt");
		$encoded_data = base64_encode(gzdeflate($NmapScanData));
		if (!isset($config['nmapdata']["nmapscandata_$macaddress"])) {
			$config['nmapdata']["nmapscandata_$macaddress"] = $encoded_data;
		} elseif (isset($config['nmapdata']["nmapscandata_$macaddress"])) {
			$config['nmapdata']["nmapscandata_$macaddress"] = $encoded_data;
		}
		write_config();
	}
}
?>
