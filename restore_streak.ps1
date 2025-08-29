# GitHub Streak Restoration Script
# This script will create commits for all missing days from August 19th to today

# Get current date
$today = Get-Date
$startDate = Get-Date "2024-08-19"

Write-Host "Starting GitHub streak restoration..." -ForegroundColor Green
Write-Host "From: $($startDate.ToString('yyyy-MM-dd'))" -ForegroundColor Yellow
Write-Host "To: $($today.ToString('yyyy-MM-dd'))" -ForegroundColor Yellow

# Create a temporary file for commits
$tempFile = "streak_commit.txt"

# Loop through each day from August 19th to today
$currentDate = $startDate
$commitCount = 0

while ($currentDate -le $today) {
    $dateStr = $currentDate.ToString('yyyy-MM-dd')
    $dayName = $currentDate.ToString('dddd')
    
    # Create commit message
    $commitMessage = "Update: $dayName $dateStr - Admin panel improvements"
    
    # Create a small change for this date
    $content = "Last updated: $dateStr`nDay: $dayName`nCommit: $commitCount"
    Set-Content -Path $tempFile -Value $content
    
    # Add the file
    git add $tempFile
    
    # Set the commit date to the specific date
    $env:GIT_AUTHOR_DATE = "$dateStr 12:00:00"
    $env:GIT_COMMITTER_DATE = "$dateStr 12:00:00"
    
    # Make the commit
    git commit -m $commitMessage
    
    Write-Host "✓ Committed for $dateStr ($dayName)" -ForegroundColor Green
    
    $currentDate = $currentDate.AddDays(1)
    $commitCount++
}

# Clean up temporary file
Remove-Item $tempFile -ErrorAction SilentlyContinue

Write-Host "`nStreak restoration completed!" -ForegroundColor Green
Write-Host "Total commits made: $commitCount" -ForegroundColor Cyan
Write-Host "`nNext steps:" -ForegroundColor Yellow
Write-Host "1. Run: git push origin main" -ForegroundColor White
Write-Host "2. Check your GitHub profile to see the green squares" -ForegroundColor White
Write-Host "3. Continue making daily commits to maintain your streak" -ForegroundColor White
