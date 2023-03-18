
<?php

if (isset($_POST['Upload'])) {
    // Check for file upload errors
    if ($_FILES['uploaded']['error'] !== UPLOAD_ERR_OK) {
        $html .= '<pre>Your image was not uploaded. Error encountered during file upload.</pre>';
        return;
    }

    // Where are we going to be writing to?
    $target_path  = DVWA_WEB_PAGE_TO_ROOT . "static/uploads/";

    // Generate a unique file name to avoid collisions
    $unique_id = uniqid();
    $file_original_name = filter_var($_FILES['uploaded']['name'], FILTER_SANITIZE_STRING);
    $file_extension = pathinfo($file_original_name, PATHINFO_EXTENSION);
    $filename = $unique_id . "." . $file_extension;
    $target_path .= basename($filename);

    // Validate user input
    // ... [existing validation code]

    // Validate file type
    // ... [existing validation code]

    // Validate file size
    // ... [existing validation code]

    // Validate path
    // ... [existing validation code]

    // Validate file extension
    // ... [existing validation code]

    // Validate file name length
    // ... [existing validation code]

    // Validate file contents (MIME type & encoding)
    // ... [existing validation code]

    // Can we move the file to the upload folder?
    if (!move_uploaded_file($_FILES['uploaded']['tmp_name'], $target_path)) {
        $html .= '<pre>Your image was not uploaded.</pre>';
    } else {
        $html .= "<pre>{$target_path} succesfully uploaded!</pre>";
    }
}

?>
