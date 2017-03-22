<?php
require('vendor/autoload.php');
// this will simply read AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY from env vars
$bucket = getenv('S3_BUCKET')?: die('No "S3_BUCKET" config var found in env!');
$s3_endpoint = getenv('S3_ENDPOINT')?: die('No "S3_ENDPOINT" config var found in env!');
$access_key = getenv('S3_KEY')?: die('No "S3_KEY" config var found in env!');
$access_secret = getenv('S3_SECRET')?: die('No "S3_SECRET" config var found in env!');
$s3 = Aws\S3\S3Client::factory(array('key'=>$access_key,'secret'=>$access_secret,'base_url'=>$s3_endpoint));
?>
<html>
    <head><meta charset="UTF-8"></head>
    <body>
        <h1>S3 upload example</h1>
<?php
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['userfile']) && $_FILES['userfile']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['userfile']['tmp_name'])) {
    // FIXME: add more validation, e.g. using ext/fileinfo
    try {
        // FIXME: do not use 'name' for upload (that's the original filename from the user's computer)
        $upload = $s3->upload($bucket, $_FILES['userfile']['name'], fopen($_FILES['userfile']['tmp_name'], 'rb'), 'public-read');
?>
        <p>Upload <a href="<?=htmlspecialchars($upload->get('ObjectURL'))?>">successful</a> :)</p>
<?php } catch(Exception $e) { ?>
        <p>Upload error :(</p>
<?php } } ?>
        <h2>Upload a file</h2>
        <form enctype="multipart/form-data" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
            <input name="userfile" type="file"><input type="submit" value="Upload">
        </form>
    </body>
</html>
