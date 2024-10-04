<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendMailNewUserNoReplay extends Mailable
{
    use Queueable, SerializesModels;
    public $correo_new;
    public $nombre_new;
    public $password_new;
    /**
     * Create a new message instance.
     */
    public function __construct($nombre_new, $correo_new, $password_new)
    {
        $this->correo_new = $correo_new;
        $this->nombre_new = $nombre_new;
        $this->password_new = $password_new;
    }

    public function build()
    {
        $htmlContent = '
        <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Nueva Cuenta Plataforma Compras</title>
            </head>
            <body>
                <p>Estimado Usuario: ' . $this->nombre_new . '</p>
                <p>Se le comunica que se le ha dado de alta una cuenta en la platadorma <a href="https://compras.comprasesg.com.mx/">COMPRAS-ESG</a>.</p>
                <p>INFO</p>
                <p>Cuenta: ' . $this->correo_new . '</p>
                <p>Contraseña: ' . $this->password_new . '</p>
                <br>
                <p>No contestar este correo por favor.</p>
            </body>
            </html>
        ';
        $subject = "SISTEMA COMPRAS ESG - Nueva Cuenta";
        $email = $this->subject($subject)
            ->from('notreplay@esg.com.mx')
            ->to($this->correo_new);

        $email->cc(['soporte2@esg.com.mx']);

        return $email->html($htmlContent);
    }
}
