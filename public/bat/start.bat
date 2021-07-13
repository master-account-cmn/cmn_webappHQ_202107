@echo off
set THIS_PATH=%~dp0
set START="C:\Users\cmnse\workspace\Batch\loop_bat\LoopPlayMovieFiles.bat"
set STOP=taskkill /im php.exe /f

start "" %START%
@REM %STOP%
exit /b 200
