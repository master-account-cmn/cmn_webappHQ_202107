@echo off
set THIS_PATH=%~dp0
cd %THIS_PATH%
cd ..
cd php
start "" php core.php
exit
