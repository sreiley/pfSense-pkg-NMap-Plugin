#!/bin/sh
PATH=/bin:/usr/bin:/usr/local/bin:/sbin:/usr/sbin:/usr/local/sbin
# FINISH THE PID FILE CHECKS #
# ADD ABILITY TO STOP BACKGROUND PROCESS AT END OF SCRIPT #

while [ "$#" -gt 0 ]
do
	case "$1" in
	-i)
		shift
		interface="$1"
		;;
	*)
		echo "Unknown Argument: '$1'" >&2
		exit 1
		;;
	esac
	shift
done

if [ -e /var/run/nmapscan-dhcp.pid ]; then
	#check if the process is running
	if [ $(/bin/ps ax | /usr/bin/grep nmap | /usr/bin/grep -o '^\S*') == $(/bin/cat /var/run/nmapscan-dhcp.pid) ]; then
		#create an error message inside of the log file stating that the NMap scan was not ran because another one is already running
		echo "NMap Plugin: Unable to run DHCP NMap scan due to another that is currently running" | logger
		#end script
	elif [ $(/bin/ps ax | /usr/bin/grep nmap | /usr/bin/grep -o '^S*') != $(/bin/cat /var/run/nmapscan-dhcp.pid) ]; then
		#NMap scan ran but did not remove the pid file
		rm /var/run/nmapscan-dhcp.pid
	fi
fi
nmap &
echo $! > /var/run/nmapscan-dhcp.pid
ipaddress="`cat /var/dhcpd/var/db/dhcpd.leases | grep '[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}' | sed 's/.\{2\}$//' | sed 's/^......//'`"
nmap_scan () {
	mkdir -p /usr/local/pkg/nmap-plugin/hosts/$macaddress
	if [ $interface != "" ]; then
		/usr/local/bin/nmap -e $interface -oX /tmp/Host-Check-$macaddress.xml -sP $ip
	elif [ $interface == "" ]; then
		/usr/local/bin/nmap -oX /tmp/Host-Check-$macaddress.xml -sP $ip
	fi
	mv /tmp/Host-Check-$macaddress.xml /usr/local/pkg/nmap-plugin/hosts/$macaddress/Host-Check.xml
	#if the host is not online, then...
	if [ "$(grep -o -E '<address addr=.([[:xdigit:]]{1,2}:){5}[[:xdigit:]]{1,2}.' /usr/local/pkg/nmap-plugin/hosts/$macaddress/Host-Check.xml)" == '' ]; then
		:
	#if the host is online, then...
	elif [ "$(grep -o -E '<address addr=.([[:xdigit:]]{1,2}:){5}[[:xdigit:]]{1,2}.' /usr/local/pkg/nmap-plugin/hosts/$macaddress/Host-Check.xml)" != '' ]; then
		/usr/local/bin/nmap -F -v -oG /tmp/NMap-Scan-Results-$macaddress.txt $ip
		mv /tmp/NMap-Scan-Results-$macaddress.txt /usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Results_grepable.tmp
		sed -e 's/Ignored State: filtered (..)/\ /g' /usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Results_grepable.tmp | sed 's.///..g' | tail -n +2 | sed '$d' | sed '$s/, /[]/g' | tr -s '[]' '\n' | sed 's/Ports: /[]/g' | tr -s '[]' '\n' > /usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Results_grepable.txt
		rm /usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Results_grepable.tmp
		if [ -f /usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original_grepable.txt ]; then
			diff /usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original_grepable.txt /usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Results_grepable.txt > /tmp/diff-$macaddress.txt
			if [ "$(diff -cN /usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original_grepable.txt /usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Results_grepable.txt | grep -A 1 '\----' | tail -1)" != "$(diff -cN /usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original_grepable.txt /usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Results_grepable.txt | grep '\----')" ]; then
				diff -cN /usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original_grepable.txt /usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Results_grepable.txt > /tmp/email-results-$macaddress.txt
				/usr/local/bin/mail.php -s"NMap Plugin: New Change - $macaddress" < /tmp/email-results-$macaddress.txt
				cp /usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original_grepable.txt /usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original.txt
				cp /usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Results_grepable.txt /usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original_grepable.txt
				rm /tmp/diff-$macaddress.txt
				rm /tmp/email-results-$macaddress.txt
			else
				rm /tmp/diff-$macaddress.txt
			fi
		elif [ ! -f /usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original_grepable.txt ]; then
			mv /usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Results_grepable.txt /usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original_grepable.txt
			/usr/local/bin/mail.php -s"NMap Plugin: New Host - $macaddress" < /usr/local/pkg/nmap-plugin/hosts/$macaddress/NMap-Scan-Original_grepable.txt
		fi
	fi
}

for ip in $ipaddress
do
	macaddress="`cat /var/dhcpd/var/db/dhcpd.leases | grep -A 6 "$ip" | grep -o -E '([[:xdigit:]]{1,2}:){5}[[:xdigit:]]{1,2}'`"
	if [ "`date '+%d' | cut -c -1`" == "0" ]; then
		if [ "$(ls -l /usr/local/pkg/nmap-plugin/hosts/$macaddress/ | grep -E -o '.{0,9}NMap-Scan-Original_grepable.txt' | cut -c -2 | cut -c -1)" == ' ' ]; then
			if [ "$(ls -l /usr/local/pkg/nmap-plugin/hosts/$macaddress/ | grep -E -o '.{0,8}NMap-Scan-Original_grepable.txt' | cut -c -1)" != "`date '+%d' | grep -Eo '.$'`" ]; then
				nmap_scan
			else
				:
			fi
		else
			nmap_scan
		fi
	else
		if [ "$(ls -l /usr/local/pkg/nmap-plugin/hosts/$macaddress/ | grep -E -o '.{0,9}NMap-Scan-Original_grepable.txt.txt' | cut -c -2 | cut -c -1)" != ' ' ]; then
			if [ "$(ls -l /usr/local/pkg/nmap-plugin/hosts/$macaddress/ | grep -E -o '.{0,8}NMap-Scan-Original_grepable.txt' | cut -c -2)" != "`date '+%d'`" ]; then
				nmap_scan
			else
				:
			fi
		else
			nmap_scan
		fi
	fi
done

rm /var/run/nmapscan-dhcp.pid
/usr/local/bin/php /usr/local/pkg/nmap-plugin/nmap-plugin-encode-data.php
exit
