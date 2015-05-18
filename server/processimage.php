<?php
/**
 * Purpose          : This script is used to process image related to a product. it uses the Image manager class for doing task related to storing the picture
 *                    in the right directory and generate a name for the uploaded picture.
 * Date             : 3/14/2015
 * @author          : Neris Sandino Abreu.
 */

//Require the loader file which include the autoload
require_once 'inc/loader.php';

$productId = filter_var($_POST['productId'], FILTER_SANITIZE_NUMBER_INT);
//verify if any file has been uploaded so we can move it to the proper directory and grab the imageUrl
if ( !empty( $_FILES )){
    echo 'processing file';
    $imageManager = new ImageManager($_FILES);
    if($imageManager->saveImage()){
        $productRepository = new ProductRepository();
        $updateImage = $productRepository->updateProductImageUrl($imageManager->getImageUrl(), $productId);
        if(!$updateImage){
            $errorResponse = array(
                'error'=>'The picture could not be uploaded'
            );
            echo json_encode($errorResponse);
        }
    }
}