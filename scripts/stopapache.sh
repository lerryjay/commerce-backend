#!/bin/bash

if !pidof apache2 > /dev/null
then
  echo 'Server had already been stopped or was not running!'   
else  sudo systemctl stop apache2
fi
