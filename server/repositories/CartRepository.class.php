<?php
if(count(get_included_files()) ==1) exit("Direct access not permitted.");
    /**
     * Purpose          : Class used to provide database functionality for the Cart
     * Date             : 3/11/2015
     * @author          : Neris S. Abreu
     */
    //Require the loader file which include the autoload
    require_once 'inc/loader.php';

    class CartRepository extends BaseRepository{

        /**
         * This constructo call the parent constructor to get the instance of the database.
         */
        function __construct()
        {
            parent::__construct();
        }

        /**
         * Get the products from the cart related to a user session id.
         * @param $userInfo
         * @return array
         */
        function getProductsFromCart($userInfo){
            $productsForCart = array();
            $query = "SELECT Products.Name, Products.Description, Products.Price, Products.SalePrice, Carts.Quantity
                      FROM Products
                      JOIN Carts USING (ProductId)
                      WHERE Carts.UserSessionId = ?";
            if($stmt = $this->mysqli->prepare($query)){
                $stmt->bind_param("s", $userInfo['userSessionId']);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($productName, $productDescription, $productPrice, $productSalePrice, $quantity);
                if($stmt->affected_rows){
                    while($stmt->fetch()){
                        $productsForCart[] = new Product($productName, $productDescription,"", $productPrice, $productSalePrice, $quantity);
                    }
                }
                $stmt->close();
            }
            return $productsForCart;
        }

        /**
         * Add or update product, as this function invoke add or update it scape the data
         * @param $data
         * @param $userInfo
         * @return bool|int
         */
        function addOrUpdate($data, $userInfo){
            $currentQuantity = 0;
            //Scape data
            $productId = $this->mysqli->real_escape_string(filter_var($data->productId, FILTER_SANITIZE_NUMBER_INT));
            $quantity = $this->mysqli->real_escape_string(filter_var($data->quantity, FILTER_SANITIZE_NUMBER_INT));
            $userSessionId = $this->mysqli->real_escape_string($userInfo['userSessionId']);
            $userId = $this->mysqli->real_escape_string($userInfo['userId']);
            //--- Query to determine whether add or update the product quantity ---
            $query = "SELECT Quantity FROM Carts WHERE ProductId = ? AND UserSessionId = ?";
            if($stmt = $this->mysqli->prepare($query)){
                $stmt->bind_param("is", $productId, $userSessionId);
                $stmt->bind_result($existingQuantity);
                $stmt->execute();
                $stmt->store_result();
                //if we get num_rows > 0 then we update the quantity, otherwise we add the product to the cart
                if($stmt->num_rows){
                    //update product quantity
                    while($stmt->fetch()){
                        $currentQuantity = $existingQuantity; //Get the existing quantity
                    }
                    $stmt->close(); //we need to close the current statement to avoid error related to out of sync
                    return $this->updateProductQuantity($productId, $currentQuantity, $userSessionId);
                } else{
                    $stmt->close(); //free resource
                    return $this->addProductToCart($productId, $quantity,$userSessionId, $userId);
                }
            }
            return false;
        }

        /**
         * Add product to the Cart table
         * @param $productId
         * @param $quantity
         * @param $userSessionId
         * @param $userId
         * @return int
         */
        private function addProductToCart($productId, $quantity, $userSessionId, $userId){
            $query = "INSERT INTO Carts SET
                      ProductId = ?,
                      Quantity = ?,
                      UserSessionId = ?,
                      UserId = ?";
            if($stmt = $this->mysqli->prepare($query)){
                $stmt->bind_param("iisi", $productId, $quantity, $userSessionId, $userId);
                $stmt->execute();
                $stmt->store_result();
                $insertedId = $stmt->insert_id;
                $stmt->close();
                return $insertedId;
            }
            return 0;
        }

        /**
         * Update the quantity of an existing product in the cart table.
         * @param $productId
         * @param $currentQuantity
         * @param $userSessionId
         * @return bool
         */
        private function updateProductQuantity($productId, $currentQuantity, $userSessionId){
            $quantity = $currentQuantity + 1; //We add one to the current quantity
            $query = "UPDATE  Carts SET Quantity =?
                      WHERE UserSessionId = ? AND ProductId = ?";
            if($stmt = $this->mysqli->prepare($query)){
                $stmt->bind_param("isi", $quantity, $userSessionId, $productId);
                $stmt->execute();
                $stmt->store_result();
                $affectedRows = $stmt->affected_rows;
                $stmt->close();
                return $affectedRows > 0;
            }
            return false;
        }

        /**
         * Delete all record related with an user session Id. Before deleting the item it gets the item to be delete in order to update the product database
         * @param $userInfo
         * @return mixed
         */
        function emptyCart($userInfo){
            $productTobeDeleted = array();
            $userSessionId = $this->mysqli->real_escape_string($userInfo['userSessionId']);
            //Get productId and quantity before delete so we can update the product database.
            $query = "SELECT ProductId, Quantity FROM  Carts WHERE UserSessionId = ?";
            if($stmt = $this->mysqli->prepare($query)){
                $stmt->bind_param("s", $userSessionId);
                $stmt->bind_result($productId, $quantity);
                $stmt->execute();
                $stmt->store_result();
                if($stmt->num_rows){
                    while($stmt->fetch()){
                        $productTobeDeleted[$productId] = $quantity;
                    }
                }
                $stmt->close();
            }
            if(count($productTobeDeleted))
            {
                $query = "DELETE FROM Carts WHERE UserSessionId = ?";
                if($stmt = $this->mysqli->prepare($query)){
                    $stmt->bind_param("s", $userSessionId);
                    $stmt->execute();
                    $stmt->store_result();
                    $affectedRows = $stmt->affected_rows;
                    $stmt->close();
                    if($affectedRows){
                        return $productTobeDeleted;
                    }
                }
            }
            return false;
        }
    }