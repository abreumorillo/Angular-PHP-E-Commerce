<?php
if(count(get_included_files()) ==1) exit("Direct access not permitted.");
/**
 * Purpose     : This c;ass is used to handle Authentication related tasks.
 *               This class inherits from BaseRepository which expose the base functionality to access the database
 * Date         : 3/14/2015
 * @author      : Neris S. Abreu
 */
//Require the loader file which include the autoload
require_once 'inc/loader.php';
class AuthRepository extends BaseRepository {

    /**
     * This constructor call the parent constructor in order to get the instance of the Database.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Function to perform login operation.
     * @param $hashedPassword
     * @param $userName
     * @return string|UserModel
     */
    function login($hashedPassword, $userName){
        $userModel= null;
        $query = "SELECT UserId, FirstName, LastName, IsAdmin FROM Users
                  WHERE Email = ? AND Password = ?";
        if($stmt = $this->mysqli->prepare($query)){
            $userName = $this->mysqli->real_escape_string($userName);
            $hashedPassword = $this->mysqli->real_escape_string($hashedPassword);
            $stmt->bind_param("ss", $userName, $hashedPassword);
            $stmt->bind_result($userId, $firtName, $lastName, $isAdmin);
            $stmt->execute();
            $stmt->store_result();
            while($stmt->fetch()){
                $userName = new UserModel($userId, $firtName, $lastName, $isAdmin);
            }
            $stmt->close();
        }
        return $userName;
    }

    /**
     * Function to register a user in the database
     * @param $userName
     * @param $hashedPassword
     * @param $lastName
     * @param $firstName
     * @return int id of the inserted user
     */
    function register($userName, $hashedPassword, $lastName, $firstName){
        $result = 0;
        $query = "INSERT INTO Users SET
                  FirstName = ?,
                  LastName = ?,
                  Email = ?,
                  Password = ? ";
        if($stmt = $this->mysqli->prepare($query)){
            $userName = $this->mysqli->real_escape_string($userName);
            $hashedPassword =  $this->mysqli->real_escape_string($hashedPassword);
            $firstName =  $this->mysqli->real_escape_string($firstName);
            $lastName =  $this->mysqli->real_escape_string($lastName);
            $stmt->bind_param("ssss", $firstName, $lastName, $userName, $hashedPassword);
            $stmt->execute();
            $stmt->store_result();
            $result = $stmt->insert_id;
            $stmt->close();
        }
        return $result;
    }

    /**
     * Verify if a user email is already register in the database. This is use to avoid duplicate username (email)
     * @param $userName is the email of the user.
     * @return int
     */
    function verifyUserExist($userName){
        $result = 0;
        $query = "SELECT COUNT(*) FROM Users WHERE Email = ?";
        if($stmt= $this->mysqli->prepare($query)){
            $userName =  $this->mysqli->real_escape_string($userName);
            $stmt->bind_param("s", $userName);
            $stmt->execute();
            $stmt->store_result();
            $result = $stmt->num_rows;
            $stmt->close();
        }
        return $result;
    }

}