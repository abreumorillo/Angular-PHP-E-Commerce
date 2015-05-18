/*
    Purpose		: This service provide the data access functionality for the index page. This angular service uses the index.php as a data endpoint.
                  In order to achieve the purpose we use an angular factory and the $http service which is wrapper
                  to the AJAX functionality. As part of the get request an action is passed to the server as a parameter.
                  The action specify which function to invoke in the php script.
    Date		: 3/10/2015
    @author		: Neris Sandino Abreu.
   */
   (function() {
     'use strict';
     angular.module('app')
      .factory('IndexService', IndexService);
     IndexService.$inject = ['$http', '$q', 'appConfig'];
    /* @ngInject */
        function IndexService($http, $q, appConfig) {
        var service = {
            getProductsOnSale: getProductsOnSale,
            getProductCatalog: getProductCatalog,
            countProduct: countProduct
        };
        return service;

        ////////////////
        /*
            Function to get the products on sale, it invokes the php script Index.php and execute the function
            getProductsOnSale.
            @return promise
         */
        function getProductsOnSale() {
            var url = appConfig.baseUrl+'server/index.php';
            var deferred = $q.defer();
            $http({
                method: 'GET',
                url: url,
                params: {action: 'getProductsOnSale'}
            }).success(function (data) {
                deferred.resolve(data);
            }).error(function (data, status) {
                deferred.reject(status);
            });
            return deferred.promise;
       }

        /*
            Function to get the catalog of products. it invokes the getProductCatalog function in the index.php
            @return promise
       */
       function getProductCatalog (page) {
           var url = appConfig.baseUrl+'server/index.php';
           var deferred = $q.defer();
           $http({
               method: 'GET',
               url: url,
               params:{action: 'getProductCatalog', page: page}
           }).success(function (data) {
               deferred.resolve(data);
           }).error(function (data, status) {
               deferred.reject(status);
           });
           return deferred.promise;
       }

        /**
         * This function is used to count the products in the database
          * @returns {d.promise|promise|m.ready.promise|fd.g.promise}
         */
       function countProduct(){
           var url = appConfig.baseUrl +'server/index.php';
           var deferred = $q.defer();
           $http({
               method: 'GET',
               url: url,
               params: {action: 'countProduct', isProductFromCatalog: true}
           }).success(function (data) {
               deferred.resolve(data);
           }).error(function (data, status) {
               deferred.reject(status);
           });
           return deferred.promise;
       }
 }
})();
