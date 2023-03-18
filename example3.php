
<?php
function validate_file_name($file_name) {
    return preg_match('/^[a-zA-Z0-9_\-\.]+$/', $file_name) && !preg_match('/\.\./', $file_name);
}

function reprocess_image($uploaded_tmp, $uploaded_ext) {
    $img = null;
    switch (strtolower($uploaded_ext)) {
        case 'jpg':
        case 'jpeg':
            $img = imagecreatefromjpeg($uploaded_tmp);
            break;
        case 'png':
            $img = imagecreatefrompng($uploaded_tmp);
            break;
        default:
            return false;
    }

    if (!$img) {
        return false;
    }

    return $img;
}

function save_image($img, $target_path, $uploaded_ext) {
    switch (strtolower($uploaded_ext)) {
        case 'jpg':
        case 'jpeg':
            return imagejpeg($img, $target_path);
        case 'png':
            return imagepng($img, $target_path);
    }

    return false;
}

function upload_image() {
    if (empty($_FILES['uploaded']) || !validate_file_name($_FILES['uploaded']['name'])) {
        return "Your image was not uploaded. Invalid file name.";
    }

    $uploaded_name = $_FILES['uploaded']['name'];
    $uploaded_ext = substr($uploaded_name, strrpos($uploaded_name, '.') + 1);
    $uploaded_size = $_FILES['uploaded']['size'];
    $uploaded_tmp = $_FILES['uploaded']['tmp_name'];

    if (!validate_file_ext($uploaded_ext)) {
        return "Your image was not uploaded. We can only accept JPEG or PNG images.";
    }

    if (!validate_file_size($uploaded_size)) {
        return "Your image was not uploaded. Invalid file size.";
    }

    $img = reprocess_image($uploaded_tmp, $uploaded_ext);
    if (!$img) {
        return "Your image was not processed properly. Try another file.";
    }
    
    $target_path = "uploads/" . md5(uniqid(rand(), true)) . "." . $uploaded_ext;

    if (!save_image($img, $target_path, $uploaded_ext)) {
        imagedestroy($img);
        return "Your image was not uploaded.";
    }

    if (!validate_file($target_path) || !validate_file_type($target_path) || !validate_file_contents($target_path)) {
        unlink($target_path);
        return "Your image was not uploaded. Invalid file.";
    }
    
    imagedestroy($img);
    chmod($target_path, 0600);
    return "{$target_path} successfully uploaded!";
}

$html = '<pre>' . upload_image() . '</pre>';
?>
