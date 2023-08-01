<?php
	/**
	 * Configuración del cliente de SMTP
	 */

	//Email del remitente
	define('CONFIG_SMTP_EMAIL','estatus.eio@upc.edu');

	//Dirección del servidor SMTP
	define('CONFIG_SMTP_HOST','relay.upc.es');

	//Puerto del servidor SMTP. Normalmente 25
	define('CONFIG_SMTP_PORT', 25);

        //Host de autenticación POP3 (dejar vacío si no es necesario)
        define('CONFIG_POP3_AUTHHOST', '');

        //Authentication realm (normalmente el dominio de autenticación del correo del usuario, dejar vacío si no es necesario)
        define('CONFIG_AUTH_REALM', ''); 

        //Mecanismo de autenticación SASL(LOGIN, PLAIN, CRAM-MD5, NTLM... dejar vacío si no es necesario o para negociar)
        define('CONFIG_SASL_MECHANISM', '');

        //Nombre del terminal de trabajo para autenticación NTLM
        define('CONFIG_NTLM_WORKSTATION', '');

	//Usuario para conectar al servidor SMTP (dejar vacío si no es necesario)
	define('CONFIG_SMTP_USER', '');

	//Password para conectar al servidor SMTP (dejar vacío si no es necesario)
	define('CONFIG_SMTP_PASS', '');

        //Iniciar TLS durante la conexión
        define('CONFIG_START_TLS', 1);

        //Usar conexión segura SSL
        define('CONFIG_USE_SSL', 0);

        //Tiempo de espera para conectar al servidor SMTP, en segundos
        define('CONFIG_SMTP_TIMEOUT', 10);

        //Tiempo de espera para enviar o recibir datos, en segundos a/desde el servidor SMTP
        define('CONFIG_DATA_TIMEOUT', 0);        

        //Entregar directamente al servidor SMTP del receptor
        define('CONFIG_DIRECT_DELIVER', 0);

?>
