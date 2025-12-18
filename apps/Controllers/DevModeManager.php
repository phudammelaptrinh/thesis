<?php
/**
 * DevModeManager - Quản lý chế độ Developer Mode
 * 
 * Script này giúp bật/tắt Dev Mode cho upload folder
 * Chạy từ command line hoặc tạo endpoint riêng
 */

require_once __DIR__ . '/cUpload.php';

class DevModeManager
{
    private $uploadCtrl;

    public function __construct()
    {
        $this->uploadCtrl = new cUpload();
    }

    /**
     * Hiển thị menu CLI
     */
    public function showMenu()
    {
        echo "\n";
        echo "╔════════════════════════════════════════╗\n";
        echo "║      DEV MODE MANAGER - TaskBB         ║\n";
        echo "╚════════════════════════════════════════╝\n";
        echo "\n";

        $currentStatus = $this->uploadCtrl->isDevModeEnabled() ? '🔴 ENABLED (INSECURE)' : '🟢 DISABLED (SECURE)';
        echo "Current Status: $currentStatus\n";
        echo "\n";
        echo "Options:\n";
        echo "  [1] Enable Dev Mode (Allow all uploads, no scan)\n";
        echo "  [2] Disable Dev Mode (Restore security)\n";
        echo "  [3] Check Status\n";
        echo "  [4] Exit\n";
        echo "\n";
    }

    /**
     * Bật Dev Mode
     */
    public function enable()
    {
        echo "\n⚠️  WARNING: Enabling Dev Mode will:\n";
        echo "   - Allow uploading ANY file type (including PHP)\n";
        echo "   - Disable malware/webshell scanning\n";
        echo "   - Allow PHP execution in uploads folder\n";
        echo "\nAre you sure? (yes/no): ";

        $handle = fopen("php://stdin", "r");
        $line = trim(fgets($handle));
        fclose($handle);

        if (strtolower($line) !== 'yes') {
            echo "❌ Cancelled.\n";
            return;
        }

        $result = $this->uploadCtrl->enableDevMode();

        if ($result['success']) {
            echo "\n✅ " . $result['message'] . "\n";
            echo "📁 Upload folder: Views/uploads/\n";
            echo "⚠️  Remember to disable Dev Mode when done!\n";
        } else {
            echo "\n❌ Error: " . $result['message'] . "\n";
        }
    }

    /**
     * Tắt Dev Mode
     */
    public function disable()
    {
        echo "\n🔒 Restoring security settings...\n";

        $result = $this->uploadCtrl->disableDevMode();

        if ($result['success']) {
            echo "✅ " . $result['message'] . "\n";
            echo "🔒 Production security restored.\n";
        } else {
            echo "❌ Error: " . $result['message'] . "\n";
        }
    }

    /**
     * Kiểm tra trạng thái
     */
    public function checkStatus()
    {
        $isDevMode = $this->uploadCtrl->isDevModeEnabled();

        echo "\n";
        echo "═══════════════════════════════════════\n";
        echo "STATUS REPORT\n";
        echo "═══════════════════════════════════════\n";

        if ($isDevMode) {
            echo "Mode: 🔴 DEV MODE ENABLED\n";
            echo "Security: ⚠️  DISABLED (INSECURE)\n";
            echo "Upload Restrictions: ❌ None\n";
            echo "Malware Scan: ❌ Disabled\n";
            echo "PHP Execution: ✅ Allowed\n";
            echo "\n";
            echo "⚠️  WARNING: This is a DANGEROUS configuration!\n";
            echo "   Only use for local development.\n";
            echo "   NEVER deploy to production with Dev Mode enabled.\n";
        } else {
            echo "Mode: 🟢 PRODUCTION MODE\n";
            echo "Security: ✅ ENABLED (SECURE)\n";
            echo "Upload Restrictions: ✅ Active\n";
            echo "Malware Scan: ✅ Enabled\n";
            echo "PHP Execution: ❌ Blocked\n";
            echo "\n";
            echo "✅ Secure configuration active.\n";
        }

        echo "═══════════════════════════════════════\n";
        echo "\n";
    }

    /**
     * Chạy CLI interactive
     */
    public function run()
    {
        while (true) {
            $this->showMenu();
            echo "Select option: ";

            $handle = fopen("php://stdin", "r");
            $choice = trim(fgets($handle));
            fclose($handle);

            switch ($choice) {
                case '1':
                    $this->enable();
                    break;
                case '2':
                    $this->disable();
                    break;
                case '3':
                    $this->checkStatus();
                    break;
                case '4':
                    echo "\nGoodbye!\n";
                    exit(0);
                default:
                    echo "\n❌ Invalid option. Please choose 1-4.\n";
            }

            echo "\nPress Enter to continue...";
            fgets(fopen("php://stdin", "r"));
        }
    }
}

// ==========================================
// CLI Usage
// ==========================================
// Chạy script này từ command line:
// php DevModeManager.php
// 
// Hoặc với tham số:
// php DevModeManager.php enable
// php DevModeManager.php disable
// php DevModeManager.php status
// ==========================================

if (php_sapi_name() === 'cli') {
    $manager = new DevModeManager();

    if (isset($argv[1])) {
        $command = strtolower($argv[1]);

        switch ($command) {
            case 'enable':
            case 'on':
                $uploadCtrl = new cUpload();
                $result = $uploadCtrl->enableDevMode();
                echo $result['message'] . "\n";
                exit($result['success'] ? 0 : 1);

            case 'disable':
            case 'off':
                $uploadCtrl = new cUpload();
                $result = $uploadCtrl->disableDevMode();
                echo "Usage: php DevModeManager.php [enable|disable|status]\n";
                exit(1);
        }
    } else {
        // Interactive mode
        $manager->run();
    }
}
