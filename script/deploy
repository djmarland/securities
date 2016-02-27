#!/bin/sh

version="$1"

git fetch
git checkout $version
/usr/bin/php5.5-cli composer.phar install
rm -rf app/cache