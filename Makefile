# $FreeBSD$

PORTNAME= pfSense-pkg-NMap-Plugin
PORTVERSION= 0.99
CATEGORIES= sysutils
MASTER_SITES= # empty
DISTFILES= # empty
EXTRACT_ONLY= # empty
MAINTAINER= shean.d.reiley@gmail.com
COMMENT= pfSense package NMap Plugin
RUN_DEPENDS= ${LOCALBASE}/bin/nmap:${PORTSDIR}/security/nmap
NO_BUILD= yes
NO_MTREE= yes
SUB_FILES= pkg-install pkg-deinstall
SUB_LIST= PORTNAME=${PORTNAME}

do-extract:
${MKDIR} ${WRKSRC}
do-install:
${MKDIR} ${STAGEDIR}${PREFIX}/pkg
${MKDIR} ${STAGEDIR}/etc/inc/priv
${MKDIR} ${STAGEDIR}${DATADIR}
	${INSTALL_DATA} -m 0644 ${FILESDIR}${PREFIX}/pkg/nmap-plugin.xml \
${STAGEDIR}${PREFIX}/pkg
	${INSTALL_DATA} ${FILESDIR}${PREFIX}/pkg/nmap-plugin.inc \
${STAGEDIR}${PREFIX}/pkg
	${INSTALL_DATA} ${FILESDIR}/etc/inc/priv/nmap-plugin.priv.inc \
${STAGEDIR}/etc/inc/priv
	${INSTALL_DATA} ${FILESDIR}${DATADIR}/info.xml \
${STAGEDIR}${DATADIR}
	${INSTALL_DATA} ${FILESDIR}${PREFIX}/pkg/nmap-plugin/nmap-scan-dhcp.sh \
${STAGEDIR}${PREFIX}/pkg/nmap-plugin
	${INSTALL_DATA} ${FILESDIR}${PREFIX}/pkg/nmap-plugin/nmap-scan-arp.sh \
${STAGEDIR}${PREFIX}/pkg/nmap-plugin
	${INSTALL_DATA} ${FILESDIR}${PREFIX}/pkg/nmap-plugin/interface-information.php \
${STAGEDIR}${PREFIX}/pkg/nmap-plugin
	${INSTALL_DATA} ${FILESDIR}${PREFIX}/pkg/nmap-plugin/nmap-plugin-encode-data.php \
${STAGEDIR}${PREFIX}/pkg/nmap-plugin
	${INSTALL_DATA} ${FILESDIR}${PREFIX}/pkg/nmap-plugin/nmap-plugin-decode-data.php \
${STAGEDIR}${PREFIX}/pkg/nmap-plugin
	${INSTALL_DATA} ${FILESDIR}${PREFIX}/pkg/nmap-plugin/auto-nmap-scans.php \
${STAGEDIR}${PREFIX}/pkg/nmap-plugin
	${INSTALL_DATA} ${FILESDIR}${PREFIX}/pkg/nmap-plugin/auto-nmap-scans.sh \
${STAGEDIR}${PREFIX}/pkg/nmap-plugin
	${INSTALL_DATA} ${FILESDIR}${PREFIX}/www/nmap-plugin/nmap-plugin-schedule-scan-times.php \
${STAGEDIR}${PREFIX}/www/nmap-plugin
	${INSTALL_DATA} ${FILESDIR}${PREFIX}/www/nmap-plugin/nmap-plugin-email-setup.php \
${STAGEDIR}${PREFIX}/www/nmap-plugin
	${INSTALL_DATA} ${FILESDIR}${PREFIX}/www/nmap-plugin/nmap-plugin-auto-scans.php \
${STAGEDIR}${PREFIX}/www/nmap-plugin
	${INSTALL_DATA} ${FILESDIR}${PREFIX}/www/nmap-plugin/nmap-plugin-nmap-scan.php \
${STAGEDIR}${PREFIX}/www/nmap-plugin
	${INSTALL_DATA} ${FILESDIR}${PREFIX}/www/nmap-plugin/nmap-plugin-scan-results.php \
${STAGEDIR}${PREFIX}/www/nmap-plugin
	${INSTALL_DATA} ${FILESDIR}${PREFIX}/www/nmap-plugin/nmap-plugin-view-results.php \
${STAGEDIR}${PREFIX}/www/nmap-plugin

.include <bsd.port.mk>
