@echo off
echo POS System Disk Cleanup Utility
echo ==============================
echo.

cd c:\xampp\htdocs\pos

echo Clearing Laravel caches...
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan optimize:clear
echo.

echo Cleaning logs...
del /Q storage\logs\laravel*.log
echo.

echo Optimizing database...
php artisan system:maintenance
echo.

echo Cleaning temporary files...
del /Q /S "C:\Windows\Temp\*.*"
echo.

echo Disk cleanup completed!
echo.
pause
