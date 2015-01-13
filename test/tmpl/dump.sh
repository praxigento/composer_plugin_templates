#!/bin/sh
##
#       Dump Magento DB and create archive (tar.gz)
##
DB_NAME=${MYSQL_DBNAME}
DB_USER=${MYSQL_USER}
DB_PASS=${MYSQL_PASSWORD}

DB_DUMP=mage_db_dump.sql

echo "Dumping Magento db '$DB_NAME' into '$DB_DUMP'..."
mysqldump --user=$DB_USER --password=$DB_PASS $DB_NAME > $DB_DUMP
tar -zcf $DB_DUMP.tar.gz $DB_DUMP

echo "DB '$DB_NAME' is dumped."