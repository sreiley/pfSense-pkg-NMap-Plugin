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

.include <bsd.port.mk>
