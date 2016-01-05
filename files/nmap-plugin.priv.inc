<?php
/* COPYRIGHT GOES HERE */
global $priv_list;
$priv_list['page-services-nmap-plugin'] = array();
$priv_list['page-services-nmap-plugin']['name'] = "WebCfg - Services: NMap Plugin package";
$priv_list['page-services-nmap-plugin']['descr'] = "Allow access to NMap Plugin package GUI";
$priv_list['page-services-nmap-plugin']['match'] = array();
$priv_list['page-services-nmap-plugin']['match'][] = "pkg_edit.php?xml=nmap-plugin.xml*";
$priv_list['page-services-nmap-plugin']['match'][] = "nmap-plugin/nmap-plugin-schedule-scan-times.php*";
$priv_list['page-services-nmap-plugin']['match'][] = "nmap-plugin/nmap-plugin-email-setup.php*";
$priv_list['page-services-nmap-plugin']['match'][] = "nmap-plugin/nmap-plugin-auto-scans.php*";
$priv_list['page-services-nmap-plugin']['match'][] = "nmap-plugin/nmap-plugin-nmap-scan.php*";
$priv_list['page-services-nmap-plugin']['match'][] = "nmap-plugin/nmap-plugin-scan-results.php*";
$priv_list['page-services-nmap-plugin']['match'][] = "nmap-plugin/nmap-plugin-view-results.php*";
?>
