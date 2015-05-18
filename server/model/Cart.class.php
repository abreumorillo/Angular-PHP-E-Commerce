<?php
    if(count(get_included_files()) ==1) exit("Direct access not permitted.");
	/**
	* Purpose 	:This class is used to represent a shopping cart to be presented to the user. This class is used for presentation purpose.
	* Date 		: 3/8/2015 | Modified 3/12/2015
	* @author   : Neris S. Abreu <nsa2741@rit.edu>
	*/
	class Cart
	{
		public $products = array();
        public $totalCost = 0.0;
        public $totalItem = 0;

		function __construct($productsForCart=array())
		{
			$this->products = $productsForCart;
            $this->calculateTotalCost();
            $this->calculateTotalItem();
		}

        /**
         * This function calculate the total price for the cart. it uses the sales price for computing the value.
         * If salePrice is equal 0 then uses the regular price to calculate the total amount.
         */
        private function calculateTotalCost(){
            foreach($this->products as $product){
                if($product->salePrice > 0){
                    $this->totalCost += ($product->salePrice * $product->quantity);
                }else{
                    $this->totalCost += ($product->price * $product->quantity);
                }
            }
        }

        private function calculateTotalItem(){
            foreach($this->products as $product){
                $this->totalItem += $product->quantity;
            }
        }
	}