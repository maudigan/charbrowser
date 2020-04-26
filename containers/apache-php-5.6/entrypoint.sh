#!/bin/bash

echo "root=root@email.com" >> /etc/ssmtp/ssmtp.conf
echo "mailhub=$MAIL_HUB_SERVER" >> /etc/ssmtp/ssmtp.conf
echo "AuthUser=$MAIL_HUB_USERNAME" >> /etc/ssmtp/ssmtp.conf
echo "AuthPass=$MAIL_HUB_PASSWORD" >> /etc/ssmtp/ssmtp.conf
echo "UseTLS=YES" >> /etc/ssmtp/ssmtp.conf
echo "UseSTARTTLS=YES" >> /etc/ssmtp/ssmtp.conf
echo "FromLineOverride=YES" >> /etc/ssmtp/ssmtp.conf
echo "sendmail_path=sendmail -i -t" >> /usr/local/etc/php/conf.d/php-sendmail.ini
echo "sendmail_from = $MAIL_FROM" >> /usr/local/etc/php/conf.d/php-sendmail.ini
echo "date.timezone = 'America/Chicago'" >> /usr/local/etc/php/conf.d/php-timezone.ini

apache2-foreground
