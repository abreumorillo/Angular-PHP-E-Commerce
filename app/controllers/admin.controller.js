/**
 * Created by sabreu on 3/12/2015.
 */
angular
    .module('app')
    .controller('AdminController', AdminController);

AdminController.$inject = ['$log','FileUploader', 'AdminService', 'toastr', '$state'];

/* @ngInject */
function AdminController($log,FileUploader,AdminService, toastr, $state) {
    /* jshint validthis: true */
    var vm = this;
    //File uploader setting
    var uploader = vm.uploader = new FileUploader({
        url: 'server/processimage.php'
    });
    vm.errors = [];
    vm.isSaving = false;
    vm.activate = activate;
    vm.product ={};
    vm.products =[];
    vm.productToUpdate = {};

    vm.saveProduct = saveProduct;
    vm.selectProductToUpdate = selectProductToUpdate;
    vm.clearForm = clearForm;
    vm.isInvalid = isInvalid;
    activate();

    ////////////////

    /**
     * This is the start up function and it gets executed when this controller is loaded.
     */
    function activate() {
        vm.products =[];
        //Get all the product so we can edit
        AdminService.getProducts().then(function (data) {
            if(data.isAuthorize == undefined){
                vm.products = data;
            }else{
                $state.go('login');
            }

        })
    }

    /**
     * Function to save the current product (new or edit) to the database.
     */
    function saveProduct(form){
        //if the id of the product is 0 then we insert the product
        if(!vm.product.productId)
        {
            if(vm.product.salePrice>0){
                vm.product.isOnSale = true;
            }
            AdminService.saveProduct(vm.product).then(function (data) {
                if(data.productId> 0){
                    //upload the file
                    if(vm.uploader.queue.length > 0){
                        vm.uploader.queue[0].formData = [{"productId": data.productId}];
                        vm.uploader.queue[0].upload();
                    }
                    vm.product = {};
                    vm.errors =[];
                    form.$setPristine();
                    toastr.success('Product added to the database');
                    activate();
                } else{
                    notifyError(data);
                }
            })
        }else{
            //update the product
            AdminService.updateProduct(vm.product).then(function(data){
                if(data.message){
                    //upload the file
                    if(vm.uploader.queue.length > 0){
                        vm.uploader.queue[0].formData = [{"productId": vm.product.productId}];
                        vm.uploader.queue[0].upload();
                    }
                    vm.errors =[];
                    vm.product = {};
                    form.$setPristine();
                    toastr.success('Product updated successfully!');
                    activate();
                }else{
                    notifyError(data);
                }
            })
        }
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

    /**
     * This function is execute when the reset button is click, it just set the value of the product object to an empty object.
     */
    function clearForm(){
        vm.product = {};
        if(vm.uploader.queue.length > 0){
            vm.uploader.queue[0].remove();
        }
    }

    /**
     * This function is used for validation purpose. It evaluates if a given form element is dirty and invalid
     * @param formElement
     * @returns {rd.$dirty|*|dg.$dirty|$dirty|rd.$invalid|b.ctrl.$invalid} boolean
     */
    function isInvalid(formElement){
        return formElement.$dirty && formElement.$invalid;
    }

    /**
     * Based on the product selected product to be update, populate the fields in the form
     */
    function selectProductToUpdate(){
        vm.product.name = vm.productToUpdate.name;
        vm.product.description = vm.productToUpdate.description;
        vm.product.price = parseFloat(vm.productToUpdate.price);
        vm.product.salePrice = parseFloat(vm.productToUpdate.salePrice);
        vm.product.quantity = parseInt(vm.productToUpdate.quantity);
        vm.product.productId = parseInt(vm.productToUpdate.productId);
        vm.product.imageUrl = vm.productToUpdate.imageUrl
        if(vm.productToUpdate.salePrice > 0){
            vm.product.isOnSale = true;
        }
    }
    //Function executed after file has been uploaded, This is a callback function of the FileUploader library
    uploader.onSuccessItem = function(fileItem, response, status, headers) {
        if(vm.uploader.queue.length > 0){
            vm.uploader.queue[0].remove();
        }
    };
}
