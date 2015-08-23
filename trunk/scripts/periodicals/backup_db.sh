#!/bin/bash
FOLDER=/home/zugefang/db_backups/
DB_NAME_PREFIX=zugefang_db_backup
TIMESTAMP=`date +%Y-%m-%d`
DB_NAME=${DB_NAME_PREFIX}-${TIMESTAMP}
mysqldump -u zugefang_user -p zugefang_db > ${FOLDER}${DB_NAME} || exit 1

exit 0
