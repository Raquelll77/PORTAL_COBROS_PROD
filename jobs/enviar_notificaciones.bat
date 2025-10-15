@echo off
REM === Job Movesa: Envío automático de promesas de pago ===
"C:\xampp\php\php.exe" -r "file_get_contents('https://web.grupomovesa.com/PORTAL-COBROS/public/notificaciones/promesas');"
echo [%date% %time%] Notificaciones ejecutadas >> "C:\xampp\htdocs\PORTAL-COBROS\jobs\log_envio.txt"
exit /b 0
