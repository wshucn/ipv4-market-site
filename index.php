<?php
header('Content-Type: text/html; charset=utf-8');

$storage_path = '/var/www/html/storage';
$efs_status = is_dir($storage_path) ? 'exist' : 'not-exist';
$efs_writable = is_writable($storage_path) ? 'writable' : 'readonly';
$test_file = $storage_path . '/test_' . date('YmdHis') . '.txt';
$write_test = false;

try {
    $write_test = file_put_contents($test_file, 'EFS test - ' . date('Y-m-d H:i:s'));
    $read_test = file_get_contents($test_file);
} catch (Exception $e) {
    $error_message = $e->getMessage();
}

$files = [];
if (is_dir($storage_path)) {
    $files_list = scandir($storage_path);
    $files = array_diff($files_list, ['.', '..']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple PHP</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #333;
        }
        .debug-info {
            background-color: #e9f7fe;
            border: 1px solid #ccc;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>PHP Service Is Running</h1>
        <p>Curernt Time: <?php echo date('Y-m-d H:i:s'); ?></p>
        <p>Server Info: <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
        <p>PHP Version: <?php echo phpversion(); ?></p>
        
        <div class="debug-info">
            <h2>EFS Mount Debug</h2>
            <p>Dir (<?php echo $storage_path; ?>): <strong><?php echo $efs_status; ?></strong></p>
            <p>Permission: <strong><?php echo $efs_writable; ?></strong></p>
            <p>Write Test: 
                <?php if($write_test !== false): ?>
                <span class="success">Success (<?php echo $write_test; ?> Bytes)</span>
                <?php else: ?>
                <span class="error">Failure<?php echo isset($error_message) ? ' - ' . $error_message : ''; ?></span>
                <?php endif; ?>
            </p>
            <?php if(count($files) > 0): ?>
            <p>List:</p>
            <ul>
                <?php foreach($files as $file): ?>
                <li><?php echo htmlspecialchars($file); ?> - <?php echo date('Y-m-d H:i:s', filemtime($storage_path . '/' . $file)); ?></li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <p>Empty Directory</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>