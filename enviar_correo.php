<?php


	$mail_header = "MIME-Version: 1.0\r\n";
	$mail_header .= "Content-type: text/html; charset=utf-8\r\n";
	$mail_header .= "From: Rangers Modelbrush <no-reply@modelbrush.com>\r\n";
	$mail_header .= "Reply-To: Rangers Modelbrush <rangers@modelbrush.com>\r\n";
	$mail_header .= "Bcc: rangers@modelbrush.com\r\n";

	mail("nagash87@gmail.com", "hola", "Hola :)", $mail_header);

?>