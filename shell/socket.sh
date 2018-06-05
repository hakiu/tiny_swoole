#!/bin/bash
# start, stop, status, restart, reload server
# Usage: sh socket.sh {start|stop|restart|status|reload}
# TO-DO: 第二次 restart

PHP=`which php`
TIME=`date "+%Y-%m-%d %H:%M:%S"`
SOCKET_FILE=/usr/www/tiny_swoole/tinySwoole.php
LOG_FILE=/tmp/swoole.log
PID_FILE=/tmp/swoole.pid

#定义颜色的变量
RED_COLOR='\E[1;31m'   #红
GREEN_COLOR='\E[1;32m' #绿
YELOW_COLOR='\E[1;33m' #黄
BLUE_COLOR='\E[1;34m'  #蓝
PINK='\E[1;35m'        #粉红
RES='\E[0m'

start() {
    SWOOLE_MASTER_PID=`cat $PID_FILE`
    if [ $SWOOLE_MASTER_PID ]; then
        echo 'Server is running ......' && exit 0
    else
        echo 'Starting ......'
        $PHP $SOCKET_FILE &
        sleep 1
        NEW_SWOOLE_MASTER_PID=`cat $PID_FILE`
        if [ $NEW_SWOOLE_MASTER_PID ]; then
        	MSG="${GREEN_COLOR}Server start success${RES}"
        else
        	MSG="${RED_COLOR}Server start fail${RES}"
        fi
    fi

    echo -e $MSG && echo $TIME $MSG >> $LOG_FILE
}

stop() {
    echo 'Stopping ......'
    ### DO NOT USE KILL -9, GIVE THE MASTER CHANCE TO DO SOMETHING ###
    SWOOLE_MASTER_PID=`cat $PID_FILE`
    kill -15 $SWOOLE_MASTER_PID
    NEW_SWOOLE_MASTER_PID=`ps -ef | grep Tiny_Swoole | grep -v "grep" | sed -n '1p' | awk -F ' ' '{print $2}'`
    if [ $NEW_SWOOLE_MASTER_PID ]; then
        # Ultimate weapon
        PID_TO_KILL=`ps -ef | grep Tiny_Swoole | grep -v "grep"  | awk -F ' ' '{print $2}'`
        for pid in $PID_TO_KILL; 
        do 
            kill -9 $pid; 
        done
        # kill -9 -$SWOOLE_MASTER_PID => 利用 KILL -N 的灭团模式
    	MSG="${RED_COLOR}Server stop fail !!!${RES}"
    fi

    > $PID_FILE
    MSG="${GREEN_COLOR}Server stop success${RES}"
    echo -e $MSG && echo $TIME $MSG >> $LOG_FILE
}

restart() {
    stop
    sleep 1
    start
}

reload() {
    SWOOLE_MASTER_PID=`cat $PID_FILE`
    MSG=' Reloading... '
    kill -USR1 $SWOOLE_MASTER_PID
    echo $MSG && echo $TIME $MSG >> $LOG_FILE
}

status() {
    SWOOLE_MASTER_PID=`cat $PID_FILE`
    if [ $SWOOLE_MASTER_PID ]; then
    	MSG="${GREEN_COLOR}Server is running${RES}"
    else
    	MSG="${RED_COLOR}Server is DOWN !!!${RES}"
    fi

    echo -e $MSG && echo $TIME $MSG >> $LOG_FILE
}

case "$1" in
    start)
        start
        ;;
    stop)
        stop
        ;;
    restart)
        restart
        ;;
    status)
        status
        ;;
    reload)
        reload
        ;;
    *)
        echo "Usage: $0 {start|stop|restart|status|reload}"
        ;;
esac
exit 0