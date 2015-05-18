<?php
	/*
		Purpose 	: The purpose of this scripts if to define the autoload which will take
					  care of loading classes from multiples directories.
		date 		: 3/9/2015
		@author     : Neris S. Abreu.
	 */
	
	/*
	 * Autoload needed classes.
	 */
	function __autoload($class_name) 
	{
	    //array of directories containing classes
	   	$directorys = array(
	   		'model/',
	   		'repositories/',
	   		);
	    //for each directory
	   	foreach($directorys as $directory)
	   	{
	        //see if the file exsists
	   		if(file_exists($directory.$class_name . '.class.php'))
	   		{
	   			require_once($directory.$class_name . '.class.php');
	                //only require the class once, so quit after to save effort (if you got more, then name them something else 
	   			return;
	   		}            
	   	}
	}