<?php
/* COPYRIGHT GOES HERE */
require("guiconfig.inc");
require_once("notices.inc");
require_once("/usr/local/pkg/nmap-plugin.inc");

$pgtitle = array(gettext("Services"),gettext("NMap-Plugin Schedule Scan Times"));
include("head.inc");

if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
	$server_protocol = "https";
} else {
	$server_protocol = "http";
}
$actual_link = "$server_protocol://$_SERVER[HTTP_HOST]/nmap-plugin/nmap-plugin-schedule-scan-times.php";
//echo '<META HTTP-EQUIV="Refresh" Content="10; URL='.$actual_link.'">'; 

$MinutesTime = $_POST['MinutesTime'];
$MinutesFile = "/usr/local/pkg/nmap-plugin/run-time-minutes.txt";
$MinutesArray = array(
	"every-minute" => "* ",
	"" => "*/15 ",
	"5-minutes" => "*/5 ",
	"10-minutes" => "*/10 ",
	"15-minutes" => "*/15 ",
	"20-minutes" => "*/20 ",
	"25-minutes" => "*/25 ",
	"30-minutes" => "*/30 ",
);

$HoursTime = $_POST['HoursTime'];
$HoursCustom = $_POST['CustomHours'];
$HoursFile = "/usr/local/pkg/nmap-plugin/run-time-hours.txt";
$HoursArray = array(
	"default-hours" => "* ",
	"" => "* ",
	"even-hours" => "0,2,4,6,8,10,12,14,16,18,20,22 ",
	"odd-hours" => "1,3,5,7,9,11,13,15,17,19,21,23 ",
	"quarter-hours" => "0,6,12,18 ",
	"half-hours" => "0,12 ",
	"once-hours" => "0 ",
);

$DaysTime = $_POST['DaysTime'];
$DaysCustom = $_POST['CustomDays'];
$DaysFile = "/usr/local/pkg/nmap-plugin/run-time-days.txt";
$DaysArray = array(
	"default-days" => "* ",
	"" => "* ",
	"even-days" => "2,4,6,8,10,12,14,16,18,20,22,24,26,28,30 ",
	"odd-days" => "1,3,5,7,9,11,13,15,17,19,21,23,25,27,29,31 ",
	"half-days" => "1,15 ",
	"once-days" => "1 ",
);

$MonthsTime = $_POST['MonthsTime'];
$MonthsCustom = $_POST['CustomMonths'];
$MonthsFile = "/usr/local/pkg/nmap-plugin/run-time-months.txt";
$MonthsArray = array(
	"default-months" => "* ",
	"" => "* ",
	"even-months" => "2,4,6,8,10,12 ",
	"odd-months" => "1,3,5,7,9,11 ",
	"quarter-months" => "1,4,7,10 ",
	"half-months" => "1,7 ",
	"once-months" => "1 ",
);

$WeekdaysTime = $_POST['WeekdaysTime'];
$WeekdaysCustom = $_POST['CustomWeekdays'];
$WeekdaysFile = "/usr/local/pkg/nmap-plugin/run-time-weekdays.txt";
$WeekdaysArray = array(
	"default-weekdays" => "* ",
	"" => "* ",
	"weekdays-weekdays" => "1,2,3,4,5 ",
	"weekends-weekdays" => "0,6 ",
	"sunday-weekdays" => "0 ",
	"monday-weekdays" => "1 ",
	"tuesday-weekdays" => "2 ",
	"wednesday-weekdays" => "3 ",
	"thursday-weekday" => "4 ",
	"friday-weekdays" => "5 ",
	"saturday-weekdays"=> "6 ",
);

if (array_key_exists("".$MinutesTime."", $MinutesArray)) {
	$MinutesKey = $MinutesArray["$MinutesTime"];
	file_put_contents($MinutesFile, $MinutesKey);
}

if (array_key_exists("".$HoursTime."", $HoursArray)) {
	$HoursKey = $HoursArray["$HoursTime"];
	file_put_contents($HoursFile, $HoursKey);
} elseif ($HoursTime == "custom-hours") {
	exec('(crontab -l 2>/dev/null; echo "* '.$HoursCustom.' * * * /usr/local/pkg/nmap-plugin/auto-nmap-scans.php") | crontab -', $output, $return);
	if (!$return) {
		shell_exec('echo "y" | crontab -r');
		file_put_contents($HoursFile, $HoursCustom);
		file_put_contents($HoursFile, " ", FILE_APPEND);
	} else {
		file_put_contents($HoursFile, "* ");
		$input_errors[] = gettext('The specified time interval for "Hours" is invalid!\n');
	}
}

