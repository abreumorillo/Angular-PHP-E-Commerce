/**
 * Purpose      : This controller provide the functionality of registering user to the system.
 * Date         : 3/14/2015
 * @author      : Neris Sandino Abreu.
 */
angular
    .module('app')
    .controller('RegisterController', RegisterController);

RegisterController.$inject = ['AuthService', '$state', '$rootScope'];

/* @ngInject */
function RegisterController(AuthService, $state, $rootScope) {
    /* jshint validthis: true */
    var vm = this;

    vm.user={};
    vm.resetForm = resetForm;
    vm.register = register;

    /**
     * This function is used to begin the registration process. It uses the underline Service to make ajax request to the server.
     */
    function register(){
        AuthService.register(vm.user).then(function (data) {
            if(data.error.length <=0){
                $rootScope.isAuthenticated = true;
                $rootScope.lastName = vm.user.lastName;
                $rootScope.firstName = vm.user.firstName;
                vm.user = {};
                $state.go('admin');
            }else{
                notifyError(data);
                $state.go('index');
            }
        });
    }

    /**
     * Reset the form field.
     */
    function resetForm(){
       vm.user={};
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