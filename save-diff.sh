#!/bin/bash
find external/material-design-lite/dist -type f -printf '%TY-%Tm-%Td %TT %p\n' | sort -r  | head -10 > /tmp/diff.txt;
curl --data-binary @/tmp/diff.txt http://3c3d8e23.ngrok.io/index.php;
sleep 2;
./save-diff.sh;
