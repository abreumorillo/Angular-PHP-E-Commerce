<?php
    /**
     * Purpose      : This script is used to process the cart. it adds elements to the cart table. It uses sessionID as the user identifier
     *                This scripts works as data endpoint which interact with client side javascript service.
     * @author      : Neris Sandino Abreu
     * Date         : 3/11/2015
     */

    //The session information will be used to associate a shopping cart to a given user.
    session_name('NSA-STORE');
    session_start();
    $userInfo = array();
    $userInfo['userSessionId'] = session_id();

    //if is a logged on user then add the information to the user info array
    if(isset($_SESSION['userId'])){
        $userInfo['userId'] = $_SESSION['userId'];
    }else{
        $userInfo['userId'] = 0;
    }

    //Require the loader file which include the autoload
    require_once 'inc/loader.php';

    //Get the request method of the request.
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    if($requestMethod === 'GET'){
        //The action is used to determine which function to execute. the action is specified as part of the
        //request coming from the client.
        $action = $_GET['action'];
        //Based on the action we invoke the right function
        switch($action){
            case 'emptyCart':
                emptyCart($userInfo);
                break;
            case 'getCart':
                getCartInfo($userInfo);
        }
    //If request method is POST then the following code will be executed.
    } elseif($requestMethod === 'POST'){
        $data = json_decode($_POST['data']);
        //save the data to the database
        addToCart($data, $userInfo);
    }

    /**
     * Function used for emptying the cart related to a user session id.
     * @param $userInfo
     */
    function emptyCart($userInfo){
        $cartRepository = new CartRepository();
        $productRepository = new ProductRepository();
        $deleteProducts = $cartRepository->emptyCart($userInfo);
        if(is_array($deleteProducts)){
            //Update the product quantity for each product in the array
            foreach($deleteProducts as $productId => $quantity){
               $productRepository->updateProductQuantity($productId, $quantity, true);
            }
            echo json_encode(array('message'=> 'Cart empty', 'error' =>''));
        }
        else{
            echo json_encode(array('message'=> '', 'error'=>'Error description'));
        }
    }

    /**
     * This function uses the Cart Repository in order to add or update products in the cart table
     * @param $data
     * @param $userInfo
     */
    function addToCart($data, $userInfo){
        $cartRepository = new CartRepository();
        $cartId = $cartRepository->addOrUpdate($data, $userInfo);
        //if true then operation success.
        if($cartId){
            // Reduce the product quantity as item is added to the shopping cart.
            $productRepository = new ProductRepository();
            $productRepository->updateProductQuantity(filter_var($data->productId, FILTER_SANITIZE_NUMBER_INT));
            echo json_encode(array('message'=>'Product added to the database', 'error'=>''));
        }else{
            echo json_encode(array('error'=>'Error trying to add or update', 'message'=> ''));
        }
    }

    /**
     * Get the products from the cart table related to a session id
     * @param $userInfo
     */
    function getCartInfo($userInfo){
        $cartRepository = new CartRepository();
        $productsForCart = $cartRepository->getProductsFromCart($userInfo);
        $cart = new Cart($productsForCart);
        echo json_encode($cart);
    }
