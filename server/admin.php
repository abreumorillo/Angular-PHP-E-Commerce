<?php
/**
 * Created by PhpStorm.
 * User: sabreu
 * Date: 3/12/2015
 * Time: 10:22 PM
 */
//The session information will be used to associate a shopping cart to a given user.
session_name('NSA-STORE');
session_start();
//Require the loader file which include the autoload
require_once 'inc/loader.php';
//This array is used to store user information like the userSession which is used to save the cart.
$userInfo = array();
$userInfo['userSessionId'] = session_id();

//if is a logged on user then add the information to the user info array
if(isset($_SESSION['userId']) &&  $_SESSION['isAdmin']==1 ){
    $userInfo['userId'] = $_SESSION['userId'];

    //Get the request method of the request.
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    //Invoke function based on the incoming request method.
    if($requestMethod === 'GET') {
        $action = $_GET['action'];
        if($action === 'getProducts'){
            getProducts();
        }else {
            echo json_encode(array('error'=>'A valid action have to be specified'));
        }
    } elseif($requestMethod === 'POST'){
        $errors = array();
        $productRequiredFields = array('name', 'description', 'price', 'quantity');
        $imageUrl = "";
        //Grab the posted data.
        $jsonData = json_decode($_POST['data']);

        //validate incoming data
        $errors = Validation::validateRequiredField($jsonData, $productRequiredFields);

        //Get the data from the json object //SANITIZATION AND VALIDATION//
        $productId = filter_var(Validation::sanitizeInput($jsonData->productId), FILTER_SANITIZE_NUMBER_INT);
        $name = Validation::sanitizeInput($jsonData->name);
        $description = Validation::sanitizeInput($jsonData->description);
        $price = floatval(Validation::sanitizeInput($jsonData->price));
        $salePrice = floatval(Validation::sanitizeInput($jsonData->salePrice));
        $quantity = intval(Validation::sanitizeInput($jsonData->quantity));
        $imageUrl = Validation::sanitizeInput($jsonData->imageUrl);
        $isProductOnSale = filter_var($jsonData->isOnSale, FILTER_VALIDATE_BOOLEAN);

        //apply filter
        if(!Validation::filterString($name)){
            $errors['Product Name'] = "Invalid product name";
        }
        if(!Validation::filterString($name)){
            $errors['Product Description'] = "Invalid product description";
        }
        if($price < 0){
            $errors['Price'] = "Price cannot be a negative number";
        }
        if($quantity <0 ){
            $errors['Quantity'] = "Quantity cannot be a negative number";
        }
        if(isset($salePrice) && $salePrice < 0){
            $errors['Sale Price'] = "Sale Price cannot be a negative number";
        }
        $canUpdateOrInsert = false;
        //Before we continue verify we can add|update products for sale, only a maximum of 5 products can be on sale
        $productRepository = new ProductRepository();
        if($isProductOnSale && $productId > 0){
            $canUpdateOrInsert = true;
        }elseif(getNumberOfProductOnSale($productRepository) >= Product::MAX_PRODUCT_ON_SALE && $salePrice > 0){
            $canUpdateOrInsert = false;
            $errors['Product on Sale greater than 5'] = "Only a maximum of 5 products can be on sale at the same time";
        }else{
            $canUpdateOrInsert = true;
        }

        //if no error proceed
        if(empty($errors) && $canUpdateOrInsert){
            switch($jsonData->action){
                case 'addProduct':
                    insertProduct($name,$description,$price, $salePrice, $quantity, $imageUrl, $productRepository);
                    break;
                case 'updateProduct':
                    updateProduct($productId, $name,$description,$price, $salePrice, $quantity, $imageUrl, $productRepository);
                    break;
            }
        } else{ //if validation fail then echo array of errors to the client side app.
            $errorResponse = array(
                'error' => 'Errors had occurred while processing the data',
                'errors' => $errors
            );
            echo json_encode($errorResponse);
        }

    }
    }else{
        $errorResponse = array(
            "error"=> "unauthorized user",
            "isAuthorize" => false,
            "errors" => array("Only allowed users")
        );
        echo json_encode($errorResponse);
}

/**
 * Get all the products from the database
 * An array of product is the output of this function
 */
function getProducts(){
    $productRepository = new ProductRepository();
    $products = $productRepository->getProducts();
    echo json_encode($products);
    return;
}

/**
 * Add product to the database
 * @param $name
 * @param $description
 * @param $price
 * @param $salePrice
 * @param $quantity
 * @param $imageUrl
 * @param $productRepository instance of the product repository
 */
function insertProduct($name,$description,$price, $salePrice, $quantity, $imageUrl, $productRepository){
    $insertedProductId = $productRepository->insertProduct($name, $description,$price, $salePrice, $quantity, $imageUrl);
    if($insertedProductId){
        $successResponse = array(
            'message' => 'Product added to the database',
            'productId' => $insertedProductId
        );
        echo json_encode($successResponse);
    }else{
        $errorResponse = array(
            'error' => 'Error adding the product',
            'errors' => array('An error has occurred while trying to add the product')
        );
        echo json_encode($errorResponse);
    }

}

/**
 * Update an existing product in the database
 * @param $productId
 * @param $name
 * @param $description
 * @param $price
 * @param $salePrice
 * @param $quantity
 * @param $imageUrl
 * @param $productRepository instance of the product repository
 */
function updateProduct($productId, $name,$description,$price, $salePrice, $quantity, $imageUrl, $productRepository){
    if($productRepository->updateProduct($productId, $name, $description, $price, $salePrice, $quantity, $imageUrl)){
        $successResponse = array(
            'message' => 'Product updated successfully',
            'productId' => $productId
        );
        echo json_encode($successResponse);
    }else {
        $errorResponse = array(
            'error' => 'Error updating the product',
            'errors' => array('An error has occurred while trying to update the product')
        );
        echo json_encode($errorResponse);
    }
}

/**
 * Count the number of products in the database that have sale price greater than 0.
 * @param $productRepository instance of the ProductRepository
 * @return int
 */
function getNumberOfProductOnSale ($productRepository){
    return $productRepository->getNumberOfProductOnSale();
}

