<?php
include('../connect/connect.php');

// Traitement du formulaire lors de la soumission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postData = $_POST;
    $fileData = $_FILES;

    // Vérification des champs obligatoires
    if (
        empty($postData['title'])
        || empty($postData['content'])
        || empty($postData['author'])
        || trim(strip_tags($postData['title'])) === ''
        || trim(strip_tags($postData['content'])) === ''
        || trim(strip_tags($postData['author'])) === ''
    ) {
        echo 'Il faut un titre, un contenu et un auteur pour soumettre le formulaire.';
    } else {
        $titre = trim(strip_tags($postData['title']));
        $contenu = trim(strip_tags($postData['content']));
        $auteur = trim(strip_tags($postData['author']));

        $imagePath = null;

        // Gestion de l'upload de l'image
        if (!empty($fileData['image']['name'])) {
            // Définition des extensions de fichiers autorisées
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            // Récupération de l'extension du fichier téléchargé
            $fileExtension = pathinfo($fileData['image']['name'], PATHINFO_EXTENSION);

            // Vérification si l'extension est autorisée
            if (in_array(strtolower($fileExtension), $allowedExtensions)) {
                // Définition du répertoire de destination pour l'upload
                $uploadDir = '../uploads/';
                // Création du répertoire s'il n'existe pas
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Génération d'un chemin unique pour l'image
                $imagePath = $uploadDir . uniqid() . '.' . $fileExtension;

                // Déplacement du fichier téléchargé vers le répertoire de destination
                if (!move_uploaded_file($fileData['image']['tmp_name'], $imagePath)) {
                    // Message d'erreur si l'upload échoue
                    echo 'Erreur lors de l’upload de l’image.';
                    $imagePath = null; // Réinitialisation du chemin de l'image en cas d'erreur
                }
            } else {
                // Message d'erreur si le format du fichier est invalide
                echo 'Le format du fichier est invalide. Formats autorisés : jpg, jpeg, png, gif.';
            }
        }

        // Insertion en base
        $insertcontenu = $mysqlClient->prepare('INSERT INTO s2_press_articles(title, content, author, date_publication, match_id, image) VALUES (:title, :content, :author, :date_publication, :match_id, :image)');
        $insertcontenu->execute([
            'title' => $titre,
            'content' => $contenu,
            'author' => $auteur,
            'date_publication' => date('Y-m-d'),
            'match_id' => 0, 
            'image' => $imagePath
        ]);

        echo '<div class="container alert-success"><h1>Article ajouté avec succès !</h1></div>';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un article</title>
</head>

<body class="d-flex flex-column min-vh-100">
    <div class="container">
        <h1>Ajouter un article</h1>
        <form action="add_article.html" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="author" class="form-label">Auteur de l'article</label>
                <input type="text" class="form-control" id="author" name="author">
            </div>
            <div class="mb-3">
                <label for="title" class="form-label">Titre de l'article</label>
                <input type="text" class="form-control" id="title" name="title" aria-describedby="titre-help">
                <div id="titre-help" class="form-text">Choisissez un titre percutant !</div>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Contenu de l'article</label>
                <textarea class="form-control" placeholder="Seulement du contenu vous appartenant ou libre de droits." id="content" name="content"></textarea>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image de l'article</label>
                <input type="file" class="form-control" id="image" name="image">
            </div>
            <button type="submit" class="btn btn-warning">Envoyer</button> <br>
            <a class="btn btn-warning" role="button" href="articles.html">Retour aux articles</a>
        </form>
    </div>
</body>

</html>
