<?php
    if(count(get_included_files()) ==1) exit("Direct access not permitted.");
	/**
	* Purpose	: This class is used to provide database funtionality to the product Database
	* date      : 3/8/2015
	* @author   : Neris S. Abreu <nsa2741@rit.edu>
	*/
    //Require the loader file which include the autoload
    require_once 'inc/loader.php';

	class ProductRepository extends BaseRepository
	{
        const DEFAULT_IMAGE_URL = "images/defaultimage.jpg";

        /**
         * This constructor call the parent's constructor to get the instance of the database
         */
		function __construct()
		{
            parent::__construct();
		}

        /**
         * Function to get the products on sale from the database. Products on sales are those that the sale price is  > 0
         * and only a max of 5 could exist in database.
         * @return array of products(object)
         */
		public function getProductsOnSale()
		{
            $data = array();
            $query = "SELECT ProductId, Name, Description, ImageUrl, Price, SalePrice,Quantity
                      FROM Products
                      WHERE SalePrice > 0
                      LIMIT 5";
            if($stmt = $this->mysqli->prepare($query)){
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($productId, $name, $description, $imageUrl, $price, $salePrice, $quantity);
                if($stmt->num_rows){
                    while($stmt->fetch()){
                        $data[] = new Product($name, $description, $imageUrl, $price, $salePrice,$quantity, $productId);
                    }
                }
                $stmt->close();
            }
            return $data;
		}

        /**
         * Function to get the catalog of product from the database. This function provides the ability to paginate products in the database
         * using LIMIT and OFFSET
         * @param int $page
         * @param int $itemPerPage
         * @return array of products (object)
         */
        public function getProductCatalog($page=1, $itemPerPage=5){
            $start = ($page -1) * $itemPerPage;
            $data = array();
            $query = "SELECT ProductId, Name, Description, ImageUrl, Price, SalePrice,Quantity
                      FROM Products
                      WHERE SalePrice <= 0
                      LIMIT ? OFFSET ?";
            if($stmt = $this->mysqli->prepare($query)){
                //scape input data
                $start = $this->mysqli->real_escape_string($start);
                $itemPerPage = $this->mysqli->real_escape_string($itemPerPage);
                $stmt->bind_param("ii",$itemPerPage, $start);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($productId, $name, $description, $imageUrl, $price, $salePrice, $quantity);
                if($stmt->num_rows){
                    while($stmt->fetch()){
                        $data[] = new Product($name, $description, $imageUrl, $price, $salePrice,$quantity, $productId);
                    }
                }
                $stmt->close();
            }
            return $data;
        }

        /**
         * Count all the products in the database.
         * @param $isProductFromCatalog
         * @return int
         */
        public function countProduct($isProductFromCatalog){
            $count = 0;
            $query = "";
            if($isProductFromCatalog){
                $query = "SELECT COUNT(*) FROM Products WHERE SalePrice <= 0";
            }else{
                $query = "SELECT COUNT(*) FROM Products";
            }
            if($stmt = $this->mysqli->prepare($query)){
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($productCount);
                while($stmt->fetch())
                {
                    $count = $productCount;
                }
                $stmt->close();
            }
            return $count;
        }

        /**
         * Function to get all the products from the database.
         * @return array of products(object)
         */
        public function getProducts()
        {
            $data = array();
            $query = "SELECT ProductId, Name, Description, ImageUrl, Price, SalePrice,Quantity
                      FROM Products";
            if($stmt = $this->mysqli->prepare($query)){
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($productId, $name, $description, $imageUrl, $price, $salePrice, $quantity);
                if($stmt->num_rows){
                    while($stmt->fetch()){
                        $data[] = new Product($name, $description, $imageUrl, $price, $salePrice,$quantity, $productId);
                    }
                }
                $stmt->close();
            }
            return $data;
        }

        /**
         * Verify the amount of products on sale in the database
         * @return int
         */
        public function getNumberOfProductOnSale(){
            $count = 0;
            $query = "SELECT COUNT(*) FROM Products WHERE SalePrice > 0";
            if($stmt = $this->mysqli->prepare($query)){
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($productCount);
                while($stmt->fetch())
                {
                    $count = $productCount;
                }
                $stmt->close();
            }
            return $count;
        }

        /**
         * This function insert a new product into the database.
         * @param $name
         * @param $description
         * @param $price
         * @param $salePrice
         * @param $quantity
         * @param $imageUrl
         * @return int
         */
        public function insertProduct($name,$description,$price, $salePrice, $quantity, $imageUrl){
            if(empty($imageUrl)){
                $imageUrl = self::DEFAULT_IMAGE_URL;
            }
            $productId = 0;
            $query = "INSERT INTO Products SET
                      Name = ? ,
                      Description = ?,
                      Price = ?,
                      SalePrice = ?,
                      Quantity = ?,
                      ImageUrl = ? ";
            if($stmt = $this->mysqli->prepare($query)){
                $name = $this->mysqli->real_escape_string($name);
                $description = $this->mysqli->real_escape_string($description);
                $price = $this->mysqli->real_escape_string($price);
                $salePrice = $this->mysqli->real_escape_string($salePrice);
                $quantity = $this->mysqli->real_escape_string($quantity);
                $imageUrl =$this->mysqli->real_escape_string($imageUrl);
                $stmt->bind_param("ssddis", $name, $description, $price, $salePrice, $quantity, $imageUrl);
                $stmt->execute();
                $stmt->store_result();
                $productId = $stmt->insert_id;
                $stmt->close();
                return $productId;
            }
            return $productId;
        }

        /**
         * This function is used to update the picture of a product.
         * @param $imageUrl
         * @param $productId
         * @return int
         */
        public function updateProductImageUrl($imageUrl, $productId){
            $result=0;
            $query = "UPDATE Products SET ImageUrl = ?
                      WHERE ProductId = ?";
            if($stmt = $this->mysqli->prepare($query)){
                $imageUrl = $this->mysqli->real_escape_string($imageUrl);
                $productId = $this->mysqli->real_escape_string($productId);
                $stmt->bind_param("si", $imageUrl, $productId);
                $stmt->execute();
                $stmt->store_result();
                $result = $stmt->affected_rows;
                $stmt->close();
                return $result;
            }
            return $result;
        }

        /**
         * This function update an existing product.
         * @param $productId
         * @param $name
         * @param $description
         * @param $price
         * @param $salePrice
         * @param $quantity
         * @param $imageUrl
         * @return int
         */
        public function updateProduct($productId, $name, $description, $price, $salePrice, $quantity, $imageUrl){

            $result = false;
            $query = "UPDATE Products SET
                      Name = ? ,
                      Description = ?,
                      Price = ?,
                      SalePrice = ?,
                      Quantity = ?,
                      ImageUrl = ?
                      WHERE ProductId = ?";
            if($stmt = $this->mysqli->prepare($query)){
                $name = $this->mysqli->real_escape_string($name);
                $description = $this->mysqli->real_escape_string($description);
                $price = $this->mysqli->real_escape_string($price);
                $salePrice = $this->mysqli->real_escape_string($salePrice);
                $quantity = $this->mysqli->real_escape_string($quantity);
                $imageUrl =$this->mysqli->real_escape_string($imageUrl);
                $productId = $this->mysqli->real_escape_string($productId);
                $stmt->bind_param("ssddisi", $name, $description, $price, $salePrice, $quantity, $imageUrl, $productId);
                $stmt->execute();
                $stmt->store_result();
                $result = $stmt->affected_rows || $stmt->errno == 0;
                $stmt->close();
                return $result;
            }
            return $result;
        }

        /**
         * This function basically decrease the product by 1 every time one item is added to the shopping cart
         * @param $productId
         * @param int $quantityToAdd
         * @param bool $isIncrement to determine whether decrement or increment the amount of product.
         * @return bool
         */
        public function updateProductQuantity($productId, $quantityToAdd = 0, $isIncrement = false){
            $result = false;
            //Select current product quantity
            $currentQuantity = 0;
            $query = "SELECT Quantity FROM Products WHERE ProductId = ?";
            if($stmt = $this->mysqli->prepare($query)){
                $productId = $this->mysqli->real_escape_string($productId);
                $stmt->bind_param("i", $productId);
                $stmt->bind_result($quantity);
                $stmt->execute();
                $stmt->store_result();
                while($stmt->fetch()){
                    $currentQuantity = $quantity;
                }
                $stmt->close();
                //if is an increment we add the quantity to the current quantity else we decrement by one in case is just adding products to the cart
                if($isIncrement){
                    $currentQuantity += $quantityToAdd;
                }
                else{
                    if($currentQuantity > 0){
                        $currentQuantity -=1;
                    }
                }

                //Save the update quantity to the database
                $query = "UPDATE Products SET Quantity = ? WHERE ProductId = ?";
                if($stmt = $this->mysqli->prepare($query)){
                    $stmt->bind_param("ii", $currentQuantity, $productId);
                    $stmt->execute();
                    $stmt->store_result();
                    $result = $stmt->affected_rows || $stmt->errno == 0;
                    $stmt->close();
                    return $result;
                }
            }
            return $result;
        }

	}