FROM zanox/mysql
MAINTAINER Muhammad Rahman <muhammad.rahman@affiliatewindow.com>

ADD schema /schema
RUN start-mysql && mysql -e "CREATE DATABASE reacttest" && stop-mysql
RUN start-mysql && cat /schema/reacttest/*.sql | mysql reacttest && stop-mysql
RUN start-mysql && echo "SHOW DATABASES;" | mysql && stop-mysql
RUN start-mysql && echo "USE reacttest; SHOW TABLES;" | mysql && stop-mysql
RUN start-mysql && \
    echo "CREATE USER 'reacttest_ro'@'%'; GRANT SELECT ON *.* TO 'reacttest_ro'@'%' IDENTIFIED BY 'passwordRo' ;" | mysql && \
    echo "CREATE USER 'reacttest_rw'@'%'; GRANT ALL ON *.* TO 'reacttest_rw'@'%' IDENTIFIED BY 'passwordRw' ;" | mysql && \
    stop-mysql
