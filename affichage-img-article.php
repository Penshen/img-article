<?php
include('../connect/connect.php');

// Vérification et récupération de l'identifiant de l'article
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $articleId = $_GET['id'];

    // Requête pour récupérer les informations de l'article
    $sqlQuery = '
        SELECT ap.id, ap.title, ap.content, ap.date_publication, ap.image, rs.score, rs.lieu
        FROM s2_press_articles ap
        LEFT JOIN s2_sports_results rs ON ap.match_id = rs.id WHERE ap.id = :id';
    $statement = $mysqlClient->prepare($sqlQuery);
    $statement->bindParam(':id', $articleId, PDO::PARAM_INT);
    $statement->execute();
    $article = $statement->fetch();
}
?>


    <div class="container text-center d-flex flex-wrap justify-content-center mt-5">

        <?php if ($article) { ?>
            <div class="card col-9 m-5 p-3">
                <?php if (!empty($article['image'])) { ?>
                    <img src="<?php echo htmlspecialchars($article['image']); ?>" class="img-fluid rounded-top mb-2" alt="Image de l'article">
                <?php } else { ?>
                    <img src="assets/img/top14.webp" class="img-fluid rounded-top mb-2" alt="Image par défaut">
                <?php } ?>

                <h1><?php echo htmlspecialchars($article['title']); ?></h1>
                <p>Date de publication : <?php echo htmlspecialchars($article['date_publication']); ?></p>

                <?php if ($article['score']) { ?>
                    <strong style="color:#FF0000">Score : <?php echo htmlspecialchars($article['score']); ?></strong>
                <?php } ?>

                <?php if ($article['lieu']) { ?>
                    <p>Lieu : <?php echo htmlspecialchars($article['lieu']); ?></p>
                <?php } ?>

                <p><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
            </div>
        <?php } else { ?>
            <div class="card m-5 p-5">
                <p>Article non trouvé.</p>
            </div>
        <?php } ?>

        <div class="col-12">
            <a class="btn btn-warning" role="button" href="articles.html">Retour</a>
        </div>
    </div>
