<?php
    /**
     * Outbound Mail module for OliveWeb
     * 
     * @author Luke Bullard
     */

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    //make sure we are included securely
    if (!defined("INPROCESS")) { header("HTTP/1.0 403 Forbidden"); exit(0); }

    /**
     * The Outbound Mail OliveWeb Module
     */
    class MOD_outboundmail
    {
        protected $m_configs;

        public function __construct()
        {
            //load phpmailer
            require_once(dirname(__FILE__) . "/phpmailer/Exception.php");
            require_once(dirname(__FILE__) . "/phpmailer/PHPMailer.php");
            require_once(dirname(__FILE__) . "/phpmailer/SMTP.php");

            //get preset server configs
            $this->m_configs = array();
            include_once(dirname(__FILE__) . "/config.php");

            if (isset($mail_config))
            {
                foreach ($mail_config as $configName => $config)
                {
                    //if a config already exists for this name, skip
                    if (isset($this->m_configs[$configName]))
                    {
                        continue;
                    }

                    $hostname = (isset($config['hostname']) ? trim($config['hostname']) : "");
                    $type = (isset($config['type']) ? trim(strtolower($config['type'])) : "");
                    $port = (isset($config['port']) ? intval($config['port']) : -1);
                    $username = (isset($config['username']) ? $config['username'] : "");
                    $password = (isset($config['password']) ? $config['password'] : "");
                    $smtpsecure = (isset($config['smtpsecure']) ? trim(strtolower($config['smtpsecure'])) : "");
                    
                    //if config parameters invalid, skip
                    if ($hostname == "")
                    {
                        continue;
                    }

                    //set defaults on non-essential parameters
                    if ($type == "")
                    {
                        $type = "smtp";
                    }

                    //add the config to the list
                    $this->m_configs[$configName] = array(
                        "hostname" => $hostname,
                        "type" => $type,
                        "port" => $port,
                        "username" => $username,
                        "password" => $password,
                        "smtpsecure" => $smtpsecure
                    );
                }
            }
        }

        /**
         * Creates a PHPMailer object with config options found in config.php
         * 
         * @param String $a_configName The name of the config to use.
         * @return PHPMailer The created PHPMailer object.
         * @throws Exception When the PHPMailer object could not be created or
         *  the specified config does not exist.
         */
        public function getMailer($a_configName)
        {
            //make sure the config exists
            if (!isset($this->m_configs[$a_configName]))
            {
                throw new Exception("Outbound mail config not found!");
            }

            $config = $this->m_configs[$a_configName];

            try {
                $mail = new PHPMailer(true);

                //if SMTP, configure mailer for smtp and (optional) smtp auth
                if ($config['type'] == "smtp")
                {
                    $mail->isSMTP();
                    if ($config['username'] != "")
                    {
                        $mail->SMTPAuth = true;
                        $mail->Username = $config['username'];
                        $mail->Password = $config['password'];
                    }

                    if ($config['smtpsecure'])
                    {
                        $mail->SMTPSecure = $config['smtpsecure'];
                    }
                }

                $mail->Host = $config['hostname'];
                $mail->Port = $config['port'];
            } catch (Exception $e)
            {
                throw $e;
            }

            return $mail;
        }
    }
?>