FROM php:7.2

RUN apt-get -yq update \
    && apt-get install --assume-yes --no-install-recommends \
        sudo tar zip unzip bzip2 vim ssh git rsync ftp wget cron logrotate libssl-dev libicu-dev libpcre3-dev \
        build-essential

WORKDIR /react
RUN chown -R www-data:www-data /react

#cleanup
RUN echo "Y" | apt-get autoremove

# add www-data to the root group to be able to read app3.json
RUN usermod -G root www-data

#install s6
RUN curl -sL "https://github.com/just-containers/s6-overlay/releases/download/v1.21.2.2/s6-overlay-amd64.tar.gz" | tar xz -C /

#we need this for app3.json
RUN mkdir -p /etc/aw
#and we need app3.json
COPY docker/config/app3.json /etc/aw/app3.json

#we need this for logging and the cache directory needs to exist
RUN mkdir -p /srv/log/react \
    && touch /srv/log/react/error.log \
    && touch /srv/log/react/access.log \
    && chmod 777 -R /srv/log/react/

#we need to have at least date.timezone set to Europe/London
COPY docker/config/01-custom.ini /usr/local/etc/php/conf.d/01-custom.ini
COPY docker/config/03-intl.ini /usr/local/etc/php/conf.d/03-intl.ini

# install composer

# the first curl call is here to cause docker to fail when composer website fails
# otherwise the next curl line would seem to have worked as curl would output nothing
# through stdout and php would process it returning error code 0
# so docker would use and cache this layer
# which would then lead to having no composer binary available
RUN mkdir -p /root/.ssh/ \
    && curl -sS https://getcomposer.org/installer > /dev/null \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename composer \
    && ssh-keyscan -H github.com >> /root/.ssh/known_hosts

#copy the files into the root of the application
COPY . ./

#build application dependencies
RUN composer install --no-autoloader && composer clear-cache \
    && composer dumpautoload

#logrotate
COPY docker/config/logrotate.conf /etc/logrotate.d/email-approval-api
RUN rm -rf /etc/logrotate.d/apache2 && chmod 644 /etc/logrotate.d/email-approval-api

COPY docker/s6/services.d /etc/services.d
COPY docker/s6/cont-init.d /etc/cont-init.d

EXPOSE 80

CMD ["/init"]
