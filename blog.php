<?php

error_reporting(E_ALL);

$sqlDsn = parse_url(getenv('CLEARDB_DATABASE_URL') ?: 'mysql://root:@localhost/clasament_2015');
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

function link_filtre($f = array()) {
  global $filters;

  $f = array_merge($filters, $f);

  return $_SERVER['SCRIPT_NAME'] . '?' . http_build_query($f);
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
// query pentru rezultate
$stmt = $db->prepare("SELECT sportiv.nume, sportiv.foto, clasament.echipa, sum(clasament.punctaj) as puncte, count(clasament.id_sportiv) as curse
                      FROM `clasament`, sportiv
                      WHERE clasament.id_sportiv = sportiv.id AND clasament.id_concurs IN (" . $filters['concursuri'] . ") AND sportiv.sex = :sex
                      GROUP BY id_sportiv
                      ORDER BY puncte DESC, nume ASC"
);
$stmt->execute(array('sex' => $filters['sex']));
$rezultate = $stmt->fetchAll(PDO::FETCH_ASSOC);


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
    <title>Clasament alergători montani</title>
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
		<h1 class="titlu" align="center">Alergători montani</h1>
			<div class="menu" align="center">
				<div id='cssmenu'>
					<ul>
					   <li class='active'><a href='index.php'><span>Clasament</span></a></li>
					   <li class='last'><a href='blog.php'><span>Povesti</span></a></li>
					</ul>
					</div>
			</div>
			
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
	<div class="postari">
		<div class="postareunu">
			<img src="parangu.jpg" width="320px" height="190px"/>
		</div>
		<div class="titlu">
			<h3> Parangu Night Chalange </h3>
			<h6>21 Ianuarie 2015 - de <h6 id="autor" color="white">Hajnal Robert </h6> </h6> 
		</div>
		<div class="postareunu">
			<img src="parang2.jpg" width="320px" height="190px"/>
		</div>
		<div class="titlu">
			<h3> Parangu Night Chalange </h3>
			<h6>21 Ianuarie 2015 - de <h6 id="autor" color="white">Silviu Brucan </h6> </h6> 
		</div>
		<div class="postaretrei">
			<img src="asault.jpg" width="640px" height="380px"/>
		<div class="titluasalt">
			<h3> Mountain Assault </h3>
			<h6>21 Ianuarie 2015 - de <h6 id="autorasalt" color="white">Silviu Brucan </h6> </h6> 
		</div>
		</div>
		<div class="postarepatru">
			<img src="postavaru.jpg" width="420px" height="290px"/>
		</div>
		<div class="postarecinci">
			<img src="alba.jpg" width="540px" height="290px"/>
		</div>
	</div>
	
	


  <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-3548847-13', 'auto');
    ga('send', 'pageview');
  </script>

 </body>

 </html>
