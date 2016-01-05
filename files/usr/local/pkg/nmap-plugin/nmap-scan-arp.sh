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

if [ -e /var/run/nmapscan-arp.pid ]; then
	#check if the process is running
	if [ $(/bin/ps ax | /usr/bin/grep nmap | /usr/bin/grep -o '^\S*') == $(/bin/cat /var/run/nmapscan-arp.pid) ]; then
		#create an error message inside of the log file stating that the NMap scan was not ran because another one is already running
		echo "NMap Plugin: Unable to run ARP NMap scan due to another that is currently running" | logger
		#end script
	elif [ $(/bin/ps ax | /usr/bin/grep nmap | /usr/bin/grep -o '^S*') != $(/bin/cat /var/run/nmapscan-arp.pid) ]; then
		#NMap scan ran but did not remove the pid file
		rm /var/run/nmapscan-arp.pid
fi
nmap &
echo $! > /var/run/nmapscan-arp.pid

rm /tmp/Host-Check.xml
if [ "$interface" != "" ]; then
	#interfaceip="`/sbin/ifconfig $interface | grep 'inet' | sed '/inet6/d' | tr -s '\t' ' ' | sed 's/^......//' | grep -o '^\S*'`"
	interfaceip="`/usr/local/pkg/nmap-plugin/interface-information.php -i $interface`"
	/usr/local/bin/nmap -e "$interface" -oX /tmp/Host-Check.xml -sP "$interfaceip"
elif [ "$interface" == "" ]; then
	#laninterfaceip="`xmllint --xpath 'string(//pfsense/interfaces/lan/ipaddr)' /cf/conf/config.xml`/`xmllint --xpath 'string(//pfsense/interfaces/lan/subnet)' /cf/conf/config.xml`"
	interfaces="`grep '<if>.*</if>' /cf/conf/config.xml | tr -s '\t' ' ' | sed 's/^.....//' | sed 's/.....$//'`" #display available interfaces
	for intname in $interfaces
	do
		#intnameip="`/sbin/ifconfig $intname | grep 'inet' | sed '/inet6/d' | tr -s '\t' ' ' | sed 's/^......//' | grep -o '^\S*'`" #display the ip for the entered interface
		intnameip="`/usr/local/pkg/nmap-plugin/interface-information.php -i $intname`"
		/usr/local/bin/nmap -e "$intname" -oX /tmp/Host-Check-$intname.xml -sP "$intnameip"
		cat /tmp/Host-Check-$intname.xml >> /tmp/Host-Check.xml
		rm /tmp/Host-Check-$intname.xml
	done
fi
grep -E -o '<address addr=.([0-9]{1,3}[\.]){3}[0-9]{1,3}' /tmp/Host-Check.xml | sed 's/^...............//' > /tmp/list-of-up-hosts.txt

for ip in `cat /tmp/list-of-up-hosts.txt`
do
	macaddress="`arp -a | grep -E $ip | grep -o -E '([[:xdigit:]]{1,2}:){5}[[:xdigit:]]{1,2}'`"
	mkdir -p /usr/local/pkg/nmap-plugin/hosts/$macaddress
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
done

rm /var/run/nmapscan-arp.pid
/usr/local/bin/php /usr/local/pkg/nmap-plugin/nmap-plugin-encode-data.php
exit
