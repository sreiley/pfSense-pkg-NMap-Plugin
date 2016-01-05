#!/usr/local/bin/php
<?php
$options = getopt("i:");
require_once("config.inc");
require_once("gwlb.inc");
require_once("interfaces.inc");

$interface_name = $options[i];
$realif = get_real_interface($interface_name);
$ipaddr = get_interface_ip($interface_name);
$subnet = get_interface_subnet($interface_name);

printf("%s/%s",
	$ipaddr,
	$subnet
);
?>
