(function() {
    'use strict';

    angular
        .module('app')
        .controller('IndexController', IndexController);
         IndexController.$inject =['IndexService','CartService', 'toastr'];
    /* @ngInject */
    function IndexController(IndexService,CartService, toastr) {
        /*jshint validthis: true */
        var vm = this;
        vm.title = 'Index Controller';
        //public expose member
        vm.productOnSale = [];
        vm.productCatalog = [];
        vm.totalProduct=0;

        //Functions
        vm.getProductsOnSale = getProductsOnSale;
        vm.addToCart = addToCart;

        //Pagination options
        vm.totalItems = 0;
        vm.currentPage = 1;
        vm.itemPerPage = 5;
        /***
         * Function execute every time we interact with the pagination control
         */
        vm.pageChanged = function() {
            getProductCatalog(vm.currentPage);
        };


        activate();

        /**
         * This function get execute when this controller is loaded
         */
        function activate() {
            getProductsOnSale();
            getProductCatalog(vm.currentPage);
            countProduct();
        }

        /**
         * Function that get products on sale. It uses the product services to make a request to the server.
         */
        function getProductsOnSale(){
            IndexService.getProductsOnSale().then(function (data) {
                vm.productOnSale = data;
            });
        }

        /**
         * Function that get products from the catalog.It uses the product services to make a request to the server.
         */
        function getProductCatalog(page) {
            vm.productCatalog = [];
            IndexService.getProductCatalog(page).then(function (data) {
                vm.productCatalog = data;
            })
        }

        /**
         * Add product to the cart. It uses the CartService in order send the data to the server using AJAX.
         * @param product
         */
        function addToCart(product){
            //data to be posted to the server.
            var dataToSave = {
                productId: product.productId,
                quantity: 1
            }
            CartService.addToCart(dataToSave).then(function (data) {
                if(data.error.length > 0){
                    toastr.error('The product could not be added', 'An error has occurred');
                }else{
                    toastr.success(product.name +' has been added to the cart.', 'Product added');
                    //Reduce quantiy -- UPDATE UI ---
                    product.quantity-=1;
                }
            });
        }

        /**
         * Count the products in the database, this information is used for the pagination.
         */
        function countProduct(){
            IndexService.countProduct().then(function (data) {
               vm.totalItems = data;
            });
        }
    }
})();