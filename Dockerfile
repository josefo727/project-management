FROM webdevops/php-nginx:8.3-alpine

ENV WEB_DOCUMENT_ROOT=/app/public
ENV PHP_DISMOD=bz2,calendar,exiif,ffi,intl,gettext,ldap,mysqli,imap,pdo_pgsql,pgsql,soap,sockets,sysvmsg,sysvsm,sysvshm,shmop,xsl,apcu,vips,yaml,imagick,mongodb,amqp

WORKDIR /app

RUN sed -i 's/v3\.21/v3.20/g' /etc/apk/repositories && \
    apk update && \
    apk add --update nodejs npm

RUN apk add --no-cache supervisor

COPY docker/supervisord.conf /opt/docker/etc/supervisor.d/system.conf

RUN chown -R application:application .

EXPOSE 80
