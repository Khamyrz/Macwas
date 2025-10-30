Write-Host "Starting Madridejos Community Waterworks System Development Server..." -ForegroundColor Green
Write-Host ""
Write-Host "Clearing all user sessions (logging out all users)..." -ForegroundColor Yellow
php artisan sessions:clear-all
Write-Host ""
Write-Host "Starting development server..." -ForegroundColor Green
php artisan serve

