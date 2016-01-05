<?php
/* COPYRIGHT GOES HERE */
require("guiconfig.inc");
require_once("notices.inc");
require_once("/usr/local/pkg/nmap-plugin.inc");

$pgtitle = array(gettext("Services"),gettext("NMap-Plugin Automatic Scans"));
include("head.inc");

$crontabs = exec('crontab -l', $output, $return);
$arp_check = $_POST['Start-Arp-Checks'];
$dhcp_check = $_POST['Start-Dhcp-Checks'];
$interface = $_POST['Selected-Interface'];
/*
if ($_POST['Submit'] == gettext("Start NMap Scans")) {
	//Start DHCP scan...
	$savemsg = "Starting the DHCP NMap scans\n";
	if (!isset($_POST['Selected-Interface'])){
		system("/usr/local/pkg/nmap-plugin/auto-nmap-scans.php -d start");
	} elseif (isset($_POST['Selected-Interface'])) {
		system("/usr/local/pkg/nmap-plugin/auto-nmap-scans.php -d start -i $interface");
	}
*/
if ($_POST['Submit'] == gettext("Stop NMap Scans")) {
//} elseif ($_POST['Submit'] == gettext("Stop NMap Scans")) {
	//Stopping NMap scans...
	$savemsg .= "Stopping the NMap scans\n";
	$savemsg .= "A reboot is necessary to update the list of cron jobs\n";
	unlink_if_exists("/usr/local/pkg/nmap-plugin/arp-checked.txt");
	unset($_POST['Start-Arp-Checks']);
	system("/usr/local/pkg/nmap-plugin/auto-nmap-scans.php -s stop");
} elseif ($_POST['Submit'] == gettext("Reset NMap Scan Results")) {
	$savemsg .= "Reseting the NMap scan results\n";
	$savemsg .= "A reboot is necessary to update the list of cron jobs\n";
	system("/usr/local/pkg/nmap-plugin/auto-nmap-scans.php -r reset");
}
if (isset($_POST['Start-Arp-Checks']) || file_exists("/usr/local/pkg/nmap-plugin/arp-checked.txt")) {
	$arp_checkbox = '<input type="checkbox" id="Start-Arp-Checks" name="Start-Arp-Checks" checked="checked">Enable Arp Host Checks<br />';
	touch("/usr/local/pkg/nmap-plugin/arp-checked.txt");
} elseif (!isset($_POST['Start-Arp-Checks']) || !file_exists("/usr/local/pkg/nmap-plugin/arp-checked.txt")) {
	$arp_checkbox = '<input type="checkbox" id="Start-Arp-Checks" name="Start-Arp-Checks">Enable Arp Host Checks<br />';
	unlink_if_exists("/usr/local/pkg/nmap-plugin/arp-checked.txt");
}
if (isset($_POST['Start-Dhcp-Checks']) || file_exists("/usr/local/pkg/nmap-plugin/dhcp-checked.txt")) {
	$dhcp_checkbox = '<input type="checkbox" id="Start-Dhcp-Checks" name="Start-Dhcp-Checks" checked="checked">Enable Dhcp Host Checks<br />';
	touch("/usr/local/pkg/nmap-plugin/dhcp-checked.txt");
} elseif (!isset($_POST['Start-Dhcp-Checks']) || !file_exists("/usr/local/pkg/nmap-plugin/dhcp-checked.txt")) {
	$dhcp_checkbox = '<input type="checkbox" id="Start-Dhcp-Checks" name="Start-Dhcp-Checks">Enable Dhcp Host Checks<br />';
	unlink_if_exists("/usr/local/pkg/nmap-plugin/dhcp-checked.txt");
}
if (file_exists("/usr/local/pkg/nmap-plugin/dhcp-checked.txt")) {
	//Starting DHCP NMap scans...
	if (!isset($_POST['Selected-Interface'])) {
		$savemsg .= "Staring DHCP NMap scans\n";
		$savemsg .= "A reboot is necessary to update the list of cron jobs\n";
		system("/usr/local/pkg/nmap-plugin/auto-nmap-scans.php -d start");
	} elseif (isset($_POST['Selected-Interface'])) {
		$savemsg .= "Starting DHCP NMap scans\n";
		$savemsg .= "A reboot is necessary to update the list of cron jobs\n";
		system("/usr/local/pkg/nmap-plugin/auto-nmap-scans.php -d start -i $interface");
	}
}
if (file_exists("/usr/local/pkg/nmap-plugin/arp-checked.txt")) {
	//Starting ARP NMap scans...
	if (!isset($_POST['Selected-Interface'])) {
		$savemsg .= "Starting the ARP NMap scans\n";
		$savemsg .= "A reboot is necessary to update the list of cron jobs\n";
		system("/usr/local/pkg/nmap-plugin/auto-nmap-scans.php -a start");
	} elseif (isset($_POST['Selected-Interface'])) {
		$savemsg .= "Starting the ARP NMap scans\n";
		$savemsg .= "A reboot is necessary to update the list of cron jobs\n";
		system("/usr/local/pkg/nmap-plugin/auto-nmap-scans.php -a start -i $interface");
	}
}
?>
	<head>
		<style>
			pre {
				display: block;
				font-family: monospace;
				white-space: pre-wrap;
				margin: 1em 0;
			}
		</style>
	</head>
	<body link="#0000CC" vlink="#0000CC" alink="#0000CC">
		<?php include("fbegin.inc"); ?>
		<form action="nmap-plugin-auto-scans.php" method="post">
			<?php
				if ($input_errors) {
					print_input_errors($input_errors);
				}
				if ($savemsg) {
					print_info_box($savemsg);
				}
			?>
		</form>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" summary="nmap plugin auto scans">
			<tr>
				<td>
					<div class="newtabmenu" style="margin: 1px 0px; width: 775px;">
						<ul class="newtabmenu">
							<li>
								<a href="/pkg_edit.php?xml=nmap-plugin.xml" data-toggle="tooptip" title="<?=gettext("$main_page_tooltip_text"); ?>">
									<span>Main Page</span>
								</a>
							</li>
							<li>
								<a href="/nmap-plugin/nmap-plugin-schedule-scan-times.php" data-toggle="tooltip" title="<?=gettext("$scan_times_tooltip_text"); ?>">
									<span>Scan Times</span>
								</a>
							</li>
							<li>
								<a href="/nmap-plugin/nmap-plugin-email-setup.php" data-toggle="tooltip" title="<?=gettext("$email_setup_tooltip_text"); ?>">
									<span>Email Setup</span>
								</a>
							</li>
							<li class="newtabmenu_active">
								<a href="/nmap-plugin/nmap-plugin-auto-scans.php" data-toggle="tooltip" title="<?=gettext("$auto_scans_tooltip_text"); ?>">
									<span>Auto Scans</span>
								</a>
							</li>
							<li>
								<a href="/nmap-plugin/nmap-plugin-nmap-scan.php" data-toggle="tooltip" title="<?=gettext("$nmap_scan_tooltip_text"); ?>">
									<span>NMap Scan</span>
								</a>
							</li>
							<li>
								<a href="/nmap-plugin/nmap-plugin-scan-results.php" data-toggle="tooltip" title="<?=gettext("$scan_results_tooltip_text"); ?>">
									<span>Scan Results</span>
								</a>
							</li>
						</ul>
					</div>
					<?php/*
						$tab_array = array();
						$tab_array[] = array(gettext("Main Page"), false, "/pkg_edit.php?xml=nmap-plugin.xml");
						$tab_array[] = array(gettext("Scan Times"), false, "/nmap-plugin/nmap-plugin-schedule-scan-times.php");
						$tab_array[] = array(gettext("Email Setup"), false, "/nmap-plugin/nmap-plugin-email-setup.php");
						$tab_array[] = array(gettext("Auto Scans"), true, "/nmap-plugin/nmap-plugin-auto-scans.php");
						$tab_array[] = array(gettext("NMap Scan"), false, "/nmap-plugin/nmap-plugin-nmap-scan.php");
						$tab_array[] = array(gettext("Scan Results"), false, "/nmap-plugin/nmap-plugin-scan-results.php");
						display_top_tabs($tab_array);
					*/?>
				</td>
			</tr>
			<tr>
				<td id="mainarea">
					<div class="tabcont">
						<form action="nmap-plugin-auto-scans.php" method="post" name="iform">
							<table width="100%" border="0" cellpadding="6" cellspacing="0" summary="main area">
								<tr>
									<td width="22%" valign="top" class="vncell"><?=gettext(" "); ?></td>
									<td width="78%" class="vtable">
										<?=gettext("This is where you will start the NMap scans. Below are descriptions of the different types of NMap scans that can be ran."); ?><br />
										<?=gettext("Once the NMap scans have been set to start/stop, a reboot will be necessary to update the list of cron jobs."); ?><br />
									</td>
								</tr>
								<tr>
									<td colspan="2" valign="top" class="listtopic"><?=gettext("Scan Options"); ?></td>
								</tr>
								<tr>
									<td width="22%" valign="top" class="vncell"><?=gettext("DHCP Scan"); ?></td>
									<td width="78%" class="vtable">
										<?=gettext('The "DHCP Scan" will run an NMap scan that will get its hosts from the active DHCP leases and the scan times are determined by the time intervals that have been setup in the "Scan Times" tab. The NMap scan results will then be used to find differences between previous NMap scans. If there are no previous NMap scan results, then the new results will be saved and an email will be sent to the email address that has been setup in the "Email Setup" tab. If there are previous NMap scan results once an NMap scan has finished, the the differences from the new NMap scan and the previous NMap scan will be found an dif there are any differences, then an email will be sent containing the differences.'); ?><br />
									</td>
								</tr>
								<tr>
									<td width="22%" valign="top" class="vncell"><?=gettext("Arp Host Checks"); ?><br />
									<td width="78%" class="vtable">
										<!-- <?=gettext('The "ARP Host Checks" will run every night (at midnight) and start by pinging every IP address on the LAN subnet to determine which IP addresses are still online. The IP addresses that are still online will have an NMap scan ran on them and the differences, if any, will be saved and an email will be sent to the email address that has been setup in the "Email Setup" tab.'); ?><br /> -->
										<?=gettext('The "ARP Host Check" will get its list of hosts from the ARP table. The hosts will then be checked to se if an NMap scan has been ran on them for that day or not. If not then an NMap scan will be ran. If an NMap scan has been ran of the selected hosts, then that host will be ignored until the next time the host check has been ran on a different day.'); ?><br />
									</td>
								</tr>
								<tr>
									<td colspan="2" valign="top" class="listtopic"><?=gettext("Options"); ?></td>
								</tr>
								<tr>
									<td width="22%" valign="top" class="vncell"><?=gettext("Dhcp/Arp Host Check"); ?></td>
									<td width="78%" class="vtable">
										<!--<?php/* echo "Original: $crontabs<br />"; */?>-->
										<?php echo "$dhcp_checkbox\n"; ?>
										<?php echo "$arp_checkbox\n"; ?>
									</td>
								</tr>
								<tr>
									<td width="22%" valign="top" class="vncell"><?=gettext("Interfaces"); ?></td>
									<td width="78%" class="vtable">
										<select name="Selected-Interface" class="formselect" id="Selected-Interface">
										<?php
											echo '<option value="any">Any</option>';
											foreach ($config['interfaces'] as $key => $value) {
												if (isset($value['enable'])) {
													echo '<option value="'.print_r($value['if'], TRUE).'">'.print_r($key, TRUE).'</option>';
												}
											}
										?>
										</select>
										<?=gettext("If you would like the NMap scans to be ran on a specific interface, then select the desired interface in the field above."); ?><br />
									</td>
								</tr>
								<tr>
									<td width="22%" valign="top" class="vncell"><?=gettext("Start NMap Scans"); ?></td>
									<td width="78%" class="vtable">
										<?=gettext('The "Start NMap Scans" button below will start all of the NMap scan options that have been selected above.'); ?><br />
									</td>
								</tr>
								<tr>
									<td width="22%" valign="top" class="vncell"><?=gettext("Stop NMap Scans"); ?></td>
									<td width="78%" class="vtable">
										<?=gettext('The "Stop NMap Scans" button below will stop all of the current crontab-scheduled NMap scans.'); ?><br />
									</td>
								</tr>
								<tr>
									<td width="22%" valign="top" class="vncell"><?=gettext("Reset NMap Scan Results"); ?></td>
									<td width="78%" class="vtable">
										<?=gettext('The "Reset NMap Scan Results" button below will reset the original NMap scan results which will allow the NMap scans to view any host found in the next NMap scan as newly discovered with no other scan results.'); ?><br />
									</td>
								</tr>
								<tr>
									<td width="22%" valign="top" class="vncell"><?=gettext(" "); ?></td>
									<td width="78%" class="vtable">
<pre>
<input type='submit' id='Submit' name='Submit' value='<?=gettext("Start NMap Scans"); ?>' />  <input type='submit' id='Submit' name='Submit' value='<?=gettext("Stop NMap Scans"); ?>' />  <input type='submit' id='Submit' name='Submit' value='<?=gettext("Reset NMap Scan Results"); ?>' />
<input type='submit' id='Submit' name='Submit' value='Refresh' /></pre>
									</td>
								</tr>
							</table>
						</form>
					</div>
				</td>
			</tr>
		</table>
		<script>
			$(document).ready(function(){
				$('[data-toggle="tooltip"]').tooltip();   
			});
		</script>
		<?php include("fend.inc"); ?>
	</body>
</html>
