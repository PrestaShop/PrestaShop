#!/bin/sh

#CREATE SF2 PARAMETER FILE
if [ ! -f app/config/parameters.yml ]; then
  cp app/config/parameters.yml.dist app/config/parameters.yml
  echo "The app/config/parameters.yml file was created"
fi