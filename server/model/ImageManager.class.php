<?php
if(count(get_included_files()) ==1) exit("Direct access not permitted.");
/**
 * Purpose          : This class is responsible for moving uploaded file to the image directory
 *                    the class also provide the functionality of generating the image URL to be used in GUI part
 * Date             : 3/13/2015
 * @author          : Neris Sandino Abreu
 */
class ImageManager {
    private $_file;
    private $_fileName;
    const IMAGE_DIRECTORY = '/home/nsa2741/Sites/projectone/images/';
    const IMAGE_URL = 'images/';

    /**
     * @param $file -> $_FILE
     */
    function __construct($file){
        $this->_file = $file;
    }

    /**
     * The function is responsible for moving the file from $_FILE to the image directory, before attempting to do so
     * the function validates that the file is an image JPEG
     * @return bool
     */
    function saveImage() {
        echo ' trying to save image';
        //verify if file is an image http://php.net/manual/en/function.exif-imagetype.php
        if (exif_imagetype($this->_file['file']['tmp_name']) == IMAGETYPE_JPEG ) {
            $tempPath = $this->_file[ 'file' ][ 'tmp_name' ];
            $this->_fileName = str_replace(' ','', $this->_file[ 'file' ][ 'name' ]);
            $uploadPath = self::IMAGE_DIRECTORY . $this->_fileName;
            return move_uploaded_file( $tempPath, $uploadPath );
        }
        echo $this->_fileName;
        return false;
    }

    /**
     * Get the image URL for an uploaded image
     * @return string with imageUrl to be store in the database
     */
    function getImageUrl(){
        if(strlen($this->_fileName) > 0){
            return self::IMAGE_URL.$this->_fileName;
        }
        return self::IMAGE_URL.$this->_file['file']['name'];
    }

}