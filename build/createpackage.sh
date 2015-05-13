#!/bin/sh
find ./ -type f -name *.zip | xargs rm -f
VERSION=`xml_grep version ../sendactivation/sendactivationmail.xml --text`

rsync -av --progress ../ ./ --exclude build
zip  -r --exclude=*.svn* "plg_users_sendactivationmail_$VERSION.zip" sendactivation

find . \! -name "createpackage.sh" \! -name "plg_users_sendactivationmail_$VERSION.zip" | xargs rm -rf
