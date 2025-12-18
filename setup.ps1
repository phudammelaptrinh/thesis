# TaskBB Setup Script for Windows
# Chạy PowerShell với quyền Administrator

Write-Host "=== TaskBB Setup Script ===" -ForegroundColor Green

# 1. Set quyền cho thư mục uploads
Write-Host "`n1. Setting permissions for upload folders..." -ForegroundColor Yellow

$uploadFolders = @(
    "public\uploads",
    "public\uploads\avatars",
    "public\uploads\reports",
    "public\uploads\tasks",
    "Views\uploads"
)

foreach ($folder in $uploadFolders) {
    if (Test-Path $folder) {
        icacls $folder /grant Everyone:F /T
        Write-Host "  ✓ $folder" -ForegroundColor Green
    } else {
        Write-Host "  ✗ $folder (not found)" -ForegroundColor Red
    }
}

# 2. Kiểm tra file .env
Write-Host "`n2. Checking .env file..." -ForegroundColor Yellow
if (!(Test-Path ".env")) {
    if (Test-Path ".env.example") {
        Copy-Item ".env.example" ".env"
        Write-Host "  ✓ Created .env from .env.example" -ForegroundColor Green
    } else {
        Write-Host "  ✗ .env.example not found!" -ForegroundColor Red
    }
} else {
    Write-Host "  ✓ .env already exists" -ForegroundColor Green
}

# 3. Kiểm tra config
Write-Host "`n3. Checking configuration..." -ForegroundColor Yellow
Write-Host "  BASE_PATH: $PWD" -ForegroundColor Cyan

# 4. Hướng dẫn tiếp theo
Write-Host "`n=== Next Steps ===" -ForegroundColor Green
Write-Host "1. Edit .env file and configure database"
Write-Host "2. Import database: mysql -u root -p taskbb1 < taskbb1_latest.sql"
Write-Host "3. Access: http://localhost:88/taskbb"
Write-Host ""
