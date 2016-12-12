#!/bin/sh

# Define a timestamp function
timestamp() {
  date +"%s"
}

# do something...
timestamp > app/config/release-time.txt
