<?php

$erreurs = validerDonnees();

if (count($erreurs) > 0) {
    $erreurs = json_encode(["erreurs" => $erreurs]);
    http_response_code(422);
    echo $erreurs;
} else {
    mail('thibsix@outlook.be', "Prise de contact de {$_POST['nom']}.", $_POST['message'],[
        'From' => $_POST['email'],
        'Reply-To' => $_POST['email'],
    ]);
    echo json_encode(["message" => "ok"]);
}

// validation des données
// regles:
//  - Nom obligatoire
//  - Email obligatoire (+ format valide)
//  - Message obligatoire
function validerDonnees() {
    $nom= $_POST['nom'];
    $email= $_POST['email'];
    $sujet= $_POST['sujet'];
    $message= $_POST['message'];
    
    $erreurs = [];
    $erreurs = obligatoire($nom, 'nom', $erreurs);
    $erreurs = obligatoire($sujet, 'sujet', $erreurs);
    $erreurs = emailValide($email, 'email', $erreurs);
    $erreurs = obligatoire($email, 'email', $erreurs);
    $erreurs = obligatoire($message, 'message', $erreurs);
    
    return $erreurs;
}

function obligatoire($valeur, $nomDuChamps, $erreurs) {
    if (!isset($valeur)|| trim($valeur) === "") {
        $erreurs[$nomDuChamps] = "Le champs {$nomDuChamps} est obligatoire";
    }
    return $erreurs;
}

function emailValide($valeur, $nomDuChamps, $erreurs) {
    if (!filter_var($valeur, FILTER_VALIDATE_EMAIL)) {
    $erreurs[$nomDuChamps] = "Le champs {$nomDuChamps} n'est pas une adresse email valide";
    }
    return $erreurs;
}