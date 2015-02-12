<?php
error_reporting(E_ALL);
$db = new PDO('mysql:host=neu-mysql;dbname=ivf;charset=utf8', 'ion-shop', 'molimed8212peha') or die('Fehler');

$language = 'DE';

if(isset($_POST['language']))
{
	$language = $_POST['language'];
}

$stmt = $db->query("SELECT              
                  prgr_beschreibung.gr_fk,
                  prgr_beschreibung.name,
                  prgr_bilder.dateiname,
                  prgr_beschreibung.beschreibung,
                  view_InternetKatalog.InternetKatalog
               FROM
                  artikel
               LEFT JOIN
                  view_InternetKatalog
               ON
                  view_InternetKatalog.ar_fk = artikel.pk
               LEFT JOIN
                  sg_pr
               ON
                  sg_pr.pr_fk = artikel.pr_fk
               LEFT JOIN
                  gr_sg
               ON
                  sg_pr.sg_fk = gr_sg.sg_fk                
               LEFT JOIN
                  prgr_beschreibung
               ON
                  gr_sg.gr_fk = prgr_beschreibung.gr_fk
               LEFT JOIN
                  prgr_bilder
               ON
                  prgr_beschreibung.gr_fk=prgr_bilder.prgr_fk
               where
                  prgr_beschreibung.sprache_iso='".$language."'
               AND
                  view_InternetKatalog.InternetKatalog>0
               GROUP BY
                  prgr_beschreibung.name
               ORDER BY
                  prgr_beschreibung.sortierung");
$productGroups = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
{
	$row['subGroups'] = getSubGroups($row['gr_fk']);
    $productGroups[] = $row;
}

echo json_encode(array("data" => $productGroups));

function getSubgroups($gr_fk)
{
	global $db;
	global $language;
	
	$stmt = $db->query("SELECT
                  sg_beschreibung.name,
                  sg_bilder.dateiname,
                  sg_beschreibung.sg_fk,
                  sg_beschreibung.pk,
                  view_InternetKatalog.InternetKatalog
               FROM
                  artikel
               LEFT JOIN
                  view_InternetKatalog
               ON
                  view_InternetKatalog.ar_fk = artikel.pk
               LEFT JOIN
                  sg_pr
               ON
                  sg_pr.pr_fk = artikel.pr_fk
               LEFT JOIN
                  gr_sg
               ON
                  gr_sg.sg_fk = sg_pr.sg_fk
               LEFT JOIN
                  sg_bilder
               ON
                  sg_bilder.sg_fk = sg_pr.sg_fk
               LEFT JOIN
                  sg_beschreibung
               ON
                  sg_pr.sg_fk = sg_beschreibung.sg_fk
               WHERE
                  sg_beschreibung.sprache_iso='".$language."'
               AND
                  gr_sg.gr_fk='".$gr_fk."'
               AND
                  view_InternetKatalog.InternetKatalog>0
               GROUP BY
                  sg_beschreibung.name
               ORDER BY
                  sg_beschreibung.sortierung");
				  
	$subGroups = array();
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
	{
		$subGroups['products'] = getProducts($row['sg_fk']);
		$subGroups[] = $row;
	}
	return $subGroups;
}

function getProducts($sg_fk)
{
	global $db;
	global $language;
		
	$stmt = $db->query("SELECT
                  pr_beschreibung.name,
                  produkte.pk,
                  pr_beschreibung.pk as ppk,
                  pr_beschreibung.charakter_SL,
                  pr_bilder.dateiname,
                  view_InternetKatalog.InternetKatalog
               FROM
                  artikel
               LEFT JOIN
                  view_InternetKatalog
               ON
                  view_InternetKatalog.ar_fk = artikel.pk
               LEFT JOIN
                  produkte
               ON
                  produkte.pk = artikel.pr_fk
               LEFT JOIN
                  pr_beschreibung
               ON
                  produkte.pk = pr_beschreibung.pr_fk
               LEFT JOIN
                  pr_bilder
               ON
                  produkte.pk = pr_bilder.pr_fk
               LEFT JOIN
                  sg_pr
               ON
                  sg_pr.pr_fk = produkte.pk
               WHERE
                  sg_pr.sg_fk=".$sg_fk."
               AND
                  pr_beschreibung.sprache_iso='".$language."'
               AND
                  view_InternetKatalog.InternetKatalog>0
               GROUP BY
                  produkte.name
               ORDER BY
                  pr_beschreibung.sortierung");
	$products = array();
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
	{
		$products[] = $row;
	}
	return $products;
}