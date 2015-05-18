/**
 * Purpose      : This angular service provide the client side functionality to interact with the php cart script.
 *                all function here are promise based request, so all happen asynchronously.
 * Date         : 3/11/2015
 * @author      : Neris S. Abreu.
 */
angular
    .module('app')
    .factory('CartService', CartService);
//This inject functionality is useful for minification purpose
CartService.$inject = ['$http', '$q', 'appConfig', '$log'];

/* @ngInject */
function CartService($http, $q, appConfig, $log) {
    var service = {
        emptyCart: emptyCart,
        getCart: getCart,
        addToCart: addToCart
    };

    return service;

    //---- Service functions ---

    /**
     * This function uses the $http angular service to interact with the cart.php and execute the emptyCart function
     * @returns {d.promise|promise|m.ready.promise|fd.g.promise}
     */
    function emptyCart () {
        var url = appConfig.baseUrl + 'server/cart.php';
        var deferred = $q.defer();
        $http({
            method: 'GET',
            url: url,
            params: {action: 'emptyCart'}
        }).success(function (data) {
            deferred.resolve(data);
        }).error(function (data, status) {
            deferred.reject(status);
        });
        return deferred.promise;
    }

    /**
     * This function get the cart data to be displayed to the user. it invokes the getCart function in the cart.php
     * @returns {d.promise|promise|m.ready.promise|fd.g.promise}
     */
    function getCart(){
        var url = appConfig.baseUrl + 'server/cart.php';
        var deferred = $q.defer();
        $http({
            method: 'GET',
            url: url,
            params: {action: 'getCart'}
        }).success(function (data) {
            deferred.resolve(data);
        }).error(function (data, status) {
            deferred.reject(status);
        });
        return deferred.promise;
    }

    /**
     * Function to add products to the cart. It posts the product to php script for processing
     * @param data to be posted to the server.
     * @returns {d.promise|promise|m.ready.promise|fd.g.promise}
     */
    function addToCart(data){
        var postData = 'data='+JSON.stringify(data);
        var url = appConfig.baseUrl +'server/cart.php';
        var deferred = $q.defer();
        $http({
            method: 'POST',
            url: url,
            data: postData
        }).success(function (data) {
            deferred.resolve(data);
        }).error(function (data, status) {
            deferred.reject(status);
        });
        return deferred.promise;
    }

}

