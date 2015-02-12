<?php

error_reporting(E_ALL); //gibt alle Fehlermeldungen zurück 

$db = new PDO('mysql:host=neu-mysql;dbname=ivf;charset=utf8', ion-shop', 'molimed8212peha') or die ('Fehler'); // new PDO ist die Schnittstelle  damit PHP auf Db zugreifen kann  
 
 $language ='DE'; //gib die Sprache Deutsch mit

if(isset($_POST['language'])) //issets prüft ob eine variable existiert und ob sie nicht null ist

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
                  prgr_beschreibung.sprache_iso='DE'
               AND
                  view_InternetKatalog.InternetKatalog>0
               GROUP BY
                  prgr_beschreibung.name
               ORDER BY
                  prgr_beschreibung.sortierung");

$productGroups = array(); //erzeuge eine variable und mach ein array.

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) //stmt = row (anzahl muss gleich sein)
{

$productGroups[] = $row, // die einzelnen rows werden in die Felder der variable productGroups geschrieben 

}

echo json_encode(array("data"=> $productGroups));

