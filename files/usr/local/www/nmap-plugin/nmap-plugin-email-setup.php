<?php
/* COPYRIGHT GOES HERE */
require("guiconfig.inc");
require_once("notices.inc");
require_once("/usr/local/pkg/nmap-plugin.inc");

// SMTP
$pconfig['disable_smtp'] = isset($config['notifications']['smtp']['disable']);
if ($config['notifications']['smtp']['ipaddress']) {
	$pconfig['smtpipaddress'] = $config['notifications']['smtp']['ipaddress'];
}
if ($config['notifications']['smtp']['port']) {
	$pconfig['smtpport'] = $config['notifications']['smtp']['port'];
}
if (isset($config['notifications']['smtp']['ssl'])) {
	$pconfig['smtpssl'] = true;
}
if (isset($config['notifications']['smtp']['tls'])) {
	$pconfig['smtptls'] = true;
}
if ($config['notifications']['smtp']['notifyemailaddress']) {
	$pconfig['smtpnotifyemailaddress'] = $config['notifications']['smtp']['notifyemailaddress'];
}
if ($config['notifications']['smtp']['username']) {
	$pconfig['smtpusername'] = $config['notifications']['smtp']['username'];
}
if ($config['notifications']['smtp']['password']) {
	$pconfig['smtppassword'] = $config['notifications']['smtp']['password'];
}
if ($config['notifications']['smtp']['authentication_mechanism']) {
	$pconfig['smtpauthmech'] = $config['notifications']['smtp']['authentication_mechanism'];
}
if ($config['notifications']['smtp']['fromaddress']) {
	$pconfig['smtpfromaddress'] = $config['notifications']['smtp']['fromaddress'];
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;
	/* if this is an AJAX caller then handle via JSON */
	if (isAjax() && is_array($input_errors)) {
		input_errors2Ajax($input_errors);
		exit;
	}
	if ($_POST['apply']) {
		$retval = 0;
		system_setup_sysctl();
		$savemsg = get_std_save_message($retval);
	}

	if ($_POST['Submit'] == gettext("Save")) {
		$tunableent = array();
		// SMTP
		$config['notifications']['smtp']['ipaddress'] = $_POST['smtpipaddress'];
		$config['notifications']['smtp']['port'] = $_POST['smtpport'];
		if (isset($_POST['smtpssl'])) {
			$config['notifications']['smtp']['ssl'] = true;
		} else {
			unset($config['notifications']['smtp']['ssl']);
		}
		if (isset($_POST['smtptls'])) {
			$config['notifications']['smtp']['tls'] = true;
		} else {
			unset($config['notifications']['smtp']['tls']);
		}
		$config['notifications']['smtp']['notifyemailaddress'] = $_POST['smtpnotifyemailaddress'];
		$config['notifications']['smtp']['username'] = $_POST['smtpusername'];
		$config['notifications']['smtp']['password'] = $_POST['smtppassword'];
		$config['notifications']['smtp']['authentication_mechanism'] = $_POST['smtpauthmech'];
		$config['notifications']['smtp']['fromaddress'] = $_POST['smtpfromaddress'];

		if ($_POST['disable_smtp'] == "yes") {
			$config['notifications']['smtp']['disable'] = true;
		} else {
			unset($config['notifications']['smtp']['disable']);
		}
		write_config(); //write the changes to the config file
		pfSenseHeader("nmap-plugin-email-setup.php");
		return;
	}
	if ($_POST['test_smtp'] == gettext("Test Email")) {
		// Send test message via smtp
		if (file_exists("/var/db/notices_lastmsg.txt")) {
			unlink("/var/db/notices_lastmsg.txt");
		}
		send_smtp_message("This is a test email. If you received this email as directed, then this means that the NMap Plugin will successfully send NMap scan results.", "NMap Plugin: Test Email");
	}
}

