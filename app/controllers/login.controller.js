/**
 * Created by sabreu on 3/14/2015.
 */
angular
    .module('app')
    .controller('LoginController', LoginController);

LoginController.$inject = ['AuthService', '$state','toastr', '$rootScope'];

/* @ngInject */
function LoginController(AuthService,$state, toastr, $rootScope) {
    /* jshint validthis: true */
    var vm = this;

    vm.activate = activate;
    vm.login = login;
    vm.user = {};
    vm.errors = [];

    activate();
    /**
     * Function to provide logout feature
     */
    $rootScope.logOut = function(){
        AuthService.logOut().then(function (data) {
            $rootScope.isAuthenticated = data.isAuthenticated;
            $rootScope.lastName ='';
            $rootScope.firstName = '';
            $state.go('index');
        })
    }

    ////////////////$

    function activate() {
    }

    /**
     * Provide login functionality. This function relies on server data endpoint.
     */
    function login (){
        AuthService.login(vm.user).then(function (data) {
            $rootScope.isAuthenticated = data.isAuthenticated;
            $rootScope.lastName = data.lastName;
            $rootScope.firstName = data.firstName;
            if(data.error.length <=0){
                vm.user = {};
                $state.go('admin');
            }else{
                notifyError(data);
            }
        })
    }

    /**
     * Notify errors to the user using toaster component
     * @param data
     */
    function notifyError(data) {
        var errors = "";
        angular.forEach(data.errors, function (data) {
            vm.errors.push(data);
            errors += data + '<br/>';
        });
        toastr.error(errors, 'Errors had occurred');
    }

}
