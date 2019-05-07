<?php
require_once ('AmazonWebServicesS3.php');

class FileUploader{

    /**
     * valid image mime type
     * @var array of string
     */
    private $image_valid_mime_type = array('jpg', 'jpeg', 'gif', 'png');

    /**
     * image maximum size (5MB)
     * @var integer
     */
    private $image_max_size        = 5242880;

    /**
     * documents valid mime type (for policies documents mainly)
     * @var array of strings
     */
    private $doc_valid_mime_type   = array('doc', 'docx', 'dot', 'dotx', 'pdf', 'xls', 'xlsx');

    /**
     * documents maximum size (5MB)
     * @var integer
     */
    private $doc_max_size          = 5242880;


    /**
     * temporary path when resizing image
     * @var string
     */
    private $resize_temp_path          = UPLOADED_FILES."resized";

    /**
     * upload path to s3 when resizing image
     * @var string
     */
    private $upload_temp_path          = UPLOADED_FILES."upload";


    /**
     * wrapper for validate valid documents
     *
     * @param $file
     *
     * @return mixed
     */

    public function validateDocument($file){
        return $this->validate($file, $this->doc_valid_mime_type,  $this->doc_max_size);
    }

    /**
     * wrapper for validate valid images
     *
     * @param $file
     *
     * @return mixed
     */
    public function validateImage($file){
        return $this->validate($file, $this->image_valid_mime_type,  $this->image_max_size);

    }

    /**
     * function that validate the uploaded file
     *
     * @param $file
     * @param $file_valid_type
     * @param $file_max_size
     *
     * @return mixed
     */

    private function validate($file, $file_valid_type, $file_max_size){

        if(empty($file['name'])){
            return 'Please upload file.';
        }

        if(isset($file['type']) == false){
            return 'Type does not exist.';
        }
        if(isset($file['size']) == false){
            return 'Size does not exist.';
        }

        $type = explode('.',$file['name']);

        if(!in_array($type[1], $file_valid_type)){
            $last_type = $file_valid_type[count($file_valid_type) -1];
            array_pop($file_valid_type);
            $valid_type = implode(',', $file_valid_type );
            return 'Invalid file type. Please upload file that are either '. $valid_type ." and ". $last_type.".";
        }

        if($file['size'] > $file_max_size ){
            return 'File size is greater than 5MB.';
        }

        return true;

    }

    /**
     * upload image
     *
     * @param $file
     *
     * @return mixed
     */
    public function upload($file,$path){
        $s3 = new AmazonWebServicesS3();
        $response = $s3->upload($file, $path);
        if($response === false){
            return false;
        }
        return true;
    }

    /**
     * resize image image
     *
     * @param $file
     *
     * @return mixed
     */
    public function resize($file, $size_data){
        $type = exif_imagetype($file['tmp_name']);

        // copy file to the resized path first
        $extension = image_type_to_extension($type);
        $temp_image = $this->resize_temp_path . '/' . date('YmdHis') . $extension;
        $distanation_image = $this->upload_temp_path . '/' . date('YmdHis') . $extension;
        $result = move_uploaded_file($file['tmp_name'], $temp_image);

        if (!$result) {
            return array(false,'Failed to save file');
        }

        if(empty( $size_data)){
           return array(true,"No resizing needed.");
        }

        switch ($type) {
            case IMAGETYPE_GIF:
                $src_img = imagecreatefromgif($temp_image);
                break;

            case IMAGETYPE_JPEG:
                $src_img = imagecreatefromjpeg($temp_image);
                break;

            case IMAGETYPE_PNG:
                $src_img = imagecreatefrompng($temp_image);
                break;
        }

        if (!$src_img) {
            return array(false, "Failed to read the image file");
        }

        $data = json_decode(stripslashes($size_data));



        list($width_orig, $height_orig) = getimagesize($temp_image);

        $dst_img = imagecreatetruecolor($data->width, $data->height);
        //if($type == IMAGETYPE_PNG){
            imagealphablending($dst_img, false);
            imagesavealpha($dst_img, true);
            $transparent = imagecolorallocatealpha($dst_img, 255, 255, 255, 127);
            imagefilledrectangle($dst_img, 0, 0, $data->width, $data->height, $transparent);
        //}

        if($data->action == "crop"){
            $result = imagecopyresampled($dst_img, $src_img, 0, 0, $data->x, $data->y, $data->width, $data->height, $data->width, $data->height);
        }

        if($data->action == 'resize'){
            //resize
            $result = imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $data->width, $data->height, $width_orig, $height_orig);
        }

        if ($result) {
            switch ($type) {
                case IMAGETYPE_GIF:
                    $result = imagegif($dst_img, $distanation_image);
                    break;

                case IMAGETYPE_JPEG:
                    $result = imagejpeg($dst_img, $distanation_image);
                    break;

                case IMAGETYPE_PNG:
                    $result = imagepng($dst_img, $distanation_image);
                    break;
            }

            if (!$result) {
                return array(false,"Failed to save the cropped image file");
            }
        } else {
            return array(false,"Failed to crop the image file");
        }

        imagedestroy($src_img);
        imagedestroy($dst_img);
        unlink($temp_image);

        return array(true,$distanation_image);
    }

}