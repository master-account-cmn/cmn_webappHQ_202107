@echo oN
@rem #### set vlc program path and options ####
set vlc="C:\Program Files\VideoLAN\VLC\vlc.exe"
set vlcoption=-f -L --mouse-hide-timeout=0 --no-video-title-show --image-duration=5
set THIS_PATH=%~dp0

@rem #### play movies with vlc ####
@REM dir /a-d /s /b /oN *.mp4 *.mov *.m4v *.avi *.webm *.PNG *.jpg > filelist.txt
for /f "tokens=*" %%a in (%THIS_PATH%playlist.txt) do call :processline %%a

start "" %vlc% %filelist% %vlcoption%
%ERRORLABEL%
exit
goto :eof

@REM #### append file path function ####
:processline
set filelist=%filelist% "%*"
goto :eof

:eof
