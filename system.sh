#!/bin/bash -e	 

if [ $1 == 'up' ] || [ $1 == 'down' ];then
	DCMP=docker-compose

	CMD=$1
	shift;
	$DCMP $CMD --remove-orphans "$@"
	exit;
fi;

if [ $1 == 'exec' ];then
	ARG=$1
	shift;
	docker $ARG -it "$@" bash
fi;
