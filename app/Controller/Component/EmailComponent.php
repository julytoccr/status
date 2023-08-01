<?php
/**
 * EmailComponent for sending email through a SMTP server
 *
 * @author      rossoft
 * @version     0.1
 * @license		MIT
 * @package components
 *
 *
 *
 *
 * For sending with GMAIL
 * @link http://vulgarisoip.wordpress.com/2006/03/27/send-email-with-php-and-gmail-hosted-for-your-domain/
 */



/*define('PHPMAILER_SUBDIR','phpmailer' . DS);

vendor(PHPMAILER_SUBDIR. 'class.phpmailer');*/

define('LEMOSMAIL_SUBDIR','lemosmail' . DS);
App::import('Vendor', 'lemosmail/smtp');
App::import('Vendor', 'lemosmail/sasl');
require_once ROOT. DS . APP_DIR.DS.'Config'.DS.'mail.php';

class EmailComponent extends Object
{
    var $ctp;
    /**
     * Dirección de destino o bien array de direcciones de destino
     */
    var $to = null;
    var $controller;
    var $from = CONFIG_SMTP_EMAIL;
    var $subject = null;
    var $cc = null;
    var $bcc = null;
    var $isHTML=true; //false ->texto, true->html
    var $charset="utf-8";

	/**
	 * Variable que contiene el error que se ha producido si
	 * send() devuelve false
	 */
    var $error;

    function startup($controller)
    {
    	$this->controller = $controller;
	}

	/**
	 * Enviar el correo
	 * @return boolean Éxito. Si es falso, $this->error contiene el mensaje de error
	 */
    function send($message = null)
    {
       	return $this->_sendmail($this->to, $this->subject, ($message == null) ? $this->_message() : $message);
    }

    function send_html($alternative, $message = null)
    {
       	return $this->_sendhtmlmail($this->to, $this->subject, $alternative, ($message == null) ? $this->_message() : $message);
    }

	/**
	 * Función auxiliar de envio de email
	 * @param array $to Destinatarios. Cada elemento puede ser un dirección o un array nombre-dirección
	 * @param string $subject Asunto del mensaje
	 * @param string $mensaje El mensaje
	 * @param string $from Remitente.
	 */
	function _sendmail($to,$subject,$mensaje,$from='')
	{

		$from=CONFIG_SMTP_EMAIL;                           /* Change this to your address like "me@mydomain.com"; */ $sender_line=__LINE__;

		$smtp=new smtp_class;

		$smtp->host_name=CONFIG_SMTP_HOST;       /* Change this variable to the address of the SMTP server to relay, like "smtp.myisp.com" */
		$smtp->host_port=(int) CONFIG_SMTP_PORT;                /* Change this variable to the port of the SMTP server to use, like 465 */
		$smtp->ssl=CONFIG_USE_SSL;                       /* Change this variable if the SMTP server requires an secure connection using SSL */
		$smtp->start_tls=CONFIG_START_TLS;                 /* Change this variable if the SMTP server requires security by starting TLS during the connection */
		$smtp->localhost="localhost";       /* Your computer address */
		$smtp->direct_delivery=CONFIG_DIRECT_DELIVER;           /* Set to 1 to deliver directly to the recepient SMTP server */
		$smtp->timeout=CONFIG_SMTP_TIMEOUT;                  /* Set to the number of seconds wait for a successful connection to the SMTP server */
		$smtp->data_timeout=CONFIG_DATA_TIMEOUT;              /* Set to the number seconds wait for sending or retrieving data from the SMTP server.
						       Set to 0 to use the same defined in the timeout variable */
		$smtp->debug=0;                     /* Set to 1 to output the communication with the SMTP server */
		$smtp->html_debug=0;                /* Set to 1 to format the debug output as HTML */
		$smtp->pop3_auth_host=CONFIG_POP3_AUTHHOST;           /* Set to the POP3 authentication host if your SMTP server requires prior POP3 authentication */
		$smtp->user=CONFIG_SMTP_USER;                     /* Set to the user name if the server requires authetication */
		$smtp->realm=CONFIG_AUTH_REALM;                    /* Set to the authetication realm, usually the authentication user e-mail domain */
		$smtp->password=CONFIG_SMTP_PASS;                 /* Set to the authetication password */
		$smtp->workstation=CONFIG_NTLM_WORKSTATION;              /* Workstation name for NTLM authentication */
		$smtp->authentication_mechanism=CONFIG_SASL_MECHANISM; /* Specify a SASL authentication method like LOGIN, PLAIN, CRAM-MD5, NTLM, etc..
						       Leave it empty to make the class negotiate if necessary */
		if($smtp->SendMessage($from, array($to), array("From: $from", "To: $to", "Subject: =?UTF-8?Q?$subject?=", 
						"Date: ".strftime("%a, %d %b %Y %H:%M:%S %Z"), "MIME-Version: 1.0", "Content-type: text/plain; charset=UTF-8", "Content-Transfer-Encoding: quoted-printable"), $mensaje))
		{
			$this->error = "Message sent to $to OK.\n";
			$result = 1;
		}
		else
		{
			$this->error = "Cound not send the message to $to.\nError: ".$smtp->error."\n";
			$result = 0;
		}

		return $result;
		/*
		   $mail = new smtp_class;
		   $mail->PluginDir = VENDORS .PHPMAILER_SUBDIR ;
		   $mail->SetLanguage('es',VENDORS .PHPMAILER_SUBDIR . 'language/');
		   $mail->CharSet= $this->charset;

		$mail->IsSMTP();
		$mail->Host     = CONFIG_SMTP_HOST; // SMTP servers
		$mail->Port 	= (int) CONFIG_SMTP_PORT;
		$mail->SMTPAuth = true;     // turn on SMTP authentication
		$mail->Username = CONFIG_SMTP_USER;  // SMTP username
		$mail->Password = CONFIG_SMTP_PASS; // SMTP password
		if ($from =='') $mail->From = CONFIG_SMTP_EMAIL;
		else $mail->From = $from;

		$mail->FromName = '';
		if (is_array($to))
		{
			foreach ($to as $address)
			{				
				$mail->AddAddress($address);
			}
		}
		elseif ($to)
		{			
			$mail->AddAddress($to);
		}
		//$mail->WordWrap = 50;                              // set word wrap
		//$mail->AddAttachment("/var/tmp/file.tar.gz");      // attachment
		//$mail->AddAttachment("/tmp/image.jpg", "new.jpg");
		$mail->IsHTML($this->isHTML);                               // send as HTML

		$mail->Subject  =  $subject;
		$mail->Body     =  $mensaje;
		//$mail->AltBody  =  "This is the text-only body";
		$success=$mail->Send();
		$this->error=$mail->ErrorInfo;

		return $success;*/
	}