$pgtitle = array(gettext("Services"),gettext("NMap-Plugin Email Setup"));
include("head.inc");
?>
	<body link="#0000CC" vlink="#0000CC" alink="#0000CC">
		<?php include("fbegin.inc"); ?>
		<form action="nmap-plugin-email-setup.php" method="post">
			<?php
				if ($input_errors) {
					print_input_errors($input_errors);
				}
				if ($savemsg) {
					print_info_box($savemsg);
				}
			?>
		</form>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" summary="nmap plugin email setup">
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
							<li class="newtabmenu_active">
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
						</ul>
					</div>
					<?php/*
						$tab_array = array();
						$tab_array[] = array(gettext("Main Page"), false, "/pkg_edit.php?xml=nmap-plugin.xml");
						$tab_array[] = array(gettext("Scan Times"), false, "/nmap-plugin/nmap-plugin-schedule-scan-times.php");
						$tab_array[] = array(gettext("Email Setup"), true, "/nmap-plugin/nmap-plugin-email-setup.php");
						$tab_array[] = array(gettext("Auto Scans"), false, "/nmap-plugin/nmap-plugin-auto-scans.php");
						$tab_array[] = array(gettext("NMap Scan"), false, "/nmap-plugin/nmap-plugin-nmap-scan.php");
						$tab_array[] = array(gettext("Scan Results"), false, "/nmap-plugin/nmap-plugin-scan-results.php");
						display_top_tabs($tab_array);
					*/?>
				</td>
			</tr>
			<tr>
				<td id="mainarea">
					<div class="tabcont">
						<form action="nmap-plugin-email-setup.php" method="post" name="iform">
							<table width="100%" border="0" cellpadding="6" cellspacing="0" summary="main area">
								<!-- SMTP -->
								<tr>
									<td width="22%" valign="top" class="vncell"><?=gettext(" "); ?></td>
									<td width="78%" class="vtable">
										<?=gettext("All of the information below can also be setup in:"); ?><br />
										<p><a href="/system_advanced_notifications.php" target="_blank"><?=gettext("System -> Advanced -> Notifications"); ?></a></p>
									</td>
								</tr>
								<tr>
									<td colspan="2" valign="top" class="listtopic"><?=gettext("SMTP E-Mail"); ?></td>
								</tr>
								<tr>
									<td width="22%" valign="top" class="vncell"><?=gettext("E-Mail server"); ?></td>
									<td width="78%" class="vtable">
										<input name='smtpipaddress' value='<?php echo htmlspecialchars($pconfig['smtpipaddress'], ENT_QUOTES | ENT_HTML401); ?>' /><br />
										<?=gettext("This is the FQDN (Fully Qualified Domain Name) or IP address of the SMTP (Simple Mail Transfer Protocol) E-Mail server to which NMap scan results will be sent."); ?><br />
									</td>
								</tr>
								<tr>
									<td width="22%" valign="top" class="vncell"><?=gettext("SMTP Port of E-Mail server"); ?></td>
									<td width="78%" class="vtable">
										<input name='smtpport' value='<?php echo htmlspecialchars($pconfig['smtpport'], ENT_QUOTES | ENT_HTML401); ?>' /><br />
										<?=gettext("This is the port of the SMTP E-Mail server, typically 465 (smtps) or 587 (submission)"); ?>
									</td>
								</tr>
								<tr>
									<td width="22%" valign="top" class="vncell"><?=gettext("Secure SMTP Connection"); ?></td>
									<td width="78%" class="vtable">
										<input type='checkbox' id='smtpssl' name='smtpssl' <?php if (isset($pconfig['smtpssl'])) echo "checked=\"checked\""; ?> />Enable SMTP over SSL/TLS<br />
										<input type='checkbox' id='smtptls' name='smtptls' <?php if (isset($pconfig['smtptls'])) echo "checked=\"checked\""; ?> />Enable STARTTLS<br />
									</td>
								</tr>
								<tr>
									<td width="22%" valign="top" class="vncell"><?=gettext("From e-mail address"); ?></td>
									<td width="78%" class="vtable">
										<input name='smtpfromaddress' type='text' value='<?php echo htmlspecialchars($pconfig['smtpfromaddress'], ENT_QUOTES | ENT_HTML401); ?>' /><br />
										<?=gettext("This is the e-mail address that will appear in the FROM field."); ?>
									</td>
								</tr>
								<tr>
									<td width="22%" valign="top" class="vncell"><?=gettext("Notification E-Mail address"); ?></td>
									<td width="78%" class="vtable">
										<input name='smtpnotifyemailaddress' type='text' value='<?php echo htmlspecialchars($pconfig['smtpnotifyemailaddress'], ENT_QUOTES | ENT_HTML401); ?>' /><br />
										<?=gettext("Enter the e-mail address that you would like the NMap scan results to be sent to."); ?>
									</td>
								</tr>
								<tr>
									<td width="22%" valign="top" class="vncell"><?=gettext("Notification E-Mail auth username"); ?></td>
									<td width="78%" class="vtable">
										<input name='smtpusername' type='text' value='<?php echo htmlspecialchars($pconfig['smtpusername'], ENT_QUOTES | ENT_HTML401); ?>' /><br />
										<?=gettext("Enter the e-mail address' username for SMTP authentication. Typically this is the email address."); ?>
									</td>
								</tr>
								<tr>
									<td width="22%" valign="top" class="vncell"><?=gettext("Notification E-Mail auth password"); ?></td>
									<td width="78%" class="vtable">
										<input name='smtppassword' type='password' value='<?php echo htmlspecialchars($pconfig['smtppassword'], ENT_QUOTES | ENT_HTML401); ?>' /><br />
										<?=gettext("Enter the e-mail address' password for SMTP authentication."); ?>
									</td>
								</tr>
								<tr>
									<td width="22%" valign="top" class="vncell"><?=gettext("Notification E-Mail auth mechanism"); ?></td>
									<td width="78%" class="vtable">
										<select name='smtpauthmech' id='smtpauthmech' class="formselect">
										<?php
											foreach ($smtp_authentication_mechanisms as $name => $desc):
												$selected = "";
												if ($pconfig['smtpauthmech'] == $name)
													$selected = "selected=\"selected\"";
												?>
												<option value="<?=$name;?>" <?=$selected;?>><?=$desc;?></option>
											<?php endforeach; ?>
										</select>
										<br />
										<?=gettext("Select the authentication mechanism used by the SMTP server. Some SMTP servers work with PLAIN, however, most SMTP servers like Gmail, Exchange, or Office365 work with LOGIN."); ?>
									</td>
								</tr>
								<tr>
									<td width="22%" valign="top" class="vncell"><?=gettext("Setup Help"); ?></td>
									<td width="78%" class="vtable">
										<?=gettext("The below information may help you sent up the email address if you are unsure on what to enter into the above fields."); ?><br />
										<a href="https://forum.pfsense.org/index.php/topic,31580.0.html" target="_blank">pfSense Forum Post #1</a><br />
										<a href="https://forum.pfsense.org/index.php?topic=31550.0" target="_blank">pfSense Forum Post #2</a><br />
										<?=gettext("Gmail's FQDN: smtp.gmail.com"); ?><br />
										<?=gettext("Yahoo's FQDN: smtp.yahoo.com"); ?><br />
										<?=gettext("Office365's FQDN: smtp.office365.com"); ?>
									</td>
								</tr>
								<tr>
									<td valign="top" class="">
										&nbsp;
									</td>
									<td>
										<input type='submit' id='test_smtp' name='test_smtp' value='<?=gettext("Test Email"); ?>' />
										<br /><?= gettext("NOTE: If you click the button above and you receive a test email, this means that the emailing service is working and the NMap Plugin will successfully send NMap results"); ?>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="list" height="12">&nbsp;</td>
								</tr>
								<tr>
									<td valign="top" class="">
										&nbsp;
									</td>
									<td>
										<input type='submit' id='Submit' name='Submit' value='<?=gettext("Save"); ?>' /><br />
										<?=gettext("Click the button above to save any changes that have been made in this tab."); ?>
									</td>
								</tr>
							</table>
						</form>
					</div>
				</td>
			</tr>
		</table>
		<script type="text/javascript">
//<![CDATA[
		jQuery(document).ready(function() {
			if (jQuery('#smtpssl').is(':checked')) {
				jQuery('#smtptls').prop('disabled', true);
			} else if  (jQuery('#smtptls').is(':checked')) {
				jQuery('#smtpssl').prop('disabled', true);
			}
		});
		jQuery('#smtpssl').change( function() {
			jQuery('#smtptls').prop('disabled', this.checked);
		});
		jQuery('#smtptls').change( function() {
			jQuery('#smtpssl').prop('disabled', this.checked);
		});
//]]>
		</script>
		<script>
			$(document).ready(function(){
				$('[data-toggle="tooltip"]').tooltip();   
			});
		</script>
		<?php include("fend.inc"); ?>
	</body>
</html>
