<?php
if(count(get_included_files()) ==1) exit("Direct access not permitted.");
/**
* Purpose	: Class used to represent user object.
* Date      : 3/8/2015
* @author 	: Neris Sandino Abreu <nsa2741@rit.edu>
* @version 	: 1.0
*/
class User
{
	//Private field to represent the attribute of an user.
	private $_userId;
	private $_email;
	private $_firstName;
	private $_lastName;
	//Even though a user can have multiple roles assign for this app user will only have one role.
	private $_roles = array();
	
	/*
		Class Constructor
		@param $email string, represents the email of the user.
		@param $lastName string, represents the last name of the user.
		@param $firstName string, represents the first name of the user.
		@param $userId represents the Id of the user.
	 */
	function __construct($email="", $lastName="", $firstName="", $userId=0)
	{
		$this->_email = $email;
		$this->_lastName = $lastName;
		$this->_firstName = $firstName;
		//the userId is only set for update, when created this field is autoincrement.
		if ($userId > 0) {
			$this->_userId = $userId;
		}
	}
}