	/**
	 * Funció auxiliar d'enviament de email en format HTML i text pla
	 * @param array $to Destinatarios. Cada elemento puede ser un dirección o un array nombre-dirección
	 * @param string $subject Asunto del mensaje
         * @param string $alternative Missatge en format de text pla
	 * @param string $mensaje El mensaje
	 * @param string $from Remitente.
	 */
	function _sendhtmlmail($to,$subject,&$alternative,$mensaje,$from='')

	{
		$mensaje = str_replace("\r\n", "\n", $mensaje);
		$mensaje = str_replace("\n", "\r\n", $mensaje);

		$from=CONFIG_SMTP_EMAIL;                           /* Change this to your address like "me@mydomain.com"; */ $sender_line=__LINE__;

		$smtp=new smtp_class;

		$smtp->host_name=CONFIG_SMTP_HOST;       /* Change this variable to the address of the SMTP server to relay, like "smtp.myisp.com" */
		
		$smtp->host_port=(int) CONFIG_SMTP_PORT;                /* Change this variable to the port of the SMTP server to use, like 465 */
		$smtp->ssl=CONFIG_USE_SSL;                       /* Change this variable if the SMTP server requires an secure connection using SSL */
		$smtp->start_tls=CONFIG_START_TLS;                 /* Change this variable if the SMTP server requires security by starting TLS during the connection */
		$smtp->localhost="localhost";       /* Your computer address */
		$smtp->direct_delivery=CONFIG_DIRECT_DELIVER;           /* Set to 1 to deliver directly to the recepient SMTP server */
		$smtp->timeout=CONFIG_SMTP_TIMEOUT;                  /* Set to the number of seconds wait for a successful connection to the SMTP server */
		$smtp->data_timeout=CONFIG_DATA_TIMEOUT;              /* Set to the number seconds wait for sending or retrieving data from the SMTP server.
						       Set to 0 to use the same defined in the timeout variable */
		$smtp->debug=0;                     /* Set to 1 to output the communication with the SMTP server */
		$smtp->html_debug=0;                /* Set to 1 to format the debug output as HTML */
		$smtp->pop3_auth_host=CONFIG_POP3_AUTHHOST;           /* Set to the POP3 authentication host if your SMTP server requires prior POP3 authentication */
		$smtp->user=CONFIG_SMTP_USER;                     /* Set to the user name if the server requires authetication */
		$smtp->realm=CONFIG_AUTH_REALM;                    /* Set to the authetication realm, usually the authentication user e-mail domain */
		$smtp->password=CONFIG_SMTP_PASS;                 /* Set to the authetication password */
		$smtp->workstation=CONFIG_NTLM_WORKSTATION;              /* Workstation name for NTLM authentication */
		$smtp->authentication_mechanism=CONFIG_SASL_MECHANISM; /* Specify a SASL authentication method like LOGIN, PLAIN, CRAM-MD5, NTLM, etc..
						       Leave it empty to make the class negotiate if necessary */

                $headers = array("MIME-Version: 1.0",
                                 "From: $from", "To: $to", "Subject: =?UTF-8?Q?$subject?=", "Date: " . strftime("%a, %d %b %Y %H:%M:%S %Z"),
                                 "Content-Type: multipart/alternative; boundary = nextPart\r\n",
                                 "\n--nextPart",
                                 "Content-type: text/plain; charset=utf-8\r\n",
                                 $alternative,
                                 "\n--nextPart",
                                 "Content-type: text/html; charset=utf-8\r\n",
                                 $mensaje);

		if($smtp->SendMessage($from, array($to), $headers, ""))
		{
			$this->error = "Message sent to $to OK.\n";
			$result = 1;
		}
		else
		{
			$this->error = "Cound not send the message to $to.\nError: ".$smtp->error."\n";
			$result = 0;
		}

		return $result;
	}

    function _message()
    {
        ob_start();
        $layout_backup=$this->controller->autoLayout;
        $this->controller->autoLayout=false;
        $this->controller->render($this->ctp);
        $content= ob_get_clean();
        $this->controller->autoLayout=$layout_backup;
        return $content;
    }


}

?>
