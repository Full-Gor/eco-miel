<?php

/**
 * Helper pour améliorer l'envoi d'emails
 * Cette classe offre une alternative à la fonction mail() native de PHP
 */
class EmailSender
{
    private $to;
    private $from_email;
    private $from_name;
    private $subject;
    private $message;
    private $headers = array();
    private $parameters;

    /**
     * Constructeur
     * @param string $to Adresse email du destinataire
     */
    public function __construct($to)
    {
        $this->to = $to;
        $this->headers[] = "MIME-Version: 1.0";
        $this->headers[] = "Content-type: text/plain; charset=UTF-8";
    }

    /**
     * Définir l'expéditeur
     * @param string $email Email de l'expéditeur
     * @param string $name  Nom de l'expéditeur (optionnel)
     */
    public function setFrom($email, $name = '')
    {
        $this->from_email = $email;
        $this->from_name = $name;

        if (!empty($name)) {
            $this->headers[] = "From: {$name} <{$email}>";
            $this->headers[] = "Reply-To: {$name} <{$email}>";
        } else {
            $this->headers[] = "From: {$email}";
            $this->headers[] = "Reply-To: {$email}";
        }
    }

    /**
     * Définir le sujet de l'email
     * @param string $subject Sujet de l'email
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Définir le contenu du message
     * @param string $message Contenu du message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Ajouter un en-tête personnalisé
     * @param string $header En-tête à ajouter
     */
    public function addHeader($header)
    {
        $this->headers[] = $header;
    }

    /**
     * Envoyer l'email
     * @return boolean Succès ou échec de l'envoi
     */
    public function send()
    {
        $headers = implode("\r\n", $this->headers);

        if (function_exists('mail')) {
            return mail($this->to, $this->subject, $this->message, $headers);
        } else {
            // Pour les environnements où mail() n'est pas disponible
            // On enregistre l'email dans un fichier à des fins de test
            $this->saveToFile();
            return true;
        }
    }

    /**
     * Enregistrer l'email dans un fichier (utile pour le débogage)
     */
    private function saveToFile()
    {
        $filename = 'emails/email_' . date('Y-m-d_H-i-s') . '.txt';

        // Créer le dossier emails s'il n'existe pas
        if (!file_exists('emails')) {
            mkdir('emails', 0777, true);
        }

        $content = "To: {$this->to}\r\n";
        $content .= "From: " . ($this->from_name ? "{$this->from_name} <{$this->from_email}>" : $this->from_email) . "\r\n";
        $content .= "Subject: {$this->subject}\r\n";
        $content .= "Headers: " . implode("\r\n", $this->headers) . "\r\n\r\n";
        $content .= "Message:\r\n{$this->message}";

        file_put_contents($filename, $content);
    }
}
