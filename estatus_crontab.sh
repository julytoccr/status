#!/bin/sh
TMP=/tmp/.estatus_cron
LOG=/var/log/estatus_cake_tasks.log
PRIORIDAD=$1

cd /tmp

echo ------------------------------------->>$LOG
date >>$LOG
SALIDA=`curl -o $TMP https://127.0.0.1/utils/ejecutar_tareas/$PRIORIDAD 2>&1`
if [ $? -eq 7 ]; then
    SALIDA=`wget http://127.0.0.1/utils/ejecutar_tareas/$PRIORIDAD -O $TMP 2>&1`
fi
echo $SALIDA >>$LOG 

ERROR=`cat $TMP | grep  error`;
if [ -n "$ERROR" ]; then
	cat $TMP >&2
fi
