<?php
namespace App\Service;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Correo
{
    private $correodestino;
    private $htmlcuerp;
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }
 
    public function enviocorreo($correodestino,$htmlcuerpo){
        $destinatario = $correodestino["email"]; 
        $asunto = "Notificaciones del Sistema PAFAR"; 
        $urlApi = $this->params->get('urlapi');

        $logoUrl = $urlApi . 'images/Logo_CC_El_Recreo.jpg';
        $deloreanUrl = $urlApi . 'images/Delorean.webp';

        $cuerpo = '<!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Confirmación de Participación</title>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Mountains+of+Christmas:wght@700&display=swap" rel="stylesheet">
            <style>
                body {
                    margin: 0;
                    padding: 0;
                    background-color: #f2f2f2;
                    font-family: \'Inter\', Arial, sans-serif;
                }
                table, td {
                    mso-table-lspace: 0pt;
                    mso-table-rspace: 0pt;
                }
                img {
                    -ms-interpolation-mode: bicubic;
                    border: 0;
                }
            </style>
        </head>
        <body style="margin: 0; padding: 0; background-color: #f2f2f2;">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td style="padding: 20px 0;">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:100%; max-width:600px; margin:0 auto; background-color: #ffffff; border-radius: 12px;">
                            
                            <tr>
                                <td align="center" style="padding: 30px 20px 20px 20px;">
                                    <img src="' . $logoUrl . '" alt="Logo" width="80" style="display: block; width: 80px; border-radius: 50%;">
                                </td>
                            </tr>
                            
                            <tr>
                                <td style="padding: 0 20px;">
                                    <img src="' . $deloreanUrl . '" alt="Auto del Sorteo" width="560" style="display: block; width: 100%; max-width: 560px; height: auto; border-radius: 8px;">
                                </td>
                            </tr>

                            <tr>
                                <td style="padding: 30px 30px 20px 30px; text-align: center;">
                                    <h1 style="font-family: \'Mountains of Christmas\', cursive; font-size: 38px; color: #dc2626; margin: 0 0 15px 0;">¡Estás participando!</h1>
                                    <p style="font-size: 16px; color: #374151; line-height: 1.5; margin: 0;">
                                        ' . $htmlcuerpo . '
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 20px 30px 30px 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                                    <p style="font-size: 12px; color: #6b7280; margin: 0;">
                                        ¡Mucha suerte en el sorteo! 🎄✨
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>'; 

        //para el envío en formato HTML 
        $headers = "MIME-Version: 1.0\r\n"; 
        $headers .= "Content-type: text/html; charset=utf-8\r\n"; 
        //dirección del remitente 
        $headers .= "From: Pafar <admingiep@pafar.com.ve>\r\n"; 
        //direcciones que recibirán copia oculta 
        $headers .= "Bcc: sirsarmiento@gmail.com\r\n"; 

        mail($destinatario,$asunto,$cuerpo,$headers); 

    } 

    public function enviocorreoparfar($correodestino,$htmlcuerp){
            $destinatario = $correodestino["email"]; 
            $asunto = $correodestino["asunto"];
            $cuerpo = ' 
            <html> 
            <head> 
            <title>Sistema GIEP</title> 
            </head> 
            <body> 
            <p> 
            <b>'. $htmlcuerp .'</b>.  
            </p> 
            </body> 
            </html> 
            '; 
            //para el envío en formato HTML 
            $headers = "MIME-Version: 1.0\r\n"; 
            $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
            //dirección del remitente 
            $headers .= "From: ". $correodestino["nombre"] ." <admingiep@pafar.com.ve>\r\n"; 
            //direcciones que recibirán copia oculta 
            //$headers .= "Bcc: sirjcbg1@gmail.com\r\n"; 
            mail($destinatario,$asunto,$cuerpo,$headers); 

    } 
    
    
    public function enviocorreo_qr($correodestino,$htmlcuerp,$asunto){
        $destinatario = $correodestino["email"]; 
            //$asunto = utf8_decode("Nos vemos este lunes en la Simón Bolívar"); 
            $cuerpo = ' 
            <html> 
            <head> 
            <title>Sistema GIEP</title> 
            </head> 
            <body> 
            <p> 
            <b>'. $htmlcuerp .'</b>.  
            </p> 
            </body> 
            </html> 
            '; 
            //para el envío en formato HTML 
            //$headers = "MIME-Version: 1.0\r\n"; 
            //$headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 

            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: multipart/mixed; boundary=\"boundary\"\r\n";

            //dirección del remitente 
            $headers .= "From: Pafar <admingiep@pafar.com.ve>\r\n"; 
            //direcciones que recibirán copia oculta 
            //$headers .= "Bcc: sirjcbg1@gmail.com\r\n";
          
            //$file = "http://bofficegiepstage.pafar.com.ve/public/dowload/IMG-20240301-WA0090.jpg";
            //$file = "http://bofficegiepstage.pafar.com.ve/public/dowload/qrcorreo.png";
            $file = "http://bofficegiepstage.pafar.com.ve/public/qrreportes.png";

            $mensaje = "--boundary\r\n";
            $mensaje .= "Content-Type: text/html; charset='utf-8'";
            $mensaje .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $mensaje .= "$cuerpo\r\n\r\n";
            $mensaje .= "--boundary\r\n";
            $mensaje .= "Content-Type: image/png; name=\"qrreportes.png\"\r\n";
            $mensaje .= "Content-Transfer-Encoding: base64\r\n";
            $mensaje .= "Content-Disposition: attachment; filename=\"qrreportes.png\"\r\n\r\n";
            $mensaje .= chunk_split(base64_encode(file_get_contents($file))) . "\r\n";
            $mensaje .= "--boundary--";

            mail($destinatario,$asunto,$mensaje,$headers); 

    } 

    public function enviocorreo_calendar($correodestino,$htmlcuerp,$asunto){
        $destinatario = $correodestino["email"]; 
            //$asunto = utf8_decode("Nos vemos este lunes en la Simón Bolívar"); 
            $cuerpo = '  
            <html> 
            <head> 
            <title>Sistema GIEP</title> 
            </head> 
            <body> 
            <p> 
            <b>'. $htmlcuerp .'</b>.  
            </p> 
            </body> 
            </html> 
            '; 
            //para el envío en formato HTML 
            //$headers = "MIME-Version: 1.0\r\n"; 
            //$headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 

            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: multipart/mixed; boundary=\"boundary\"\r\n";

            //dirección del remitente 
            $headers .= "From: Pafar <admingiep@pafar.com.ve>\r\n"; 
            //direcciones que recibirán copia oculta 
            //$headers .= "Bcc: sirjcbg1@gmail.com\r\n";
          
            //$file = "http://bofficegiepstage.pafar.com.ve/public/dowload/IMG-20240301-WA0090.jpg";

            //$file = "http://bofficegiepstage.pafar.com.ve/public/qrreportes.png";

            $mensaje = "--boundary\r\n";
            $mensaje .= "Content-Type: text/html; charset='utf-8'";
            $mensaje .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $mensaje .= "$cuerpo\r\n\r\n";
            $mensaje .= "--boundary\r\n";
            //$mensaje .= "Content-Type: image/png; name=\"qrreportes.png\"\r\n";
            //$mensaje .= "Content-Transfer-Encoding: base64\r\n";
            //$mensaje .= "Content-Disposition: attachment; filename=\"qrreportes.png\"\r\n\r\n";
            //$mensaje .= chunk_split(base64_encode(file_get_contents($file))) . "\r\n";
            $mensaje .= "--boundary--";

            mail($destinatario,$asunto,$mensaje,$headers); 

    } 
    
    public function enviocorreoparfarcontactame($correodestino,$htmlcuerpo){
        $destinatario = "soporte@pafar.net"; //soporte@pafar.net
        $asunto = $correodestino["asunto"];
        $urlApi = $this->params->get('urlapi');

        $logoUrl = $urlApi . 'images/Logo_CC_El_Recreo.jpg';
        $deloreanUrl = $urlApi . 'images/Delorean.webp';

        $cuerpo = '<!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Confirmación de Participación</title>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Mountains+of+Christmas:wght@700&display=swap" rel="stylesheet">
            <style>
                body {
                    margin: 0;
                    padding: 0;
                    background-color: #f2f2f2;
                    font-family: \'Inter\', Arial, sans-serif;
                }
                table, td {
                    mso-table-lspace: 0pt;
                    mso-table-rspace: 0pt;
                }
                img {
                    -ms-interpolation-mode: bicubic;
                    border: 0;
                }
            </style>
        </head>
        <body style="margin: 0; padding: 0; background-color: #f2f2f2;">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td style="padding: 20px 0;">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:100%; max-width:600px; margin:0 auto; background-color: #ffffff; border-radius: 12px;">
                            
                            <tr>
                                <td align="center" style="padding: 30px 20px 20px 20px;">
                                    <img src="' . $logoUrl . '" alt="Logo" width="80" style="display: block; width: 80px; border-radius: 50%;">
                                </td>
                            </tr>
                            
                            <tr>
                                <td style="padding: 0 20px;">
                                    <img src="' . $deloreanUrl . '" alt="Auto del Sorteo" width="560" style="display: block; width: 100%; max-width: 560px; height: auto; border-radius: 8px;">
                                </td>
                            </tr>

                            <tr>
                                <td style="padding: 30px 30px 20px 30px; text-align: center;">
                                    <h1 style="font-family: \'Mountains of Christmas\', cursive; font-size: 38px; color: #dc2626; margin: 0 0 15px 0;">¡Estás participando!</h1>
                                    <p style="font-size: 16px; color: #374151; line-height: 1.5; margin: 0;">
                                        ' . $htmlcuerpo . '
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 20px 30px 30px 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                                    <p style="font-size: 12px; color: #6b7280; margin: 0;">
                                        ¡Mucha suerte en el sorteo! 🎄✨
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>'; 

        //para el envío en formato HTML 
        $headers = "MIME-Version: 1.0\r\n"; 
        $headers .= "Content-type: text/html; charset=utf-8\r\n"; 
        //dirección del remitente 
        $headers .= "From: Pafar <admingiep@pafar.com.ve>\r\n"; 
        //direcciones que recibirán copia oculta 
        $headers .= "Bcc: sirsarmiento@gmail.com\r\n"; 

        mail($destinatario,$asunto,$cuerpo,$headers); 

  } 
    
}