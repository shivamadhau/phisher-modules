<?php

header("Access-Control-Allow-Origin: *");
error_reporting(E_ALL);
ini_set('display_errors', 1);

set_error_handler("someFunction");

function someFunction($errno, $errstr) {
    echo '<h2>Upload failed.</h2><br>';
    echo '<p>'.$errstr.'</p>';
}

function selfInvoker()
{
    if (!isset($_POST['audio-filename']) && !isset($_POST['video-filename'])) {
        echo 'Empty file name.';
        return;
    }

    // do NOT allow empty file names
    if (empty($_POST['audio-filename']) && empty($_POST['video-filename'])) {
        echo 'Empty file name.';
        return;
    }

    // do NOT allow third party audio uploads
    if (false && isset($_POST['audio-filename']) && strrpos($_POST['audio-filename'], "RecordRTC-") !== 0) {
        echo 'File name must start with "RecordRTC-"';
        return;
    }

    // do NOT allow third party video uploads
    if (false && isset($_POST['video-filename']) && strrpos($_POST['video-filename'], "RecordRTC-") !== 0) {
        echo 'File name must start with "RecordRTC-"';
        return;
    }

    $fileName = '';
    $tempName = '';
    $file_idx = '';

    $filePath = 'video.webm';

    // make sure that one can upload only allowed audio/video files
    $allowed = array(
        'webm',
        'wav',
        'mp4',
        'mkv',
        'mp3',
        'ogg'
    );
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    if (!$extension || empty($extension) || !in_array($extension, $allowed)) {
        echo 'Invalid file extension: '.$extension;
        return;
    }

    if (!move_uploaded_file($tempName, $filePath)) {
        if(!empty($_FILES["file"]["error"])) {
            $listOfErrors = array(
                '1' => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
                '2' => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
                '3' => 'The uploaded file was only partially uploaded.',
                '4' => 'No file was uploaded.',
                '6' => 'Missing a temporary folder. Introduced in PHP 5.0.3.',
                '7' => 'Failed to write file to disk. Introduced in PHP 5.1.0.',
                '8' => 'A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help.'
            );
            $error = $_FILES["file"]["error"];

            if(!empty($listOfErrors[$error])) {
                echo $listOfErrors[$error];
            }
            else {
                echo 'Not uploaded because of error #'.$_FILES["file"]["error"];
            }
        }
        else {
            echo 'Problem saving file: '.$tempName;
        }
        return;
    }
    error_log("Received " .$filePath. "\r\n", 3, "Log.log");
    echo 'success';
}

selfInvoker();
?>
