<?php

error_reporting(E_ALL);

$sqlDsn = parse_url(getenv('CLEARDB_DATABASE_URL') ?: 'mysql://root:meh@localhost/clasament_2015');
if (!isset($sqlDsn['pass'])) {
  $sqlDsn['pass'] = '';
}

try {
  $db = new PDO('mysql:host=' . $sqlDsn['host'] . ';dbname=' . ltrim($sqlDsn['path'], '/') . ';charset=utf8', $sqlDsn['user'], $sqlDsn['pass']);
} catch (Exception $e) {
  echo $e->getMessage();
}

$filters = array_merge(array(
  'sex'         =>  'M',
  'concursuri'  =>  '1'
), $_GET);

function link_filtre($f = array(), $file = null) {
  global $filters;

  if (!$file) {
    $file = $_SERVER['SCRIPT_NAME'];
  }

  $f = array_merge($filters, $f);

  return $file . '?' . http_build_query($f);
}

function topClasament($categorie) {
  global $db, $filters;
  $stmt = $db->prepare("SELECT sportiv.nume, sportiv.foto, clasament.echipa, sum(clasament.punctaj) as puncte
                        FROM `clasament`, sportiv, concurs
                        WHERE clasament.id_sportiv = sportiv.id AND clasament.id_concurs = concurs.id AND sportiv.sex = :sex AND concurs.categorie = :categorie
                        GROUP BY id_sportiv
                        ORDER BY puncte DESC, nume ASC
                        LIMIT 5"
  );
  $stmt->execute(array('sex' => $filters['sex'], 'categorie' => $categorie));
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

topClasament('Vertical');


// query concursuri
$stmt = $db->prepare("SELECT * from concurs");
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$listaConcursuri = array();

foreach ($results as $result) {
  $listaConcursuri[$result['categorie']][] = $result;
}



function getResults() {
  global $db, $filters;

  // query pentru rezultate
  $stmt = $db->prepare("SELECT sportiv.nume, sportiv.foto, clasament.echipa, sum(clasament.punctaj) as puncte, count(clasament.id_sportiv) as curse
                        FROM `clasament`, sportiv
                        WHERE clasament.id_sportiv = sportiv.id AND clasament.id_concurs IN (" . $filters['concursuri'] . ") AND sportiv.sex = :sex
                        GROUP BY id_sportiv
                        ORDER BY puncte DESC, nume ASC"
  );
  $stmt->execute(array('sex' => $filters['sex']));
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getArticles() {
  global $db, $filters;

  // query pentru rezultate
  $stmt = $db->prepare("SELECT *
                        FROM articole
                        WHERE id_concurs IN (" . $filters['concursuri'] . ")
                        "
  );
  $stmt->execute(array('sex' => $filters['sex']));
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


$concursuri = explode(',', $filters['concursuri']);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="styles.css">
    <script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
    <script src="script.js"></script>
    <title>Alergători montani</title>
    <meta property="fb:admins" content="100001286327621"/>
     </head>
 <body>
    <div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

    <div class="continut">
    <header>
        <h1 class="titlu" align="center">Clasament alergători montani</h1>

            
            <div class="menu" align="center">
              <div id='cssmenu'>
                <ul>
                   <li class="<?php if (substr_count($_SERVER['SCRIPT_NAME'], 'index.php')) echo 'active' ?>"><a href="<?php echo link_filtre(array(), 'index.php') ?>"><span>Clasament</span></a></li>
                   <li class="<?php if (substr_count($_SERVER['SCRIPT_NAME'], 'blog.php')) echo 'active' ?>"><a href="<?php echo link_filtre(array(), 'blog.php') ?>"><span>Povesti</span></a></li>
                </ul>
                </div>
            </div>
           

            <?php if (isset($chooseSex)) : ?>
              <div class="butoane">
                  <A class="masculin <?php if ($filters['sex'] == 'M') echo 'activ' ?>" href="<?php echo link_filtre(['sex' => 'M']) ?>">Masculin</A>
                  <a class="feminin <?php if ($filters['sex'] == 'F') echo 'activ' ?>" href="<?php echo link_filtre(['sex' => 'F']) ?>">Feminin</a>
              </div>
            <?php endif ?>
        </header>
    <div class="tipuri">
        <div class="selecteaza">
            <h1 align="center"> Selectează una sau mai multe curse</h1>
        </div>
            <div class="concursuri">

      <div id="accordion">
        <ul>

      <?php $i = 0; foreach ($listaConcursuri as $categorie => $concursuri2) : ?>
          <li>
            <a href="#c<?php echo $i ?>"><?php echo $categorie ?></a>
              <div id="c<?php echo $i++ ?>" class="accordion activ">
                <?php foreach ($concursuri2 as $concurs) : ?>
                  <label>
                    <input class="bifa1" type="checkbox" <?php if ($concurs['disabled']) echo 'disabled' ?> <?php if (in_array($concurs['id'], $concursuri)) echo 'checked' ?> name="option1" value="<?php echo $concurs['id'] ?>" /><?php echo $concurs['nume'] ?> <br />
                  </label>
                <?php endforeach ?>
              </div>
          </li>
      <?php endforeach ?>

                </ul>
        </div>
        <div class="publicitate">
            <div class="selecteazai">
                <h1 class="titlu" align="center"> Produsul săptămânii </h1>
            </div>
            <div class="reclama">
            <a href='//event.2parale.ro/events/click?ad_type=banner&aff_code=3bcf24b77&campaign_unique=1f126f6ec&unique=db0860426' target='_blank' rel='nofollow'><img src='//img.2parale.ro/system/paperclip/banner_pictures/pics/163939/original/163939.jpg' alt='Bodosport.ro' title='Bodosport.ro' border='0' /></a>
            </div>
        </div>
    </div>

        </div>
    </div>
    <div class="right">
        <h1> Top alergători general </h1>
    </div>
    <div class="topconcursuri" >
            <div id="topaccordion">
                <ul>

          <?php $i = 0; foreach ($listaConcursuri as $categorie => $concursuri2) : ?>
              <li>
                <a href="#v<?php echo $i ?>"><?php echo $categorie ?></a>
                  <div id="v<?php echo $i++ ?>" class="topaccordion">
                      <table class="tg">
                          <?php $j = 1; foreach (topClasament($categorie) as $rezultat) : ?>
                              <tr>
                                <th class="imagine">
                                  <img src="<?php echo $rezultat['foto'] ?>" align="center" alt="<?php echo $rezultat['nume'] ?>" >
                                </th>

                                <th class="tg-qwzm"><?php echo $j++ ?>.</th>
                                <th class="tg-qwzm"><?php echo $rezultat['nume'] ?></th>
                                <th class="tg-qwzm"><?php echo $rezultat['echipa'] ?></th>
                                <th class="tg-qwzm"><?php echo $rezultat['puncte'] ?>p</th>
                              </tr>
                          <?php endforeach ?>

                        </table>
                  </div>
              </li>
          <?php endforeach ?>

                </ul>
        </div>
    </div>


