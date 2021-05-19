#!/bin/bash -e	 

if [ $1 == 'up' ] || [ $1 == 'stop' ];then
	DCMP=docker-compose

	CMD=$1
	shift;
	$DCMP $CMD "$@"
	exit;
fi;

if [ $1 == 'exec' ];then
	ARG=$1
	shift;
	docker $ARG -it "$@" bash
fi;

if [ $1 == 'start' ]; then
/bin/bash $(pwd)/system.sh up 
until [ "`docker inspect -f {{.State.Health.Status}} api`"=="starting" ]; do
    sleep 0.1;
done;

sleep 10;

bin/console composer install 

fi;