######################################################
# 5.6 apache
######################################################

FROM php:5.6.40-apache

RUN apt-get update && apt-get install -y \
	vim \
	wget \
	git \
	mysql-client \
	libbz2-dev \
	libc-client-dev \
	libcurl4-gnutls-dev \
	libkrb5-dev \
	libmcrypt-dev \
	libpng-dev \
	libgd-dev \
	libreadline-dev \
	libssl-dev \
	imagemagick \
	libxml2-dev \
	libxslt-dev \
	&& rm -rf /var/lib/apt/lists/*

######################################################
# php
######################################################
RUN docker-php-ext-install -j$(nproc) bcmath \
	calendar \
	ctype \
	curl \
	dba \
	dom \
	exif \
	fileinfo \
	ftp \
	gd \
	gettext \
	hash \
	json \
	mbstring \
	mcrypt \
	mysql \
	mysqli \
	opcache \
	pdo \
	pdo_mysql \
	soap \
	sockets \
	xsl \
	zip

RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
        && docker-php-ext-install imap

#############################################
# gd
#############################################

RUN docker-php-ext-configure gd \
        --enable-gd-native-ttf \
        --with-freetype-dir=/usr/include/freetype2 \
        --with-png-dir=/usr/include \
        --with-jpeg-dir=/usr/include \
    && docker-php-ext-install gd

#############################################
# opcache
#############################################

# env var driven ini options
ENV PHP_OPCACHE_FILE_CACHE_ONLY      0
ENV PHP_OPCACHE_FILE_CACHE           "/tmp"
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS  1
ENV PHP_OPCACHE_REVALIDATE_FREQUENCY ${PHP_OPCACHE_REVALIDATE_FREQUENCY:-60}

RUN \
	echo 'opcache.enable                  = 1                                   ' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
	echo 'opcache.enable_cli              = 1                                   ' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
	echo 'opcache.fast_shutdown           = 0                                   ' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
	echo 'opcache.interned_strings_buffer = 64                                  ' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
	echo 'opcache.max_accelerated_files   = 32531                               ' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
	echo 'opcache.memory_consumption      = 512                                 ' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
	echo 'opcache.save_comments           = 1                                   ' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
	echo 'opcache.revalidate_freq         = ${PHP_OPCACHE_REVALIDATE_FREQUENCY} ' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
	echo 'opcache.validate_timestamps     = ${PHP_OPCACHE_VALIDATE_TIMESTAMPS}  ' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
	echo 'opcache.file_cache_only         = ${PHP_OPCACHE_FILE_CACHE_ONLY}      ' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
	echo 'opcache.file_cache              = ${PHP_OPCACHE_FILE_CACHE}           ' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini


#############################################
# config
#############################################

COPY site.conf /etc/apache2/sites-available/000-default.conf

######################################################
# apache modules
######################################################
RUN a2enmod deflate
RUN a2enmod expires
RUN a2enmod file_cache
RUN a2enmod headers
RUN a2enmod rewrite
RUN a2enmod remoteip

#############################################
# finishing touches
#############################################

RUN usermod -u 1000 www-data

#############################################
# mail
#############################################

RUN apt-get update && apt-get install -q -y ssmtp mailutils && rm -rf /var/lib/apt/lists/*

RUN curl -s -o /tmp/go-pear.phar http://pear.php.net/go-pear.phar && \
    echo '/usr/bin/php /tmp/go-pear.phar "$@"' > /usr/bin/pear && \
    chmod +x /usr/bin/pear && \
    pear install mail Net_SMTP && rm -rf /tmp/*

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

CMD entrypoint.sh
