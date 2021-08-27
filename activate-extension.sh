#!/bin/sh

cd $(dirname $0)

CURDIR=${PWD##*/}
PUBDIR=../pub
SYSDIR=../sys
CONFIGFILE=${SYSDIR}/config.php
CONFIGFILE_BACKUP=${SYSDIR}/config.bak.php

if [ ! -d "$PUBDIR" ]; then
  echo "Directory not exists, $PUBDIR"
  exit 1
fi

if [ ! -d "$SYSDIR" ]; then
  echo "Directory not exists, $SYSDIR"
  exit 1
fi

if [ ! -f "$CONFIGFILE" ]; then
  echo "File not exists, $CONFIGFILE"
  exit 1
fi

echo "PUB: ${PUBDIR}"
echo "SYS: ${SYSDIR}"
echo "CONFIG: ${CONFIGFILE}"

cp $CONFIGFILE $CONFIGFILE_BACKUP
grep -v "$CURDIR" $CONFIGFILE_BACKUP > $CONFIGFILE
echo "require_once __DIR__ . '/../$CURDIR/extension.php';" >> $CONFIGFILE


cd $PUBDIR
ln -sf ../${CURDIR}/cui cui

cp ../${CURDIR}/demo/_demo.txt .
cp ../${CURDIR}/demo/demo.php .

echo "Completed"
