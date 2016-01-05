<?php
/* COPYRIGHT GOES HERE */
require("guiconfig.inc");
require_once("notices.inc");
require_once("/usr/local/pkg/nmap-plugin.inc");

$pgtitle = array(gettext("Services"),gettext("NMap-Plugin View Results"));
include("head.inc");

$macaddress = $_GET['id'];
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
		<table width="100%" border="0" cellpadding="0" cellspacing="0" summary="nmap plugin view results">
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
							<li>
								<a href="/nmap-plugin/nmap-plugin-scan-results.php" data-toggle="tooltip" title="<?=gettext("$scan_results_tooltip_text"); ?>">
									<span>Scan Results</span>
								</a>
							</li>
							<li class="newtabmenu_active">
								<a href="<?=gettext("/nmap-plugin/nmap-plugin-view-results.php?id=$macaddress"); ?>" data-toggle="tooltip" title="<?=gettext("View Results - $macaddress"); ?>">
									<span><?=gettext("Host - $macaddress"); ?></span>
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
						$tab_array[] = array(gettext("Scan Results"), false, "/nmap-plugin/nmap-plugin-scan-results.php");
						$tab_array[] = array(gettext("Host - $macaddress"), true, "/nmap-plugin/nmap-plugin-view-results.php?id=$macaddress");
						display_top_tabs($tab_array);
					*/?>
				</td>
			</tr>
			<tr>
				<td id="mainarea">
					<div class="tabcont">
							<table width="100%" border="0" cellpadding="6" cellspacing="0" summary="main area">
								<tr>
									<td colspan="2" valign="top" class="listtopic"><?=gettext("Original NMap Scan Results for $macaddress"); ?></td>
								</tr>
								<tr>
									<td width="78%" class="vtable">
										<pre><?php
											if (!file_exists("/usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original_grepable.txt") ||
											    !file_exists("/usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original.txt")) {
												echo 'There are currently no Original NMap Scan Results for the host "'.$macaddress.'"';
											} elseif (file_exists("/usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original_grepable.txt") ||
											          file_exists("/usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original.txt")) {
												if (!file_exists("/usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original.txt")) {
													$ScanResultsOriginal = file_get_contents("/usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original_grepable.txt");
												} elseif (file_exists("/usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original.txt")) {
													$ScanResultsOriginal = file_get_contents("/usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original.txt");
												}
												if ("" == trim($ScanResultsOriginal)) {
													echo 'There are currently no Original NMap Scan Results for the host "'.$macaddress.'"';
													unlink("/usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original.txt");
												} else {
													echo "$ScanResultsOriginal";
												}
											}
										?></pre>
									</td>
								</tr>
								<tr>
									<td colspan="2" valign="top" class="listtopic"><?=gettext("New NMap Scan Results for $macaddress"); ?></td>
								</tr>
								<tr>
									<td width="78%" class="vtable">
										<pre><?php
											if (!file_exists("/usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Results_grepable.txt")) {
												echo 'There are currently no New NMap Scan Results for the host "'.$macaddress.'"';
											} elseif (file_exists("/usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Results_grepable.txt")) {
												$ScanResultsNew = file_get_contents("/usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Results_grepable.txt");
												if ("" == trim($ScanResultsNew)) {
													echo 'There are currently no New NMap Scan Results for the host "'.$macaddress.'"';
												} else {
													echo "$ScanResultsNew";
												}
											}
										?></pre>
									</td>
								</tr>
								<tr>
									<td>
										<form action="/nmap-plugin/nmap-plugin-scan-results.php">
											<input type="submit" value="GO BACK" />
										</form>
									</td>
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
