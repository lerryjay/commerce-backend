#!/bin/bash

if ! pidof apache2 > /dev/null
then
   sudo systemctl start apache2
        
else echo 'Server running'
fi

