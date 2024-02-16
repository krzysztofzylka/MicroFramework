# MicroFramework example docker

FROM ubuntu:23.10

# Config
ARG DEBIAN_FRONTEND=noninteractive

ENV WEBROOT_PATH=/var/www
ENV APACHE_LOG_DIR=/var/log/apache2
ENV APACHE_SSL=off

LABEL org.opencontainers.image.authors="krzysztofzylka@yahoo.com"

WORKDIR ${WEBROOT_PATH}

# Package
RUN apt update
RUN apt -y install software-properties-common iproute2 ca-certificates apt-transport-https

# Apache2
RUN apt -y install apache2 libapache2-mod-fcgid composer && \
	a2enmod rewrite actions fcgid alias proxy_fcgi ssl && \
	echo "ServerName localhost" >> /etc/apache2/apache2.conf

# PHP
RUN apt -y install php8.2 php8.2-fpm php8.2-mysql php8.2-curl php8.2-gd \
    php8.2-intl php8.2-mbstring php8.2-xml php8.2-zip php8.2-bcmath  \
    php8.2-soap php8.2-xdebug php8.2-dev php8.2-cli php8.2-common \
    php8.2-ssh2 php8.2-opcache php8.2-readline php8.2-sqlite3 php8.2-xsl \
    php8.2-imagick php8.2-memcached php8.2-redis php8.2-mongodb php8.2-yaml \
    php8.2-igbinary php8.2-msgpack php8.2-bz2 php8.2-ldap

ADD src/start.sh /start.sh
RUN chmod 0755 /start.sh
COPY src/000-default.conf /etc/apache2/sites-available/000-default.conf

RUN ln -sf /dev/stdout /var/log/apache2/access.log && ln -sf /dev/stderr /var/log/apache2/error.log

# Ports
EXPOSE 80

# Entrypoint
ENTRYPOINT ["bash", "/start.sh"]