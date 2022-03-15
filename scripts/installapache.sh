#!/bin/bash

if ! pidof apache2 > /dev/null
then
        if ! test -f "$FILE"; then
                sudo apt install apache2
        fi
else echo 'Server already intalled and running'
fi
