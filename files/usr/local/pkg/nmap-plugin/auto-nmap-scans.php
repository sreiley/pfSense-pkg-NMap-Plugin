#!/usr/local/bin/php
<?php
/* CONSIDER CHANGING THE $user TO BE SET FROM THE WEBGUI */
require_once("globals.inc");
require_once("functions.inc");
require_once("config.inc");
require_once("util.inc");
require_once("services.inc");

$options = getopt("a:d:i:s:r:");
/*
$cron_time = file_get_contents("/usr/local/pkg/nmap-plugin/run-time-minutes.txt");
$cron_time .= file_get_contents("/usr/local/pkg/nmap-plugin/run-time-hours.txt");
$cron_time .= file_get_contents("/usr/local/pkg/nmap-plugin/run-time-days.txt");
$cron_time .= file_get_contents("/usr/local/pkg/nmap-plugin/run-time-months.txt");
$cron_time .= file_get_contents("/usr/local/pkg/nmap-plugin/run-time-weekdays.txt");
*/

$nmap_plugin_cron = &$config['cron']['item'];
$second_array = array('/usr/local/pkg/nmap-plugin/nmap-scan-dhcp.sh', '/usr/local/pkg/nmap-plugin/nmap-scan-arp.sh');

if ($options[a] == 'start') {
	//start NMap ARP scans
	foreach ($nmap_plugin_cron as $key => $value) {
		if ($value['command'] == '/usr/local/pkg/nmap-plugin/nmap-scan-arp.sh' ||
		    strpos($value['command'],'/usr/local/pkg/nmap-plugin/nmap-scan-arp.sh') !== false) {
			//if the NMap scan is already set...
			$host = shell_exec("/usr/bin/whoami | /usr/bin/tr -d \\n");
			$user = explode("\n", $host);
			$nmap_scan_arp = array();
			$nmap_scan_arp['minute'] = file_get_contents("/usr/local/pkg/nmap-plugin/run-time-minutes.txt");
			$nmap_scan_arp['hour'] = file_get_contents("/usr/local/pkg/nmap-plugin/run-time-hours.txt");
			$nmap_scan_arp['mday'] = file_get_contents("/usr/local/pkg/nmap-plugin/run-time-days.txt");
			$nmap_scan_arp['month'] = file_get_contents("/usr/local/pkg/nmap-plugin/run-time-months.txt");
			$nmap_scan_arp['wday'] = file_get_contents("/usr/local/pkg/nmap-plugin/run-time-weekdays.txt");
			$nmap_scan_arp['who'] = print_r($user[0], TRUE);
			if (!isset($options[i])) {
				$nmap_scan_arp['command'] = '/usr/local/pkg/nmap-plugin/nmap-scan-arp.sh';
			} elseif (isset($options[i])) {
				$interface = $options[i];
				$nmap_scan_arp['command'] = '/usr/local/pkg/nmap-plugin/nmap-scan-arp.sh -i '.$interface.'';
			}

			$nmap_plugin_cron[$key]['minute'] = $nmap_scan_arp['minute'];
			$nmap_plugin_cron[$key]['hour'] = $nmap_scan_arp['hour'];
			$nmap_plugin_cron[$key]['mday'] = $nmap_scan_arp['mday'];
			$nmap_plugin_cron[$key]['month'] = $nmap_scan_arp['month'];
			$nmap_plugin_cron[$key]['wday'] = $nmap_scan_arp['wday'];
			$nmap_plugin_cron[$key]['who'] = $nmap_scan_arp['who'];
			$nmap_plugin_cron[$key]['command'] = $nmap_scan_arp['command'];
			write_config();
			configure_cron(); //sync crontab
			system("/usr/local/pkg/nmap-plugin/auto-nmap-scans.sh -a");
		} elseif (!in_array('/usr/local/pkg/nmap-plugin/nmap-scan-arp.sh', $value) ||
		          strpos($value,'/usr/local/pkg/nmap-plugin/nmap-scan-dhcp.sh') == false) {
			//if the NMap scan cannot be found within $config...
			$host = shell_exec("/usr/bin/whoami | /usr/bin/tr -d \\n");
			$user = explode("\n", $host);
			$nmap_scan_arp = array();
			$nmap_scan_arp['minute'] = file_get_contents("/usr/local/pkg/nmap-plugin/run-time-minutes.txt");
			$nmap_scan_arp['hour'] = file_get_contents("/usr/local/pkg/nmap-plugin/run-time-hours.txt");
			$nmap_scan_arp['mday'] = file_get_contents("/usr/local/pkg/nmap-plugin/run-time-days.txt");
			$nmap_scan_arp['month'] = file_get_contents("/usr/local/pkg/nmap-plugin/run-time-months.txt");
			$nmap_scan_arp['wday'] = file_get_contents("/usr/local/pkg/nmap-plugin/run-time-weekdays.txt");
			$nmap_scan_arp['who'] = print_r($user[0], TRUE);
			if (!isset($options[i])) {
				$nmap_scan_arp['command'] = '/usr/local/pkg/nmap-plugin/nmap-scan-arp.sh';
			} elseif (isset($options[i])) {
				$interface = $options[i];
				$nmap_scan_arp['command'] = '/usr/local/pkg/nmap-plugin/nmap-scan-arp.sh -i '.$interface.'';
			}
			$nmap_plugin_cron[] = $nmap_scan_arp;
			write_config();
			configure_cron(); //sync crontab
			system("/usr/local/pkg/nmap-plugin/auto-nmap-scans.sh -a");
		}
	}
}
if ($options[d] == 'start') {
	//start NMap DHCP scans
	foreach ($nmap_plugin_cron as $key => $value) {
		if ($value['command'] == '/usr/local/pkg/nmap-plugin/nmap-scan-dhcp.sh' ||
		    strpos($value['command'],'/usr/local/pkg/nmap-plugin/nmap-scan-dhcp.sh') !== false) {
			//if the NMap scan is already set...
			$host = shell_exec("/usr/bin/whoami | /usr/bin/tr -d \\n");
			$user = explode("\n", $host);
			$nmap_scan_dhcp = array();
			$nmap_scan_dhcp['minute'] = file_get_contents("/usr/local/pkg/nmap-plugin/run-time-minutes.txt");
			$nmap_scan_dhcp['hour'] = file_get_contents("/usr/local/pkg/nmap-plugin/run-time-hours.txt");
			$nmap_scan_dhcp['mday'] = file_get_contents("/usr/local/pkg/nmap-plugin/run-time-days.txt");
			$nmap_scan_dhcp['month'] = file_get_contents("/usr/local/pkg/nmap-plugin/run-time-months.txt");
			$nmap_scan_dhcp['wday'] = file_get_contents("/usr/local/pkg/nmap-plugin/run-time-weekdays.txt");
			$nmap_scan_dhcp['who'] = print_r($user[0], TRUE);
			if (!isset($options[i])) {
				$nmap_scan_dhcp['command'] = '/usr/local/pkg/nmap-plugin/nmap-scan-dhcp.sh';
			} elseif (isset($options[i])) {
				$interface = $options[i];
				$nmap_scan_dhcp['command'] = '/usr/local/pkg/nmap-plugin/nmap-scan-dhcp.sh -i '.$interface.'';
			}

			$nmap_plugin_cron[$key]['minute'] = $nmap_scan_dhcp['minute'];
			$nmap_plugin_cron[$key]['hour'] = $nmap_scan_dhcp['hour'];
			$nmap_plugin_cron[$key]['mday'] = $nmap_scan_dhcp['mday'];
			$nmap_plugin_cron[$key]['month'] = $nmap_scan_dhcp['month'];
			$nmap_plugin_cron[$key]['wday'] = $nmap_scan_dhcp['wday'];
			$nmap_plugin_cron[$key]['who'] = $nmap_scan_dhcp['who'];
			$nmap_plugin_cron[$key]['command'] = $nmap_scan_dhcp['command'];
			write_config();
			configure_cron(); //sync crontab
			system("/usr/local/pkg/nmap-plugin/auto-nmap-scans.sh -a");
		} elseif (!in_array('/usr/local/pkg/nmap-plugin/nmap-scan-dhcp.sh', $value) ||
		          strpos($value,'/usr/local/pkg/nmap-plugin/nmap-scan-dhcp.sh') == false) {
			//if the NMap scan cannot be found within $config...
			$host = shell_exec("/usr/bin/whoami | /usr/bin/tr -d \\n");
			$user = explode("\n", $host);
			$nmap_scan_dhcp = array();
			$nmap_scan_dhcp['minute'] = file_get_contents("/usr/local/pkg/nmap-plugin/run-time-minutes.txt");
			$nmap_scan_dhcp['hour'] = file_get_contents("/usr/local/pkg/nmap-plugin/run-time-hours.txt");
			$nmap_scan_dhcp['mday'] = file_get_contents("/usr/local/pkg/nmap-plugin/run-time-days.txt");
			$nmap_scan_dhcp['month'] = file_get_contents("/usr/local/pkg/nmap-plugin/run-time-months.txt");
			$nmap_scan_dhcp['wday'] = file_get_contents("/usr/local/pkg/nmap-plugin/run-time-weekdays.txt");
			$nmap_scan_dhcp['who'] = print_r($user[0], TRUE);
			if (!isset($options[i])) {
				$nmap_scan_dhcp['command'] = '/usr/local/pkg/nmap-plugin/nmap-scan-dhcp.sh';
			} elseif (isset($options[i])) {
				$interface = $options[i];
				$nmap_scan_dhcp['command'] = '/usr/local/pkg/nmap-plugin/nmap-scan-dhcp.sh -i '.$interface.'';
			}
			$nmap_plugin_cron[] = $nmap_scan_dhcp;
			write_config();
			configure_cron(); //sync crontab
			system("/usr/local/pkg/nmap-plugin/auto-nmap-scans.sh -a");
		}
	}
}
if ($options[s] == 'stop') {
	//stop all NMap scans
	foreach ($nmap_plugin_cron as $key = $value) {
		if (in_array($value['command'], $second_array)) {
			unset($nmap_plugin_cron[$key]);
			write_config();
			configure_cron(); //sync crontab
			system("/usr/local/pkg/nmap-plugin/auto-nmap-scans.sh -b");
		}
	}
}
if ($options[r] == 'reset') {
	//reset all NMap scan results
	foreach ($config['nmapdata'] as $key => $value) {
		unset($config['nmapdata'][$key]);
	}
	$list_of_hosts = preg_grep('/^([^.])/', scandir('/usr/local/pkg/nmap-plugin/hosts/', 1));
	foreach ($list_of_hosts as $host) {
		unlink_if_exists("/usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original_grepable.txt");
		unlink_if_exists("/usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original.txt");
		unlink_if_exists("/usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Results_grepable.txt");
		unlink_if_exists("/usr/local/pkg/nmap-plugin/hosts/$macaddress/Host-Check.xml");
		rmdir("/usr/local/pkg/nmap-plugin/hosts/$macaddress/");
	}
}
?>
