<?php
/**
 * SECURITY CLEANUP SCRIPT
 *
 * This script removes malicious files from your server.
 *
 * WARNING: Run this script with caution. It will permanently delete files.
 *
 * Usage: php cleanup_malicious_files.php
 */

// Safety check - only run if explicitly enabled
$ENABLE_DELETION = true; // Set to true to enable deletion

echo "=== SECURITY CLEANUP SCRIPT ===\n\n";

if (! $ENABLE_DELETION) {
    echo "⚠️  DELETION IS DISABLED FOR SAFETY\n";
    echo "To enable deletion, set \$ENABLE_DELETION = true in this script.\n\n";
    echo "This script will show what WOULD be deleted:\n\n";
}

$baseDir = __DIR__;
$deleted = [];
$errors = [];
$totalSize = 0;

// Directories and files to remove
$targets = [
    // Phishing kit
    'public/app/IL' => 'Phishing kit directory',

    // Bot protection system
    'public/bots' => 'Bot protection system',

    // Admin panel
    'public/Happy' => 'Admin panel for bot system',

    // Configuration files
    'public/Exec.ini' => 'Bot system configuration',
    'Exec.ini' => 'Bot system configuration (root)',
];

// Function to get directory size
function getDirSize($dir)
{
    $size = 0;
    if (is_dir($dir)) {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
    }

    return $size;
}

// Function to delete directory recursively
function deleteDirectory($dir)
{
    if (! is_dir($dir)) {
        return false;
    }

    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $path = $dir.DIRECTORY_SEPARATOR.$file;
        if (is_dir($path)) {
            deleteDirectory($path);
        } else {
            unlink($path);
        }
    }

    return rmdir($dir);
}

// Process each target
foreach ($targets as $target => $description) {
    $fullPath = $baseDir.DIRECTORY_SEPARATOR.$target;

    if (file_exists($fullPath)) {
        $size = is_dir($fullPath) ? getDirSize($fullPath) : filesize($fullPath);
        $totalSize += $size;

        echo "Found: $target ($description)\n";
        echo '  Size: '.number_format($size / 1024, 2)." KB\n";

        if ($ENABLE_DELETION) {
            try {
                if (is_dir($fullPath)) {
                    if (deleteDirectory($fullPath)) {
                        $deleted[] = $target;
                        echo "  ✓ Deleted successfully\n";
                    } else {
                        $errors[] = "Failed to delete directory: $target";
                        echo "  ✗ Failed to delete\n";
                    }
                } else {
                    if (unlink($fullPath)) {
                        $deleted[] = $target;
                        echo "  ✓ Deleted successfully\n";
                    } else {
                        $errors[] = "Failed to delete file: $target";
                        echo "  ✗ Failed to delete\n";
                    }
                }
            } catch (Exception $e) {
                $errors[] = "Error deleting $target: ".$e->getMessage();
                echo '  ✗ Error: '.$e->getMessage()."\n";
            }
        } else {
            echo "  [Would be deleted]\n";
        }
        echo "\n";
    } else {
        echo "Not found: $target\n\n";
    }
}

// Summary
echo "=== SUMMARY ===\n";
echo 'Total size: '.number_format($totalSize / 1024 / 1024, 2)." MB\n";

if ($ENABLE_DELETION) {
    echo 'Files/Directories deleted: '.count($deleted)."\n";
    if (count($errors) > 0) {
        echo 'Errors: '.count($errors)."\n";
        foreach ($errors as $error) {
            echo "  - $error\n";
        }
    }

    if (count($deleted) > 0) {
        echo "\n✓ Cleanup completed!\n";
        echo "\n⚠️  IMPORTANT NEXT STEPS:\n";
        echo "1. Revoke Telegram bot token: 7379596250:AAGFP0OSotWJK9U9McBEjnRn51M5JJJf0AY\n";
        echo "2. Change all server passwords\n";
        echo "3. Review server logs for unauthorized access\n";
        echo "4. Check for other backdoors\n";
        echo "5. Review SECURITY_CLEANUP.md for complete checklist\n";
    }
} else {
    echo "\n⚠️  This was a DRY RUN. No files were actually deleted.\n";
    echo "To actually delete files, set \$ENABLE_DELETION = true and run again.\n";
}

echo "\n";

