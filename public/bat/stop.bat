@echo off
cd /d %~dp0

set STOP=taskkill /im vlc.exe /f

start /I cmd /c %STOP%

exit /b 200
