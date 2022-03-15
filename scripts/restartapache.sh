
#!/bin/bash

if pidof apache2 > /dev/null
then
   sudo systemctl restart apache2     
else  sudo systemctl start apache2
fi