if (array_key_exists("".$DaysTime."", $DaysArray)) {
	$DaysKey = $DaysArray["$DaysTime"];
	file_put_contents($DaysFile, $DaysKey);
} elseif ($DaysTime == "custom-days") {
	exec('(crontab -l 2>/dev/null; echo "* * '.$DaysCustom.' * * /usr/local/pkg/nmap-plugin/auto-nmap-scans.php") | crontab -', $output, $return);
	if (!$return) {
		shell_exec('echo "y" | crontab -r');
		file_put_contents($DaysFile, $DaysCustom);
		file_put_contents($DaysFile, " ", FILE_APPEND);
	} else {
		file_put_contents($DaysFile, "* ");
		$input_errors[] = gettext('The specified time interval for "Days" is invalid!\n');
	}
}

if (array_key_exists("".$MonthsTime."", $MonthsArray)) {
	$MonthsKey = $MonthsArray["$MonthsTime"];
	file_put_contents($MonthsFile, $MonthsKey);
} elseif ($MonthsTime == "custom-months") {
	exec('(crontab -l 2>/dev/null; echo "* * * '.$MonthsCustom.' * /usr/local/pkg/nmap-plugin/auto-nmap-scans.php") | crontab -', $output, $return);
	if (!$return) {
		shell_exec('echo "y" | crontab -r');
		file_put_contents($MonthsFile, $MonthsCustom);
		file_put_contents($MonthsFile, " ", FILE_APPEND);
	} else {
		file_put_contents($MonthsFile, "* ");
		$input_errors[] = gettext('The specified time interval for "Months" is invalid!\n');
	}
}

