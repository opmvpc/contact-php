<?php

$erreurs = validerDonnees();

// on indique que le contenu est du json
header('Content-type: application/json');

// si il y a des erreurs, on les renvois au client
if (count($erreurs) > 0) {
    // on encode les erreurs en json
    $erreurs = json_encode(['erreurs' => $erreurs]);
    // on renvois un code d'erreur 422 (Unprocessable Entity)
    http_response_code(422);
    echo $erreurs;
} else {
    // si il n'y a pas d'erreurs, on envois le mail
    envoyerMail();
    // on renvois un code 200 (OK)
    echo json_encode(['message' => 'Votre message a bien été envoyé.']);
}

// on arrête le script
exit;

function validerDonnees()
{
    $nom = sanitize($_POST['nom'] ?? null);
    $email = sanitize($_POST['email'] ?? null);
    $sujet = sanitize($_POST['sujet'] ?? null);
    $message = sanitize($_POST['message'] ?? null);

    $erreurs = [];
    $erreurs['nom'] = obligatoire($nom, 'nom');
    $erreurs['sujet'] = obligatoire($sujet, 'sujet');
    $erreurs['email'] = emailValide($email, 'email');
    $erreurs['message'] = obligatoire($message, 'message');

    // on supprime les erreurs vides
    $erreurs = array_filter($erreurs);

    return $erreurs;
}

function obligatoire($valeur, $nomDuChamps)
{
    if (is_null($valeur) || '' === trim($valeur)) {
        return "Le champs {$nomDuChamps} est obligatoire";
    }
}

function emailValide($valeur, $nomDuChamps)
{
    $erreur = obligatoire($valeur, 'email');
    if (null !== $erreur) {
        return $erreur;
    }

    if (!filter_var($valeur, FILTER_VALIDATE_EMAIL)) {
        return "Le champs {$nomDuChamps} n'est pas une adresse email valide";
    }
}

function envoyerMail()
{
    $nomSite = sanitize('Mon site');
    $nom = sanitize($_POST['nom']);
    $email = sanitize($_POST['email']);
    $sujet = sanitize($_POST['sujet']);
    $message = sanitize($_POST['message']);

    $from = 'test@example.com';
    $to = 'test@example.com';

    mail(
        $to,
        "[{$nomSite}] Prise de contact de {$nom}.",
        <<<EOT
    Nom : {$nom}
    Email : {$email}
    Sujet : {$sujet}

    Message :
    {$message}

    EOT
        ,
        [
            'From' => $from,
            'Reply-To' => $email,
        ]
    );
}

function sanitize($valeur)
{
    // on supprime les espaces en début et fin de chaine
    // et on converti les caractères spéciaux en entités html
    // pour éviter les failles XSS
    return htmlspecialchars(trim($valeur ?? ''));
}
