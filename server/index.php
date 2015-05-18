<?php
   //required loader, this script provides the __autoload function.
   require_once 'inc/loader.php';
   //Get the request method of the request.
   $requestMethod = $_SERVER['REQUEST_METHOD'];
   //then based on the request method we determine witch action to execute
   if($requestMethod === "GET")
   {
     /* $productRepo = new ProductRepository();
       $products = $productRepo->countProduct();
       echo '<pre>';
       //echo print_r($products);
       echo $products;
       echo '</pre>';
       */
       //The action is used to determine which function to execute. the action is specified as part of the
       //request coming from the client.
        $action = $_GET['action'];
       //Based on the action we invoke the right function
        switch($action){
            case "getProductsOnSale":
                getProductsOnSale();
                break;
            case "getProductCatalog":
                $page = filter_var($_GET['page'], FILTER_SANITIZE_NUMBER_INT);
                getProductCatalog($page);
                break;
            case "countProduct":
                $isProductFromCatalog = false;
                if(isset($_GET['isProductFromCatalog'])){
                    $isProductFromCatalog = filter_var($_GET['isProductFromCatalog'], FILTER_VALIDATE_BOOLEAN);
                }
                countProduct($isProductFromCatalog);
        }
   } elseif ($requestMethod === "POST") {
       //This scripts only process incoming GET request as the responsibility of this scripts
       //is getting products from the database.
       $output = array(
           "error" => "This method is not supported"
       );
      echo json_encode($output);
   }

    /**
     * Function to get the product on sale from the database. the result is echo out as json.
     * the json is process and render by the angular client side application.
     * @return array of products
     */
    function getProductsOnSale() {
        $productRepo = new ProductRepository();
        $products = $productRepo->getProductsOnSale();
         echo json_encode($products);
        return;
    }

/**
 * Function used for getting the catalog of product from the database. All products not on sale are returned by this function
 * @param $page int
 * @return array of products
 */
    function getProductCatalog($page) {
        $productRepo = new ProductRepository();
        $products = $productRepo->getProductCatalog($page);
        echo json_encode($products);
        return;
    }

    /**
     * Count the product in the database
     * @param $isProductFromCatalog
     */
    function countProduct($isProductFromCatalog){
        $productRepo = new ProductRepository();
        $productCount = $productRepo->countProduct($isProductFromCatalog);
        echo json_encode($productCount);
        return;
    }

   /*date_default_timezone_set('America/New_York');
   echo date("Y-m-d");*/

   //TABLES: Products, Carts, Users, Roles, UserRoles

