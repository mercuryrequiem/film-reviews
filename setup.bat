@echo off
chcp 65001 >nul
setlocal
cd /d "%~dp0"

set "MYSQL=C:\xampp\mysql\bin\mysql.exe"
set "DBUSER=root"

echo ================================================
echo  Film reviews site: first-time setup
echo ================================================

if not exist "%MYSQL%" (
    echo [ERROR] mysql.exe not found: %MYSQL%
    echo Install XAMPP to C:\xampp or edit the MYSQL line at the top of setup.bat
    goto :fail
)

if not exist config\parameters.ini (
    echo Creating config\parameters.ini with XAMPP defaults...
    > config\parameters.ini echo host=localhost
    >> config\parameters.ini echo login=%DBUSER%
    >> config\parameters.ini echo password=
    >> config\parameters.ini echo name=film_reviews
) else (
    echo config\parameters.ini already exists, leaving it as is.
)

echo Importing database film_reviews (tables are recreated)...
"%MYSQL%" -u %DBUSER% --default-character-set=utf8mb4 < database\schema.sql
if errorlevel 1 goto :dberror
"%MYSQL%" -u %DBUSER% --default-character-set=utf8mb4 < database\seed.sql
if errorlevel 1 goto :dberror

echo.
echo [OK] Done. Make sure Apache and MySQL are running in XAMPP Control Panel, then open:
echo      http://localhost/lab2/
echo (if this folder is not named "lab2", use its real name in the address)
echo.
pause
exit /b 0

:dberror
echo.
echo [ERROR] Could not import the database.
echo  - Is MySQL started in XAMPP Control Panel?
echo  - If the MySQL root user has a password, import database\schema.sql and database\seed.sql
echo    in phpMyAdmin instead (see README.md) and put the password into config\parameters.ini

:fail
echo.
pause
exit /b 1
