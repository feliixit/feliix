<?php

//upload.php

$image = '';

if(isset($_FILES['file']['name']))
{
    $image_name = $_FILES['file']['name'];
    $valid_extensions = array("jpg","jpeg","png");
    $uploaddir="../img/";
    $extension = pathinfo($image_name, PATHINFO_EXTENSION);
    if(in_array($extension, $valid_extensions))
    {
        $upload_path = $uploaddir . $image_name;
        if(move_uploaded_file($_FILES['file']['tmp_name'], $upload_path))
        {
            $message = 'Image Uploaded';
            $code = 0;
            $image = $upload_path;
        }
        else
        {
            $message = 'There is an error while uploading image';
        }
    }
    else
    {
        $message = 'Only .jpg, .jpeg and .png Image allowed to upload';
    }
}
else
{
    $message = 'Select Image';
}

$output = array(
    'code' => $code,
    'message'  => $message,
    'image'   => $image
);

echo json_encode($output);


?>