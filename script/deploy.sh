#!/bin/sh

version="$1"

git fetch
git checkout $version
composer install
rm -rf app/cache/*