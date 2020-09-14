<?php
    /**
     * Config file for OliveWeb Outbound Mail module
     * 
     * @author Luke Bullard
     */

    //make sure we are included securely
    if (!defined("INPROCESS")) { header("HTTP/1.0 403 Forbidden"); exit(0); }

    //refer to https://github.com/PHPMailer/PHPMailer for additional details of the config items
    $mail_config = array(
        "preset1" => array(
            "hostname" => "", //separate main and backup server hostnames with a ;
            "username" => "",
            "password" => "",
            "port" => 587,
            "type" => "smtp",
            "smtpsecure" => "tls" //tls or ssl (or blank/omitted if not applicable)
        )
    );
?>