#!/bin/sh
cron_start() {
	[ `/bin/pgrep -f 'cron -s' | wc -l` -eq 0 ] && /usr/sbin/cron -s &
}

cron_stop() {
	[ -f "/var/run/cron.pid" ] && kill -9 `cat /var/run/cron.pid`; rm -f /var/run/cron.pid; /bin/pkill -f 'cron -s'
}

case $1 in
	a)
		cron_stop
		cron_start
		;;
	b)
		cron_stop
		;;
esac

