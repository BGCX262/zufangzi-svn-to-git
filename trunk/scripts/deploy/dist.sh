#!/bin/sh

EXPECTED_ARGS=1
REPO=https://zufangzi.googlecode.com/svn/trunk


if [ $# -ne $EXPECTED_ARGS ]
then
	echo "Usage: `basename $0` {local/live}"
	exit 1
fi

if [ "$1" = "local" ]; then
	echo local
else
if [ "$1" = "live" ]; then
	echo Deploy zugefangzi to live
	TMP=`mktemp -d`
	cd $TMP
	echo svn pass hJ3be8Gr3JF6
	/usr/bin/svn export $REPO --username=lhj1982 || exit 1
	echo Login to www.zugefangzi.com stld9Y2rDw
	rsync -vazr --exclude-from='/home/james/projects/zugefangzi/scripts/deploy/rsync_exclude' trunk/ zugefang@www.zugefangzi.com:/home/zugefang/ || exit 3
	echo cp content from public to public_html
	ssh zugefang@www.zugefangzi.com 'cp -r /home/zugefang/public/* /home/zugefang/public_html/'
	echo rm public folder
	ssh zugefang@www.zugefangzi.com 'rm -r /home/zugefang/public/'
	cd ..
	rm -rf $TMP || exit 4 
fi
fi

exit 0