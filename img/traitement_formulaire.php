<?php
// Inclure la classe EmailSender
require_once 'send_email.php';

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
    $subject = isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : '';
    $message = isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '';

    // Valider l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Adresse email invalide. Veuillez retourner et corriger votre email.";
        exit;
    }

    // Définir l'adresse email de destination
    $to = "arnaudbarotteaux@gmail.com";

    // Construire l'email
    $email_subject = "Nouveau message de MielNaturel: " . $subject;

    $email_body = "Vous avez reçu un nouveau message depuis votre site MielNaturel.\n\n";
    $email_body .= "Détails du message:\n\n";
    $email_body .= "Nom: " . $name . "\n";
    $email_body .= "Email: " . $email . "\n";
    $email_body .= "Sujet: " . $subject . "\n";
    $email_body .= "Message: " . $message . "\n";

    // Utiliser la classe EmailSender pour envoyer l'email
    $emailSender = new EmailSender($to);
    $emailSender->setFrom($email, $name);
    $emailSender->setSubject($email_subject);
    $emailSender->setMessage($email_body);
    $emailSender->addHeader("X-Mailer: PHP/" . phpversion());

    // Tentative d'envoi de l'email
    if ($emailSender->send()) {
        // Enregistrer le message dans un fichier texte (sauvegarde)
        $file_path = "messages.txt";

        // Vérifier si le fichier existe, le créer s'il n'existe pas
        if (!file_exists($file_path)) {
            file_put_contents($file_path, "=== MESSAGES DE CONTACT ===\n\n");
        }

        $file = fopen($file_path, "a");
        fwrite($file, "Date: " . date("Y-m-d H:i:s") . "\n");
        fwrite($file, "Nom: " . $name . "\n");
        fwrite($file, "Email: " . $email . "\n");
        fwrite($file, "Sujet: " . $subject . "\n");
        fwrite($file, "Message: " . $message . "\n");
        fwrite($file, "------------------------------\n\n");
        fclose($file);

        // Rediriger vers une page de confirmation
        header("Location: index.html?message=success");
        exit;
    } else {
        // En cas d'échec de l'envoi de l'email
        echo "Une erreur s'est produite lors de l'envoi de votre message. Veuillez réessayer plus tard.";
    }
} else {
    // Si quelqu'un tente d'accéder directement à ce fichier
    header("Location: index.html");
    exit;
}
