#!/usr/local/bin/php
PATH=/bin:/usr/bin:/usr/local/bin:/sbin:/usr/sbin:/usr/local/sbin
<?php
require_once("globals.inc");
require_once("functions.inc");
require_once("config.inc");
require_once("util.inc");

$array = print_r($config['nmapdata']);
if (!$array) {
	exit;
} elseif ($array) {
	foreach ($config['nmapdata'] as $key => $value) {
		$macaddress = str_replace("-",":", substr($key, 13));
		$decoded_data = gzinflate(base64_decode($config['nmapdata']["nmapscandata_$key"]));
		if (!is_dir("/usr/local/pkg/nmap-plugin/hosts/$macaddress")) {
			mkdir("/usr/local/pkg/nmap-plugin/hosts/$macaddress", 0755);
		}
		file_put_contents("/usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original_grepable.txt", $decoded_data);
	}
}
?>