if (array_key_exists("".$WeekdaysTime."", $WeekdaysArray)) {
	$WeekdaysKey = $WeekdaysArray["$WeekdaysTime"];
	file_put_contents($WeekdaysFile, $WeekdaysKey);
} elseif ($WeekdaysTime == "custom-weekdays") {
	exec('(crontab -l 2>/dev/null; echo "* * * * '.$WeekdaysCustom.' /usr/local/pkg/nmap-plugin/auto-nmap-scans.php") | crontab -', $output, $return);
	if (!$return) {
		shell_exec('echo "y" | crontab -r');
		file_put_contents($WeekdaysFile, $WeekdaysCustom);
		file_put_contents($WeekdaysFile, " ", FILE_APPEND);
	} else {
		file_put_contents($WeekdaysFile, "* ");
		$input_errors[] = gettext('The specified time interval for "Weekdays" is invalid!\n');
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
		<?php
			if ($input_errors) {
				print_input_errors($input_errors);
			} else {
				$savemsg = gettext("NOTE: To specify custom time intervals, select the appropriate option then click the Submit button at the bottom of the page to enable the desired field(s).");
				print_info_box($savemsg);
			}
		?>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" summary="nmap plugin schedule scan times">
			<tr>
				<td>
					<div class="newtabmenu" style="margin: 1px 0px; width: 775px;">
						<ul class="newtabmenu">
							<li>
								<a href="/pkg_edit.php?xml=nmap-plugin.xml" data-toggle="tooptip" title="<?=gettext("$main_page_tooltip_text"); ?>">
									<span>Main Page</span>
								</a>
							</li>
							<li class="newtabmenu_active">
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
						</ul>
					</div>
					<?php/*
						$tab_array = array();
						$tab_array[] = array(gettext("Main Page"), false, "/pkg_edit.php?xml=nmap-plugin.xml");
						$tab_array[] = array(gettext("Scan Times"), true, "/nmap-plugin/nmap-plugin-schedule-scan-times.php");
						$tab_array[] = array(gettext("Email Setup"), false, "/nmap-plugin/nmap-plugin-email-setup.php");
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
						<form action="nmap-plugin-schedule-scan-times.php" method="post" name="iform">
						<table width="100%" border="0" cellpadding="6" cellspacing="0" summary="main area">
							<tr>
								<td colspan="2" valign="top" class="listtopic"><?=gettext("Schedule Scan Times"); ?></td>
							</tr>
							<tr>
								<td width="22%" valign="top" class="vncell"><?=gettext(" "); ?></td>
								<td width="78%" class="vtable">
									<p>
The Automatic NMap Scan times are setup using Crontab. To learn more about Crontab and how to
setup a command and/or script to run with crontab, click the link below or scroll down to the
bottom of this page for a brief explanation.
									</p>
									<p><a href="http://crontab.org" target="_blank">Crontab Information</a></p>
									<p><a href='http://www.freebsd.org/doc/en/books/handbook/configtuning-cron.html' target='_blank'>FreeBSD Cron Information</a></p>
								</td>
							</tr>
							<tr>
								<td width="22%" valign="top" class="vncell"><?=gettext('Schedule "Minutes" Interval'); ?></td>
								<td width="78%" class="vtable">
									<select name="MinutesTime" class="formselect">
										<option value="every-minute" <?php if ($MinutesTime == "every-minute") echo "selected=\"selected\"" ?>>Every Minute*</option>
										<option value="5-minutes" <?php if ($MinutesTime == "5-minutes") echo "selected=\"selected\"" ?>>Every 5 Minutes</option>
										<option value="10-minutes" <?php if ($MinutesTime == "10-minutes") echo "selected=\"selected\"" ?>>Every 10 Minutes</option>
										<option value="default-minutes" <?php if ($MinutesTime == "" || $MinutesTime == "default-minutes") echo "selected=\"selected\"" ?>>Every 15 Minutes - Default</option>
										<option value="20-minutes" <?php if ($MinutesTime == "20-minutes") echo "selected=\"selected\"" ?>>Every 20 Minutes</option>
										<option value="25-minutes" <?php if ($MinutesTime == "25-minutes") echo "selected=\"selected\"" ?>>Every 25 Minutes</option>
										<option value="30-minutes" <?php if ($MinutesTime == "30-minutes") echo "selected=\"selected\"" ?>>Every 30 Minutes</option>
									</select><br />
									<?=gettext("These options are used to select which interval, in minutes, that the NMap scan will be ran at."); ?><br />
									<?=gettext('*This option is not recommended because some hosts may take more than a minute to fully complete the NMap scan'); ?><br />
								</td>
							</tr>
							<tr>
								<td width="22%" valign="top" class="vncell">
									<?=gettext('Schedule "Hours" Interval'); ?><br /><br /><br />
									<?=gettext('Specify Custom "Hours" Interval'); ?><br />
								</td>
								<td width="78%" class="vtable">
									<select name="HoursTime" class="formselect" id="HoursTime">
										<option value="default-hours" <?php if ($HoursTime == "" || $HoursTime == "default-hours") echo "selected=\"selected\"" ?>>Every Hour - Default</option>
										<option value="even-hours" <?php if ($HoursTime == "even-hours") echo "selected=\"selected\"" ?>>Every Even Hour (2,4,6,etc.)</option>
										<option value="odd-hours" <?php if ($HoursTime == "odd-hours") echo "selected=\"selected\"" ?>>Every Odd Hour (1,3,5,etc.)</option>
										<option value="quarter-hours" <?php if ($HoursTime == "quarter-hours") echo "selected=\"selected\"" ?>>Every 6 Hours or 1/4th of a day</option>
										<option value="half-hours" <?php if ($HoursTime == "half-hours") echo "selected=\"selected\"" ?>>Every 12 Hours or 1/2 of a day</option>
										<option value="once-hours" <?php if ($HoursTime == "once-hours") echo "selected=\"selected\"" ?>>Once a Day</option>
										<option value="custom-hours" <?php if ($HoursTime == "custom-hours") echo "selected=\"selected\"" ?>>Specify Custom "Hour" Intervals</option>
									</select><br />
									<?=gettext("These options are used to specify which interval, in hours, that the NMap scan will be ran at."); ?><br /><br />
									<?php
										$HoursCustomContents = file_get_contents($HoursFile);
										if ($HoursTime == "custom-hours") {
											echo '<input type="text" name="CustomHours" value="'.$HoursCustomContents.'"/><br />';
										} else {
											echo '<input type="text" name="CustomHours" disabled><br />';
										}
									?>
									<?=gettext('Enter a custom time frame, in hours, that you would like to set the "Hours" Interval to.'); ?><br />
									<?=gettext('NOTE: This field will only be available if the field "Specify Custom "Hour" Intervals" has been selected above.'); ?><br />
								</td>
							</tr>
							<tr>
								<td width="22%" valign="top" class="vncell">
									<?=gettext('Schedule "Days" Interval'); ?><br /><br /><br />
									<?=gettext('Specify Custom "Days" Interval'); ?><br />
								</td>
								<td width="78%" class="vtable">
									<select name="DaysTime" class="formselect" id="DaysTime">
										<option value="default-days" <?php if ($DaysTime == "" || $DaysTime == "default-days") echo "selected=\"selected\"" ?>>Every Day - Default</option>
										<option value="even-days" <?php if ($DaysTime == "even-days") echo "selected=\"selected\"" ?>>Every Even Day (2,4,6,etc.)</option>
										<option value="odd-days" <?php if ($DaysTime == "odd-days") echo "selected=\"selected\"" ?>>Every Odd Day (1,3,5,etc.)</option>
										<option value="half-days" <?php if ($DaysTime == "half-days") echo "selected=\"selected\"" ?>>Twice a Month</option>
										<option value="once-days" <?php if ($DaysTime == "once-days") echo "selected=\"selected\"" ?>>Once a Month</option>
										<option value="custom-days" <?php if ($DaysTime == "custom-days") echo "selected=\"selected\"" ?>>Specify Custom "Day" Intervals</option>
									</select><br />
									<?=gettext("These options are used to specify which interval, in days, that the NMap scan will be ran at."); ?><br /><br />
									<?php
										$DaysCustomContents = file_get_contents($DaysFile);
										if ($DaysTime == "custom-days") {
											echo '<input type="text" name="CustomDays" value="'.$DaysCustomContents.'"/><br />';
										} else {
											echo '<input type="text" name="CustomDays" disabled><br />';
										}
									?>
									<?=gettext('Enter a custom time frame, in days, that you would like to set the "Days" Interval to.'); ?><br />
									<?=gettext('NOTE: This field will only be available if the field "Specify Custom "Day" Intervals" has been selected above.'); ?><br />
								</td>
							</tr>
							<tr>
								<td width="22%" valign="top" class="vncell">
									<?=gettext('Schedule "Months" Interval'); ?><br /><br /><br />
									<?=gettext('Specify Custom "Months" Interval'); ?><br />
								</td>
								<td width="78%" class="vtable">
									<select name="MonthsTime" class="formselect" id="MonthsTime">
										<option value="default-months" <?php if ($MonthsTime == "" || $MonthsTime == "default-months") echo "selected=\"selected\"" ?>>Every Month - Default</option>
										<option value="even-months" <?php if ($MonthsTime == "even-months") echo "selected=\"selected\"" ?>>Every Even Month (2,4,6,etc.)</option>
										<option value="odd-months" <?php if ($MonthsTime == "odd-months") echo "selected=\"selected\"" ?>>Every Odd Month (1,3,5,etc.)</option>
										<option value="quarter-months" <?php if ($MonthsTime == "quarter-months") echo "selected=\"selected\"" ?>>Every 3 Months or 1/4th of a year</option>
										<option value="half-months" <?php if ($MonthsTime == "half-months") echo "selected=\"selected\"" ?>>Every 6 Months or 1/2 of a year</option>
										<option value="once-months" <?php if ($MonthsTime == "once-months") echo "selected=\"selected\"" ?>>Once a Year</option>
										<option value="custom-months" <?php if ($MonthsTime == "custom-months") echo "selected=\"selected\"" ?>>Specify Custom "Month" Intervals</option>
									</select><br />
									<?=gettext("These options are used to specify which interval, in months, that the NMap scan will be ran at."); ?><br /><br />
									<?php
										$MonthsCustomContents = file_get_contents($MonthsFile);
										if ($MonthsTime == "custom-months") {
											echo '<input type="text" name="CustomMonths" value="'.$MonthsCustomContents.'"/><br />';
										} else {
											echo '<input type="text" name="CustomMonths" disabled><br />';
										}
									?>
									<?=gettext('Enter a custom time frame, in months, that you would like to set the "Months" Interval to be.'); ?><br />
									<?=gettext('NOTE: This field will only be available if the field "Specify Custom "Month" Intervals" has been selected above.'); ?><br />
								</td>
							</tr>
							<tr>
								<td width="22%" valign="top" class="vncell">
									<?=gettext('Schedule "Days of the Week" Interval'); ?><br /><br /><br />
									<?=gettext('Specify Custom "Days of the Week" Interval'); ?><br />
								</td>
								<td width="78%" class="vtable">
									<select name="WeekdaysTime" class="formselect" id="WeekdaysTime">
										<option value="default-weekdays" <?php if ($WeekdaysTime == "" || $WeekdaysTime == "default-weekdays") echo "selected=\"selected\"" ?>>Every Day of the Week - Default</option>
										<option value="weekdays-weekdays" <?php if ($WeekdaysTime == "weekdays-weekdays") echo "selected=\"selected\"" ?>>Only During the Week (Monday, Tuesday, etc.)</option>
										<option value="weekends-weekdays" <?php if ($WeekdaysTime == "weekends-weekdays") echo "selected=\"selected\"" ?>>Only on the Weekends (Sunday and Saturday)</option>
										<option value="sunday-weekdays" <?php if ($WeekdaysTime == "sunday-weekdays") echo "selected=\"selected\"" ?>>Only on Sundays</option>
										<option value="monday-weekdays" <?php if ($WeekdaysTime == "monday-weekdays") echo "selected=\"selected\"" ?>>Only on Mondays</option>
										<option value="tuesday-weekdays" <?php if ($WeekdaysTime == "tuesday-weekdays") echo "selected=\"selected\"" ?>>Only on Tuesdays</option>
										<option value="wednesday-weekdays" <?php if ($WeekdaysTime == "wednesday-weekdays") echo "selected=\"selected\"" ?>>Only on Wednesdays</option>
										<option value="thursday-weekdays" <?php if ($WeekdaysTime == "thursday-weekdays") echo "selected=\"selected\"" ?>>Only on Thursdays</option>
										<option value="friday-weekdays" <?php if ($WeekdaysTime == "friday-weekdays") echo "selected=\"selected\"" ?>>Only on Fridays</option>
										<option value="saturday-weekdays" <?php if ($WeekdaysTime == "saturday-weekdays") echo "selected=\"selected\"" ?>>Only on Saturdays</option>
										<option value="custom-weekdays" <?php if ($WeekdaysTime == "custom-weekdays") echo "selected=\"selected\"" ?>>Specify Custom "Day of the Week" Intervals</option>
									</select><br />
									<?=gettext("These options are used to specify which interval, in days of the week, that the NMap scan will be ran at."); ?><br /><br />
									<?php
										$WeekdaysCustomContents = file_get_contents($WeekdaysFile);
										if ($WeekdaysTime == "custom-weekdays") {
											echo '<input type="text" name="CustomWeekdays" value="'.$WeekdaysCustomContents.'"/><br />';
										} else {
											echo '<input type="text" name="CustomWeekdays" disabled><br />';
										}
									?>
									<?=gettext('Enter a custom time frame, in days of the week, that you would like to set the "Days of the Week" Interval to be.'); ?><br />
									<?=gettext('NOTE: This field will only be available if the field "Specify Custom "Days of the Week" Intervals" has been selected above.'); ?><br />
								</td>
							</tr>
							<tr>
								<td width="22%" valign="top" class="vncell">
									<?=gettext('Currently Scheduled Time Intervals'); ?>
								</td>
								<td width="78%" class="vtable">
The current time intervals that have been setup are listed below.
									<table border="1" style="width:100%">
									  <tr>
										<td>Minutes</td>
										<td>Hours</td>		
										<td>Days</td>
										<td>Months</td>
										<td>Days of the Week</td>
									  </tr>
									  <tr>
										<td><?php echo file_get_contents($MinutesFile); ?></td>
										<td><?php echo file_get_contents($HoursFile); ?></td>		
										<td><?php echo file_get_contents($DaysFile); ?></td>
										<td><?php echo file_get_contents($MonthsFile); ?></td>
										<td><?php echo file_get_contents($WeekdaysFile); ?></td>
									  </tr>
									</table>
Some of the time intervals may contain a string of numbers, this is to ensure that the
times are correctly executed without an offset in seconds.
								</td>
							</tr>
							<tr>
								<td width="22%" valign="top" class="vncell">
									<?=gettext('Crontab Info'); ?>
								</td>
								<td width="78%" class="vtable">
									<pre>
Field           Values
-----           ------
Minute          0-59
Hour            0-23
Day of Month    0-31
Month           0-12
Day of Week     0-7 (0 or 7 can be used for Sunday)

An asterisk (*) represents an "every" interval.
EX: "*/2" under the "Minutes" interval would represent every 2 minutes.

To specify specific time intervals, you can seperate number with a comma.
EX: "2,3,5,7" under the "Minutes" interval would represent to run the
command once the minutes are either 2, 3, 5, or 7. 

Vixie, Paul. "Crontab.org - CRONTAB(5)." Crontab.org - CRONTAB(5). Ed. Ilya
Sukhar. Paul Vixie, 24 Jan. 199. Web. 15 June 2015.</pre>
								</td>
							</tr>
							<tr>
							<form action="/nmap-plugin/nmap-plugin-schedule-scan-times.php" method="post">
								<td width="22%" valign="top" class="vncell"><?=gettext(" "); ?></td>
								<td width="78%" class="vtable">
									<p><input type="submit" value="Submit"> </p>
								</td>
							</form>
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
