<?php
/* COPYRIGHT GOES HERE */
$main_page_tooltip_text = "Main Page - REMOVE THE NEED FOR THIS PAGE";
$scan_times_tooltip_text = "Setup and/or Change the times that the NMap scans will be ran at";
$email_setup_tooltip_text = "Setup the email location where the NMap scan results will be sent to";
$auto_scans_tooltip_text = "Start, Stop, or Reset the automatic NMap scans";
$nmap_scan_tooltip_text = "Immediately run an NMap scan and view its results";
$scan_results_tooltip_text = "View the NMap scan results for each host";

function nmap_plugin_sync_package() {
	/* ADD ABILITY TO PULL DATA FROM config.xml TO CORRECT FILES IF NEEDED */
	require_once("globals.inc");
	require_once("functions.inc");
	require_once("config.inc");
	require_once("util.inc");

	//creates the tag that will hold the encoded NMap scan results
	if(!isset($config['nmapdata'])) {
		$config['nmapdata'] = true;
		write_config();
	}
}
function nmap_plugin_install_command() {
	global $config;
	conf_mount_rw();	/* mount flash card read/write */
	config_lock();

	if (!is_dir('/usr/local/pkg/nmap-plugin/')) {
		mkdir("/usr/local/pkg/nmap-plugin/", 0755, true);
	}
	if (!is_dir('/usr/local/www/nmap-plugin/')) {
		mkdir("/usr/local/www/nmap-plugin/", 0755, true);
	}
	if (!is_dir('/usr/local/pkg/nmap-plugin/hosts/')) {
		mkdir("/usr/local/pkg/nmap-plugin/hosts/", 0755, true);
	}

	if (!file_exists("/usr/local/pkg/nmap-plugin/run-time-minutes.txt")) {
		file_put_contents("/usr/local/pkg/nmap-plugin/run-time-minutes.txt", "*/15 ");
	}
	if (!file_exists("/usr/local/pkg/nmap-plugin/run-time-hours.txt")) {
		file_put_contents("/usr/local/pkg/nmap-plugin/run-time-hours.txt", "* ");
	}
	if (!file_exists("/usr/local/pkg/nmap-plugin/run-time-days.txt")) {
		file_put_contents("/usr/local/pkg/nmap-plugin/run-time-days.txt", "* ");
	}
	if (!file_exists("/usr/local/pkg/nmap-plugin/run-time-months.txt")) {
		file_put_contents("/usr/local/pkg/nmap-plugin/run-time-months.txt", "* ");
	}
	if (!file_exists("/usr/local/pkg/nmap-plugin/run-time-weekdays.txt")) {
		file_put_contents("/usr/local/pkg/nmap-plugin/run-time-weekdays.txt", "* ");
	}
/*
	//$crontabjobsfile = file_get_contents("/tmp/crontab-jobs.txt");
	//if (!in_array("@reboot /usr/local/pkg/nmap-plugin/nmap-plugin-encode-data.php", "$crontabjobsfile")) {
		//$crontabjobsfileopen = fopen("/tmp/crontab-jobs.txt","a");
		//fwrite("$crontabjobsfileopen","@reboot /usr/local/pkg/nmap-plugin/nmap-plugin-encode-data.php");
		//fclose("$crontabjobsfileopen");
	//}
*/
	nmap_plugin_sync_package();
	conf_mount_ro();
	config_unlock();
}
function nmap_plugin_deinstall_command() {
	conf_mount_rw(); //read and write permissions for config file
	//config_lock(); //lock the config so other scripts are unable to alter it
	//$list_of_hosts = @preg_grep('/^([^.])/', scandir('/usr/local/pkg/nmap-plugin/hosts/', 1));
	//if ("" != trim($list_of_hosts)) {
	if (count(glob("/usr/local/pkg/nmap-plugin/hosts/*"))==0) {
		rmdir("/usr/local/pkg/nmap-plugin/hosts/");
	} else {
		$list_of_hosts = @preg_preg('/^([^.])/', scandir('/usr/local/pkg/nmap-plugin/hosts', 1));
		foreach ($list_of_hosts as $macaddress) {
			unlink_if_exists("/usr/local/pkg/nmap-plugin/hosts/$macaddress/Host-Check.xml");
			unlink_if_exists("/usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original.txt");
			unlink_if_exists("/usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original_grepable.txt");
			unlink_if_exists("/usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Results_grepable.txt");
			unlink_if_exists("/usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Results_grepable.tmp");

			unlink_if_exists("/tmp/Host-Check.xml");
			unlink_if_exists("/tmp/Host-Check-$macaddress.xml");
			unlink_if_exists("/tmp/list-of-up-hosts.txt");
			unlink_if_exists("/tmp/NMap-Scan-Results-$macaddress.txt");
			unlink_if_exists("/tmp/diff-$macaddress.txt");
			unlink_if_exists("/tmp/email-results-$macaddress.txt");
			rmdir("/usr/local/pkg/nmap-plugin/hosts/$macaddress/");
		}
	}
	@system("/usr/local/pkg/nmap-plugin/auto-nmap-scans.php -r reset");
/*
	unlink_if_exists("/usr/local/www/nmap-plugin/nmap-plugin-schedule-scan-times.php");
	unlink_if_exists("/usr/local/www/nmap-plugin/nmap-plugin-email-setup.php");
	unlink_if_exists("/usr/local/www/nmap-plugin/nmap-plugin-auto-scans.php");
	unlink_if_exists("/usr/local/www/nmap-plugin/nmap-plugin-nmap-scan.php");
	unlink_if_exists("/usr/local/www/nmap-plugin/nmap-plugin-scan-results.php");
	unlink_if_exists("/usr/local/www/nmap-plugin/nmap-plugin-view-results.php");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/nmap-scan-dhcp.sh");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/nmap-scan-arp.sh");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/interface-information.php");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/nmap-plugin-encode-data.php");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/nmap-plugin-decode-data.php");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/auto-nmap-scans.php");
*/
	unlink_if_exists("/usr/local/pkg/nmap-plugin/run-time-minutes.txt");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/run-time-hours.txt");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/run-time-days.txt");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/run-time-months.txt");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/run-time-weekdays.txt");

	unlink_if_exists("/var/run/nmapscan-dhcp.pid");
	unlink_if_exists("/var/run/nmapscan-arp.pid");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/arp-checked.txt");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/manual-nmap-scan.sh");
	unlink_if_exists("/tmp/manual-nmap-scan-results.txt");

	unlink_if_exists("/usr/local/pkg/nmap-plugin/nmap-plugin-host-setup.xml");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/nmap-plugin-change-scan-times.xml");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/nmap-plugin-schedule-scan-times.xml");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/nmap-plugin-auto-scans.xml");
	unlink_if_exists("/usr/local/www/nmap-plugin/hosts.txt");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/run-time.txt");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/results/Original-Nmap-Results.txt");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/results/New-NMap-Results.txt");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/results/NMap-Diff-Results.txt");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/create-time-files.sh");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/crontab-start-scan-dhcp.sh");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/crontab-stop-scan.sh");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/crontab-start-scan-arp.sh");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/manual-nmap-scan.sh");
	unlink_if_exists("/tmp/crontab-jobs.txt");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/shell-script.sh");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/arp-checked.txt");
	unlink_if_exists("/tmp/Host-Check.xml");
	unlink_if_exists("/tmp/crontabtime.txt");
	unlink_if_exists("/tmp/list-of-up-hosts.txt");
	unlink_if_exists("/usr/local/pkg/nmap-plugin/update-config.php");
	unlink_if_exists("/tmp/manual-nmap-scan-results.txt");
	unlink_if_exists("/var/run/nmapscan-dhcp.pid");
	unlink_if_exists("/var/run/nmapscan-arp.pid");
/*
	unlink_if_exists("/usr/local/pkg/nmap-plugin.xml");
	unlink_if_exists("/usr/local/pkg/nmap-plugin.inc");
*/

	if (is_dir("/usr/local/pkg/nmap-plugin/hosts/")) {
		@rmdir("/usr/local/pkg/nmap-plugin/hosts/");
	}
	if (is_dir("/usr/local/www/nmap-plugin/")) {
		@rmdir("/usr/local/www/nmap-plugin/");
	}
	if (is_dir("/usr/local/pkg/nmap-plugin/")) {
		@rmdir("/usr/local/pkg/nmap-plugin/");
	}

	conf_mount_ro(); //read only permissions for config file
	//config_unlock(); //unlock config so other scripts can alter it
}

/*
$crontab_time = '@reboot /usr/local/pkg/nmap-plugin/nmap-plugin-decode-data.php';
*/
?>
