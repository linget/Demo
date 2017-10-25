@echo off
set var=1
:continue
echo item%var%
ping 127.0.0.1 -n 900 >nul
call make.bat
set /a var+=1
if %var% gtr 0 goto continue
echo end
pause