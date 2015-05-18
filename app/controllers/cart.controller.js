/**
 * Created by sabreu on 3/12/2015.
 */
angular
    .module('app')
    .controller('CartController', CartController);

CartController.$inject = ['toastr', 'CartService', '$state'];

/* @ngInject */
function CartController(toastr, CartService, $state) {
    /* jshint validthis: true */
    var vm = this;
    vm.cart = {};

    //Functions
    vm.activate = activate;
    vm.emptyCart = emptyCart;

    activate();

    ////////////////

    /**
     * This functions is executed when this script is loaded and it takes charge of loading the cart for the current user session from the database
     * In order to achieve it purpose it uses the CartService which basically makes AJAX call to the php script in the server side.
     */
    function activate() {
        CartService.getCart().then(function (data) {
            vm.cart = data;
            if(data.totalCost> 0){
                toastr.success('The total price is: '+ data.totalCost,'Total Price');
            }
        });
    }

    /**
     * The purpose of this function is empty the cart associated with the current session Id.
     * It uses the CartService which makes AJAX call to the server. if the operation is carry out successfully the user gets redirected to the index page
     * otherwise error message is displayed.
     */
    function emptyCart(){
        CartService.emptyCart().then(function (data) {
            //if the error string is empty then we redirect to the home page
            if(data.error.length <= 0){
                vm.cart = {};
                $state.go('index');
            }
            else{
                toastr.error('An error has occurred while attempting to empty the cart.');
            }
        });
    }


}