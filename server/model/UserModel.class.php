<?php
if(count(get_included_files()) ==1) exit("Direct access not permitted.");
/**
 * Created by PhpStorm.
 * User: sabreu
 * Date: 3/14/2015
 * Time: 9:12 AM
 */

class UserModel {
    public $userId;
    public $isAdmin;
    public $firstName;
    public $lastName;

    public function __construct($userId, $firstName, $lastName, $isAdmin){
        $this->userId = $userId;
        $this->isAdmin = $isAdmin;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }
}