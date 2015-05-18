<?php
if(count(get_included_files()) ==1) exit("Direct access not permitted.");
/**
* Purpose	: This class is used to represent products for the e-commerce.
* Date		: 3/8/2015
* @author 	: Neris Sandino Abreu <nsa2741@rit.edu>
* @version 	: 1.0
*/
class Product
{
    //This constants are used to define the maximum and minimum amount of product on sale (Discount)
    const MAX_PRODUCT_ON_SALE = 5;
    const MIN_PRODUCT_ON_SALE = 3;
    //Fields to represent the attribute of a product.
    public $productId;
    public  $name;
    public $description;
    public $imageUrl;
    public $price;
    //Price for product on discount, a maximum of 5 products can be on discount according
    //to the rule
    public $salePrice;
    public $quantity;

    /*
        Product class constructor.
        @param $name string, represents the name of the product.
        @param $description string, represents the description for the product.
        @param $price float, represents the price of the product.
        @param $priceOnDiscount float, represents the sale price of products on discount.
                A maximum of 5 products can be on discount.
        @param $quantity int, represents the amount of product on hand.
        @param $productId int, represents the Id of the product in the database.
     */
    function __construct($name="", $description="", $imageUrl="", $price=0.0, $salePrice=0.0,
        $quantity=0, $productId=0)
    {
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->salePrice = $salePrice;
        $this->quantity = $quantity;
        $this->imageUrl = $imageUrl;
        if($productId > 0){
            $this->productId = $productId;
        }
    }
}