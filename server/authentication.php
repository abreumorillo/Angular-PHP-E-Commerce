<?php
    /**
     * Purpose          : This script is used for handling user login/logout and register
     * Date             : 3/14/2015
     * @author          : Neris S. Abreu
     */
    session_name('NSA-STORE');
    session_start();

    //Require the loader file which include the autoload
    require_once 'inc/loader.php';
    $errors = array();
    $requiredFields = array('userName', 'password');
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    //Determine what action to execute based on the request method.
    if($requestMethod ==='POST'){
        $jsonData = json_decode($_POST['data']);
        $action = filter_var(Validation::sanitizeInput($jsonData->action), FILTER_SANITIZE_STRING);
        $userName = filter_var(Validation::sanitizeInput($jsonData->userName), FILTER_SANITIZE_EMAIL);
        $password = Validation::sanitizeInput($jsonData->password);
        $errors = Validation::validateRequiredField($jsonData, $requiredFields);

        if(empty($errors)){

            switch($action){
                case "login":
                    login($userName, $password);
                    break;
                case "register":
                    $firstName = filter_var(Validation::sanitizeInput($jsonData->firstName), FILTER_SANITIZE_STRING);
                    $lastName = filter_var(Validation::sanitizeInput($jsonData->lastName), FILTER_SANITIZE_STRING);
                    register($userName, $password, $firstName, $lastName);
                    break;
            }

        }else{
            $errorResponse = array(
                'error'=> 'Error has occurred',
                'errors' => $errors
            );
            echo json_encode($errorResponse);
        }
    }else{
        //if action is GET --- LOGOUT -----
        $action = filter_var(Validation::sanitizeInput($_GET['action']), FILTER_SANITIZE_STRING);
        if($action==='logOut'){
            //verify if session exist
            if(isset($_SESSION['userId'])){
                echo session_name();
                //unset all the session variables
                session_unset();
                echo session_name();
                //verify if cookie associated with the session exits
                if(isset($_COOKIE[session_name()])){
                    //unset the cookie
                    setcookie(session_name(), "", 1, "/");
                }
                //Destroy the session
                session_destroy();
                $successResponse = array(
                    'error'=> '',
                    'message' => 'logout success',
                    'isAuthenticated'=>false
                );
                echo json_encode($successResponse);
            }
        }
    }

    /**
     * This function is used for authentication purpose. It sets $_SESSION some variable needed to identify a logged in user
     * @param $username
     * @param $password
     */
    function login($username, $password){
        $authRepository = new AuthRepository();
        $hashedPassword = sha1($password);
        $userModel = $authRepository->login($hashedPassword, $username);
        if($userModel->userId){
            $_SESSION['userId'] = $userModel->userId;
            $_SESSION['isAdmin'] = $userModel->isAdmin;
            $successResponse = array(
                'error'=> '',
                'message' => 'login success',
                'firstName' => $userModel->firstName,
                'lastName' => $userModel->lastName,
                'isAdmin' => $userModel->isAdmin,
                'isAuthenticated'=>true
            );
            echo json_encode($successResponse);
        }else{
            $errorResponse = array(
                'error'=> 'Unable to login',
                'errors' => array('Username or Password is invalid')
            );
            echo json_encode($errorResponse);
        }
    }

    /**
     * This function is used to register a user to the database as well as setting session variable in case the registration is succeeded
     * @param $userName
     * @param $password
     * @param $firstName
     * @param $lastName
     */
    function register($userName, $password, $firstName, $lastName) {
        $authRepository = new AuthRepository();
        $hashedPassword = sha1($password);
        $registerUserId = $authRepository->register($userName, $hashedPassword,$lastName, $firstName);
        if($registerUserId){
            $successResponse = array(
                'message'=>'User register',
                'error' => ''
            );
            $_SESSION['userId'] = $registerUserId;
            $_SESSION['isAdmin'] = true;
            echo json_encode($successResponse);
        }else{
            $errorResponse = array(
                'error'=> 'unable to register user',
                'errors' => array('Invalid data provided')
            );
            echo json_encode($errorResponse);
        }
    }