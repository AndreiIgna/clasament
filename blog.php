
  <?php include '_header.php'; ?>


    <div class="main">

        <div class="articles">
            <?php foreach (getArticles() as $article) : ?>
                <a href="<?php echo $article['url'] ?>" target="_blank" class="article" style="background-image: url(<?php echo $article['image'] ?>)">
                    <h1><?php echo $article['title'] ?></h1>
                    <p><?php echo date('j F Y', strtotime($article['date'])) ?> - <?php echo $article['author'] ?></p>
                </a>
            <?php endforeach ?>
        </div>

        <div class="comentarii">
            <div align="center" class="fb-comments" data-href="http://clasament.roberthajnal.ro" data-numposts="10" data-colorscheme="light"></div>
        </div>
    </div>

  <?php include '_footer.php'; ?>
