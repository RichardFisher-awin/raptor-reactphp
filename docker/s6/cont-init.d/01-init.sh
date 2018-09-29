#!/bin/sh

mkdir -p /srv/log/react/
touch /srv/log/react/error.log
touch /srv/log/react/access.log
touch /srv/log/react/s6init.log

chmod 777 -R /srv/log/react/
