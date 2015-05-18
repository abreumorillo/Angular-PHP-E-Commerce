<?php
if(count(get_included_files()) ==1) exit("Direct access not permitted.");
/**
 * Purpose   : class to represent the diffrent roles a user can have.
 * Date      : 3/8/2015
 * @author 	: Neris Sandino Abreu <nsa2741@rit.edu>
 */
class Role
{
    //Private field which represent a role attributes.
    private $_roleId;
    private $_name;
    private $_description;

    /*
        Class constructor.
        @param $name string, represents the name of the role.
        @param $description string, represents the description of the role.
        @param $roleId int, represents the Id of the role.
     */
    function __construct($name="", $description="", $roleId=0)
    {
        $this->_name = $name;
        $this->_description = $description;
        //Only assign the value in case of update, roleId is autoincremented for new record.
        if ($roleId > 0) {
            $this->_roleId = $roleId;
        }
    }
}