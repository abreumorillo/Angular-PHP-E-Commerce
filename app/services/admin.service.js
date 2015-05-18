/**
 * Purpose          : This service is used to handle AJAX request to the server side script admin.php
 * Date             : 3/13/2015.
 * @author          : Neris S. Abreu
 */
angular
    .module('app')
    .factory('AdminService', AdminService);

AdminService.$inject = ['$http', '$q', 'appConfig'];

/* @ngInject */
function AdminService($http, $q, appConfig) {
    var service = {
        saveProduct : saveProduct,
        updateProduct: updateProduct,
        getProducts: getProducts
    };

    return service;

    ////////////////

    /**
     * This function is used to add new product to the database, it relies on XHR request to the server. It goes hand to hand
     * with admin.php which process this data in the server
     * @param product
     * @returns {d.promise|promise|m.ready.promise|fd.g.promise}
     */
    function saveProduct(product) {
        var url = appConfig.baseUrl + 'server/admin.php';
        var deferred = $q.defer();
        product.action = "addProduct";
        var postData = 'data='+JSON.stringify(product);
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

    /**
     * This function is used to update an existing product in the database.
     * @param product
     * @returns {d.promise|promise|m.ready.promise|fd.g.promise}
     */
    function updateProduct(product) {
        var url = appConfig.baseUrl + 'server/admin.php';
        var deferred = $q.defer();
        product.action = "updateProduct";
        var postData = 'data='+JSON.stringify(product);
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

    /**
     * This function is used to get all the existing products in the database.
     * @returns {d.promise|promise|m.ready.promise|fd.g.promise}
     */
    function getProducts(){
        var url = appConfig.baseUrl + 'server/admin.php';
        var deferred = $q.defer();
        $http({
            method: 'GET',
            url: url,
            params: {action: 'getProducts'}
        }).success(function (data) {
            deferred.resolve(data);
        }).error(function (data, status) {
            deferred.reject(status);
        })
        return deferred.promise;
    }


}

