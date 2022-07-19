<?php
define('PASSWORD', 'your-password');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>File Upload</title>
<style type="text/css">
a { text-decoration: none; color: #00a; }
a:hover { color: #055; }
</style>
<script type="text/javascript">
function select_all(b) {
    document.querySelectorAll("input[type=checkbox]").forEach((checkbox) => { checkbox.checked = b; });
}
</script>
</head>
<body>
<?php

$fileCount = isset($_FILES["file"]) ? count($_FILES["file"]["name"]) : 0;
 
$phpFileUploadErrors = array(
    0 => 'There is no error, the file uploaded with success',
    1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
    2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
    3 => 'The uploaded file was only partially uploaded',
    4 => 'No file was uploaded',
    6 => 'Missing a temporary folder',
    7 => 'Failed to write file to disk.',
    8 => 'A PHP extension stopped the file upload.',
);

if ($fileCount > 0) {

    if (!isset($_POST["password"]) || $_POST["password"] !== PASSWORD) {

        echo "<p>Invalid password!</p>";
    
    } else {

        for ($i = 0; $i < $fileCount; $i++) {

            $fileName = $_FILES["file"]["name"][$i];
            $fileType = $_FILES["file"]["type"][$i];
            $fileTmpName = $_FILES["file"]["tmp_name"][$i];
            $fileError = $_FILES["file"]["error"][$i];
            $fileSize = $_FILES["file"]["size"][$i];

            if ($fileError == 0) {
                if (move_uploaded_file($fileTmpName, __DIR__ . "/../files/$fileName")) {
                    echo "<p>Uploaded: <a href=\"/files/$fileName\">$fileName</a> (Size: $fileSize)</p>";
                } else {
                    echo "File upload error!";
                }
            } else if ($fileError == UPLOAD_ERR_NO_FILE) {
                echo "No files selected!";
            } else {
                echo "File error:<br />$phpFileUploadErrors[$fileError]";
            }
        }
    
        echo "<p><a href=\"/upload\">Go Back</a></p>";

    }

} else if (isset($_POST["delete"]) && isset($_POST["file"])) {

    if (!isset($_POST["password"]) || $_POST["password"] !== PASSWORD) {

        echo "<p>Invalid password!</p>";
    
    } else {

        foreach ($_POST["file"] as $fileName) {
            if (file_exists(__DIR__ . "/../files/$fileName")) {
                
                if (unlink(__DIR__ . "/../files/$fileName")) {
                    echo "<p>Deleted: $fileName</p>";
                } else {
                    echo "<p>Delete error (!): $fileName</p>";
                }
        
            } else {
                echo "<p>File not found (!): $fileName</p>";
            }
        }

        echo "<a href=\"/upload\">Go Back</a>";
    }

} else { ?>
<form method="post" enctype="multipart/form-data">
<h3><a href="/upload">File Upload</a></h3>
<p><input type="file" name="file[]" multiple /></p>
<p><input type="password" name="password" placeholder="Password" />
<input type="submit" name="upload" value="Upload" /></p>
</form>
<?php } ?>
<h3>Files</h3>
<form method="POST">
<?php
$folder = opendir(__DIR__. "/../files");
$fileCount = 0;
while ($fileName = readdir($folder)) {
	if (substr($fileName, 0, 1) == "." || $fileName == "index.php") continue;
    $fileCount++;
    echo "<p><input type=\"checkbox\" name=\"file[]\" value=\"" . urlencode($fileName) . "\" />";
	echo "<a target=\"_new\" href=\"/files/$fileName\">$fileName</a></p>";
}
if ($fileCount > 0) { ?>
<p><input type="checkbox" onchange="select_all(this.checked)" />
<input type="password" name="password" placeholder="Password" />
<input type="submit" name="delete" value="Delete Selected Files" /></p>
<?php } else {
    echo "<p>No files uploaded yet!</p>";
} ?>
</form>
</body>
</html>
