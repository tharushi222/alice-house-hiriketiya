@echo off
echo ========================================================
echo  Deploying Alice House Hiriketiya to XAMPP htdocs
echo ========================================================
set SOURCE=%~dp0

if exist "C:\xampp_new01\htdocs" (
    set TARGET=C:\xampp_new01\htdocs\alice_house
) else (
    set TARGET=C:\xampp\htdocs\alice_house
)

echo Target Directory: %TARGET%
if not exist "%TARGET%" mkdir "%TARGET%"
xcopy "%SOURCE%*" "%TARGET%\" /E /I /Y /Q

echo.
echo [SUCCESS] Project copied to %TARGET%
echo You can now access: http://localhost/alice_house/
echo Make sure Apache and MySQL are running in your XAMPP Control Panel.
pause
