#!/bin/sh

if [ "$#" -ne 2 ]
then
  echo "Usage: release [subdomain] [version]"
  exit 1
fi

subdomain="$1"
version="$2"

cd ../$subdomain.isinanalytics.com
pwd

sudo git fetch
sudo git checkout $version
sudo composer install
sudo rm -rf app/cache/*
sudo cp nginx/$subdomain.isinanalytics.com.conf /etc/nginx/sites-available/$subdomain.isinanalytics.com.conf
sudo systemctl reload nginx
