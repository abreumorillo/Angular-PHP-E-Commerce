<?php
if(count(get_included_files()) ==1) exit("Direct access not permitted.");
/**
 * The purpose of this class is to provide functions to validate form fields
 *
 * @author Neris Sandino Abreu.
 */
class Validation {
    /*
     * Function to validate an email, this function use php predifined filter
     * more info about filters: http://php.net/manual/en/filter.filters.validate.php
     * @param $email to be validated
     * @return boolean
     */

    static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Function to determine if a field has a value.
     * @param string $field
     * @return boolean
     */
    static function isEmpty($field) {
        return empty($field);
    }

    /**
     * Determine if a field has a maximun length
     * @param string $field
     * @param int $max
     * @return boolean
     */
    static function hasMaximumLength($field, $max) {
        return strlen($field) <= $max;
    }

    /**
     * Determine if a field has a minimun length.
     * @param string $field
     * @param int $min 
     * @return boolean
     */
    static function hasMinumunLength($field, $min) {
        return strlen($field) >= $min;
    }
    
    /*
     * Validate is a phone number is valid
     * @param string $phone number
     * @return boolean
     */
    static function isValidPhone($phone) {
        return preg_match("/([\(]?(?<AreaCode>[0-9]{3})[\)]?)?[ \.\-]?(?<Exchange>[0-9]{3})[ \.\-](?<Number>[0-9]{4})/", $phone);
    }
    
    /*
     * Validate an area code
     * @param string $value with the area code to be evaluated
     * @return boolean
     */
    static function isValidAreaCode($value) {
        return is_numeric($value) && strlen($value) ==3;
    }
    
    /*
     * Determine if the selected option is valid
     * @param int $selectedValue
     * @return boolean
     */
    static function isValidSelectedOption($selectedValue) {
        return $selectedValue > 0;
    }

    static function validateRequiredField($data, $requiredFields){
        $errors = array();
        //cast the object to associative array
        if(is_object($data)){
            $data = (array)$data;
        }
        // Validations for required fields
        foreach($requiredFields as $field) {
            $value = trim($data[$field]);
            if (empty($value)) {
                $errors[$field] = ucfirst($field) . " is required.";
            }
        }
        return $errors;
    }

    /*
    Function to sanitize input data, the function apply htmlentities and trim spaces
    @param string
    @return mixed
*/
    static function sanitizeInput($field)
    {
        return htmlentities(trim($field));
    }

    /**
     * Verify an incoming string is valid
     * @param $input
     * @return mixed
     */
    static function filterString($input){
        return filter_var($input, FILTER_SANITIZE_STRING);
    }

}
