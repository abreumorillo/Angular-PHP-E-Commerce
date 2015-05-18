<?php

/**
 * This class provide some common helper functions. The functions are basically to expose some basic
 * php functions to render HTML.
 * The functions in this class depends on the bootstrap framework.
 * @author Neris Sandino Abreu
 */
class Helper {
    //Error types based on the bootstrap framework http://getbootstrap.com/components/#alerts
    const ALERT_ERROR = "danger";
    const ALERT_INFO = "info";
    const ALERT_WARNING = "warning";
    const ALERT_SUCCESS = "success";

    /**
     * Display error inside bootstrap alert component.
     * @param array $errors
     * @return string
     */
    static function displayError($errors = array()) {
        $output = '<div class="row">';
        $output .= '<div class = "col-sm-6">';
        $output .= '<div class = "alert alert-danger" role = "alert">';
        $output .= '<strong>Errors:</strong>';
        $output .= '<ul>';
        foreach ($errors as $error) {
            $output .= "<li>" . $error . "</li>";
        }
        $output .= '</ul>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
        return $output;
    }

    /**
     * Function to display message to the user about certain events in the application
     * the function is return some basic html markup to provide a bootstrap alert widget.
     * @param string $title
     * @param string $message
     * @param type $alert_type can be any of the alert type supported by bootstrap (info, success, warning, danger)
     * @return string bootstrap alert widget
     */
    static function displayMessage($title, $message, $alert_type) {
        $output = '<div class = "row">';
        $output .= '<div class = "col-sm-6">';
        $output .= '<div class = "alert alert-' . $alert_type . '"' . 'role = "alert">';
        $output .= '<strong>' . $title . '</strong><br /> <p class=\"text-justified\">' . $message . '</p>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
        return $output;
    }
    /*
     * Function used for redirecting.
     */
    static function redirectTo($new_location) {
        header("Location: " . $new_location);
        exit;
    }

}
