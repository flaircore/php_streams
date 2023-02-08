<?php
// Autoload dependencies installed via composer .
require_once __DIR__ . '/vendor/autoload.php';

use Aws\Exception\MultipartUploadException;
use Aws\S3\MultipartUploader;
use Symfony\Component\Dotenv\Dotenv;

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use Nick\PhpStreams\AwsS3Client;


// Load env custom variables.
$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$upload_dir = __DIR__ . '/uploads/';

// Log channel (local_dev), to track all logs/errors.
$log = new Logger('local_dev');
$log->pushHandler(new StreamHandler($upload_dir .'dev.log', Level::Warning));

// Global error handler for unhandled exceptions.
set_exception_handler(function (\Throwable $e) use ($log){
    $log->error($e->getMessage());
    echo $e;
});


/**
 * Decodes posted file data.
 * @param $data
 *
 * @return false|string
 */
function decode_chunk( $data ) {
    $data = explode( ';base64,', $data );

    if ( ! is_array( $data ) || ! isset( $data[1] ) ) {
        return false;
    }

    $data = base64_decode( $data[1] );
    if ( ! $data ) {
        return false;
    }

    return $data;
}

$post_data = json_decode(file_get_contents('php://input'), true);
$headers = getallheaders();

if (isset( $post_data['file']) && !isset($post_data['move_uploaded'])) {

    $message =  'test message!!';

	$file_data = decode_chunk( $post_data['file_data']);
	$file_path = $upload_dir . $post_data['file'];
	$file_name = $post_data['file'];

    file_put_contents( $file_path, $file_data, FILE_APPEND );

	ob_start();
	var_dump("|||||||||||||||||||||||||||||||");
    var_dump($_POST);
	var_dump("|||||||||||||||||||||||||||||||");
	error_log(ob_get_clean(), 4);


    $saved = TRUE;

	$data = array("Saved" => $saved);

	header('Content-Type: application/json');
	echo json_encode($data);
	exit();

}

elseif (isset($post_data['move_uploaded'] )) {


    $s3Client = new AwsS3Client();
    $s3Client = $s3Client->s3Client;
    $bucket = $_ENV['AWS_S3_BUCKET'];

    // Start of multipart uploads
    $key = $post_data['file'];


    $file_path = $upload_dir . $key;

    // Use multipart upload
    $source = $file_path;
    $uploader = new MultipartUploader($s3Client, $source, [
        'bucket' => $bucket,
        'key' => $key,
     'before_initiate' => function (\Aws\Command $command) {
         // $command is a CreateMultipartUpload operation
         $command['CacheControl'] = 'max-age=3600';
     },
     'before_upload' => function (\Aws\Command $command) {
        // manual gc.
     gc_collect_cycles();
         // $command is an UploadPart operation
         $command['RequestPayer'] = 'requester';
     },
     'before_complete' => function (\Aws\Command $command) {
         // $command is a CompleteMultipartUpload operation
         $command['RequestPayer'] = 'requester';
     },
    ]);

    //Recover from errors
    do {
        try {
            $result = $uploader->promise()->wait();
        } catch (MultipartUploadException $e) {
            $uploader = new MultipartUploader($s3Client, $source, [
                'state' => $e->getState(),
            ]);
        }
    } while (!isset($result));

    //Abort a multipart upload if failed
    try {
        $result = $uploader->promise()->wait();

     // Delete the file
        echo "Upload complete: {$result['ObjectURL']}\n";
    } catch (MultipartUploadException $e) {
        // State contains the "Bucket", "Key", and "UploadId"
        $params = $e->getState()->getId();
        $result = $s3Client->abortMultipartUpload($params);
        echo $e->getMessage() . "\n";
    }




// End of multipart uploads

    unlink($file_path);

    $data = array("Saved" => true);

    header('Content-Type: application/json');
    echo json_encode($data);
    exit();

}
?>
 <!doctype html>
 <html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="dist/main.css">
  <title>A Web exp.!</title>
 </head>
 <body class="content-fluid">
 <header>

 </header>
<div>
    <hr>
    <hr>
    <hr>

       <form action="" method="post" enctype="multipart/form-data">
           <h3 class="text-center mb-5">Uploading large files in PHP, from client's browser to an s3 bucket!</h3>

        <p>Select a file to upload below.</p>
        <p id="file-upload-progress">
         <label for="file-upload-progress">File upload progress: </label>
         <progress id="file-upload-progress" value="0" max="100">0%</progress>
        </p>

        <input id="file-upload-input" type="file" name="file_upload_input" /><br><br>

        <input id="file-upload-submit" name="file-upload-submit" class="button button-primary" type="submit" value="Upload file" />

       </form>
    <hr>
    <hr>
    <hr>
    <script src="dist/main.bundle.js"></script>
    </div>
 <footer>
  <a href="https://flaircore.com/about" target="_blank">Copyright@2023 FlairCore.com</a>
 </footer>

 </body>
</html>
<?php
