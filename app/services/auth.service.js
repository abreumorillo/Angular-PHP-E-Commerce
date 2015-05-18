/**
 * Purpose      : This service provide features related to authentication the security of this application is based on $_SESSION  $_COOKIE.
 * Date         : 3/14/2015
 * @author      : Neris S. Abreu.
 */
angular
    .module('app')
    .factory('AuthService', AuthService);

AuthService.$inject = ['$http', '$q', 'appConfig'];

/* @ngInject */
function AuthService($http, $q, appConfig) {
    var userData={};
    var service = {
        login: login,
        register: register,
        isAuthenticated: isAuthenticated,
        logOut: logOut
    };

    return service;

    ////////////////

    /**
     * This function post the user information to the PHP script for processing.
     * @param userInfo
     * @returns {d.promise|promise|m.ready.promise|fd.g.promise}
     */
    function login(userInfo) {
        userInfo.action = "login";
        var postData = 'data='+JSON.stringify(userInfo);
        var url = appConfig.baseUrl +'server/authentication.php'
        var deferred = $q.defer();
        $http({
            method: 'POST',
            url: url,
            data: postData
        }).success(function (data) {
            userData = data;
            deferred.resolve(data);
        }).error(function (data, status) {
            deferred.reject(status);
        });
        return deferred.promise;
    }

    /**
     * This function provide registration functionality to the application.
     * all user are registered as normal user.
     * @param userInfo
     * @returns {d.promise|promise|m.ready.promise|fd.g.promise}
     */
    function register(userInfo){
        userInfo.action = "register";
        var postData = 'data='+JSON.stringify(userInfo);
        var url = appConfig.baseUrl+'server/authentication.php';
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

    /**
     * Determine if a user is authenticated
     * @returns {*|$rootScope.isAuthenticated}
     */
    function isAuthenticated(){
        return userData.isAuthenticated;
    }

    /**
     * Provide logout funtionality to the application
     * @returns {d.promise|promise|m.ready.promise|fd.g.promise}
     */
    function logOut(){
        var url = appConfig.baseUrl+'server/authentication.php';
        var deferred = $q.defer();
        $http({
            method: 'GET',
            url: url,
            params: {action:'logOut'}
        }).success(function (data) {
            deferred.resolve(data);
        }).error(function (data, status) {
            deferred.reject(status);
        });
        return deferred.promise;
    }

}
