<?php
/* COPYRIGHT GOES HERE */
require("guiconfig.inc");
require_once("notices.inc");
require_once("/usr/local/pkg/nmap-plugin.inc");

$pgtitle = array(gettext("Services"),gettext("NMap-Plugin Scan Results"));
include("head.inc");

ob_start();
	include("/usr/local/pkg/nmap-plugin/nmap-plugin-decode-data.php");
ob_end_clean();
$list_hosts = @preg_grep('/^([^.])/', scandir('/usr/local/pkg/nmap-plugin/hosts/', 1));
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
		<?php
			$savemsg = gettext('NOTE: If you see a "foreach" error, then this means that the NMap scan has not been ran yet.');
			print_info_box($savemsg);
		?>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" summary="nmap plugin scan results">
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
								<li>
									<a href="/nmap-plugin/nmap-plugin-auto-scans.php" data-toggle="tooltip" title="<?=gettext("$auto_scans_tooltip_text"); ?>">
										<span>Auto Scans</span>
									</a>
								</li>
								<li>
									<a href="/nmap-plugin/nmap-plugin-nmap-scan.php" data-toggle="tooltip" title="<?=gettext("$nmap_scan_tooltip_text"); ?>">
										<span>NMap Scan</span>
									</a>
								</li>
								<li class="newtabmenu_active">
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
							$tab_array[] = array(gettext("Auto Scans"), false, "/nmap-plugin/nmap-plugin-auto-scans.php");
							$tab_array[] = array(gettext("NMap Scan"), false, "/nmap-plugin/nmap-plugin-nmap-scan.php");
							$tab_array[] = array(gettext("Scan Results"), true, "/nmap-plugin/nmap-plugin-scan-results.php");
							display_top_tabs($tab_array);
						*/?>
				</td>
			</tr>
			<tr>
			<td id="mainarea">
				<div class="tabcont">
					<table width="100%" border="0" cellpadding="6" cellspacing="0" summary="main area">
					<tr>
						<td width="78%" class="vtable">
						<?=gettext("This tab is used to view the scan results of certain hosts which can be a substitute for viewing the emails or manually view the scan results using the shell."); ?><br />
						</td>
					</tr>
					<tr>
						<td colspan="2" valign="top" class="listtopic"><?=gettext("List of Hosts"); ?></td>
					</tr>
					<tr>
						<td width="78%" class="vtable">
							<pre><font size="2"><?php 
								foreach ($list_hosts as $macaddress) {
									$host = `echo $macaddress\n`;
									$mac_hostname = shell_exec('arp -a | grep "'.$macaddress.'" | sed "s/([^(]*$//"');
									$macaddress_hostname = `echo $mac_hostname\n`;
									echo '<a href="/nmap-plugin/nmap-plugin-view-results.php?id='.$macaddress.'">'.$macaddress.' '.$macaddress_hostname.'</a>';
								}
							?></font></pre><br />
						</td>
					</tr>
					<tr>
					<form action="/nmap-plugin/nmap-plugin-view-results.php" method="post">
						<td width="78%" class="vtable">
						<input type="text" name="hostmacaddress" /><br />
						<?=gettext("To view the scan results of a host, enter it's MAC address in the field above."); ?><br />
						<p><input type="submit" value="View Scan Results"> </p>
						</td>
					</form>
					</tr>
					</table>
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
