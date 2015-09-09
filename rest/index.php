<?php
require 'flight/Flight.php';
require 'jsonindent.php';
Flight::register('db', 'Database', array('MovieDB'));
$json_podaci = file_get_contents("php://input");
Flight::set('json_podaci', $json_podaci );


Flight::route('/', function(){
    echo 'hello world!';
});

Flight::route('/test', function(){
    echo 'hello world!';
});

Flight::route('POST /novosti.json', function(){
	header ("Content-Type: application/json; charset=utf-8");
	$db = Flight::db();
	$db->select();
	$niz=array();
	while ($red=$db->getResult()->fetch_object()){
		$niz[] = $red;
	}
	//JSON_UNESCAPED_UNICODE parametar je uveden u PHP verziji 5.4
	//Omogućava Unicode enkodiranje JSON fajla
	//Bez ovog parametra, vrši se escape Unicode karaktera
	//Na primer, slovo č će biti \u010
	$json_niz = json_encode ($niz,JSON_UNESCAPED_UNICODE);
	echo indent($json_niz);
	return false;
});


Flight::route('POST /novosti', function(){
	header ("Content-Type: application/json; charset=utf-8");
	$podaci_json = Flight::get("json_podaci");
	echo $podaci_json;
});


Flight::route('POST /insert', function(){
	
	//header ("Content-Type: application/json; charset=utf-8");
	// get JSOn data
	$podaci_json = Flight::get("json_podaci");
	$podaci = json_decode ($podaci_json);

	$db = Flight::db();

	foreach ($podaci as $movie) {
 		 // check does movie with this id exist
		// TO - DO

		$db->insert("Movie", 
			"imdbID, imdbRating, Poster, Metascore , Language , Awards , Plot, Actors, Writer, Director, Genre , Runtime, Released , Year , Title",
			 array(	$movie->imdbID, 
			 		$movie->imdbRating,
			 		$movie->Poster,
			 		$movie->Metascore,
			 		$movie->Language,
			 		$movie->Awards, 
			 		$movie->Plot,
			 		$movie->Actors,
			 		$movie->Writer,
			 		$movie->Director,
			 		$movie->Genre,
			 		$movie->Runtime,
			 		$movie->Released,
			 		$movie->Year,
			 		$movie->Title));
 		 echo $movie->Rated;
	}	
	echo $podaci[0]['Rated'];

	return false;
});

Flight::route('GET /movies.json', function(){
	header ("Content-Type: text/html; charset=utf-8");
	$db = Flight::db();
	$db->select("Movie", "imdbID,Title,Poster", null, null, null, null, null);
	$niz=array();
	$i=0;
	while ($red=$db->getResult()->fetch_object()){
		
		$niz[$i]["imdbID"] = $red->imdbID;
		$niz[$i]["Title"] = $red->Title;
		$niz[$i]["Poster"] = $red->Poster;
		$i++;
	}
	//JSON_UNESCAPED_UNICODE parametar je uveden u PHP verziji 5.4
	//Omogućava Unicode enkodiranje JSON fajla
	//Bez ovog parametra, vrši se escape Unicode karaktera
	//Na primer, slovo č će biti \u010
	$json_niz = json_encode ($niz,JSON_UNESCAPED_UNICODE);
	echo indent($json_niz);
	return false;
});


Flight::route('GET /SendMail/@WhoList', function($WhoList){
	
	$htmlContent='<!DOCTYPE html>
<html>
<head>
<style type="text/css">
table {border-collapse:collapse;}
table, td, th {border:0px solid black;}
table, td, th {td font-family:"Arial Black",Gadget,sans-serif;}
table, td {font-size:95%;}
td {text-align:center;}
.image {
	height: 20px;
	width: 20px;	
}
</style>
</head><body text="Black">
<pre>
<p style="font-size:15px;">
  
-------------------------------------------
Here is the list of my movies. Take a look!
-------------------------------------------
</p></pre>
<table>';

	header ("Content-Type: application/json; charset=utf-8");
	$db = Flight::db();
	$db->select("Movie", "Title", null, null, null, null, null);
	while ($red=$db->getResult()->fetch_object()){
		$htmlContent.='<tr>
		<td>'.$red->Title.'</td>
		<td>
			<a title="Tweet this movie" href="http://twitter.com/share?text=I will watch '.$red->Title.'" target="_blank"><img class="image" src="http://www.simplesharebuttons.com/images/somacro/twitter.png" alt="Twitter" /></a> 
		</td>
		<td>
			<a href="http://www.facebook.com/sharer.php?[title]=I will watch '.$red->Title.'" target="_blank"><img class="image" src="http://www.simplesharebuttons.com/images/somacro/facebook.png" alt="Facebook" /></a> 
		</td>
		<td>
			<a href="mailto:?Subject=Movie&Body=Watch this movie!  '.$red->Title.'"><img class="image" src="http://www.simplesharebuttons.com/images/somacro/email.png" alt="Email" /></a>
		</td>
		<td>
			<a href="https://plus.google.com/share?url=http://www.simplesharebuttons.com" target="_blank"><img class="image" src="http://www.simplesharebuttons.com/images/somacro/google.png" alt="Google" /></a>
		</td>
	</tr>';

	}
	
	$htmlContent.='</table>
</p>
</body></html>';


	//JSON_UNESCAPED_UNICODE parametar je uveden u PHP verziji 5.4
	//Omogućava Unicode enkodiranje JSON fajla
	//Bez ovog parametra, vrši se escape Unicode karaktera
	//Na primer, slovo č će biti \u010
	echo $htmlContent;

	$myfile = fopen("/var/www/html/test.html", "w");
	fwrite($myfile, $htmlContent);
	fclose($myfile);

	$output = shell_exec(" sh /var/www/html/mail2.sh $WhoList");

	return false;
});


Flight::route('GET /movie/@id.json', function($id){
	header ("Content-Type: application/json; charset=utf-8");
	$db = Flight::db();
	$db->selectMovie("Movie", "*", $id );
	$niz=array();
	
	while ($red=$db->getResult()->fetch_object()){
		
		$niz["imdbID"] = $red->imdbID;
		$niz["Title"] = $red->Title;
		$niz["Poster"] = $red->Poster;
		$niz["imdbRating"] = $red->imdbRating;
		$niz["Metascore"] = $red->Metascore;
		$niz["Language"] = $red->Language;
		$niz["Awards"] = $red->Awards;
		$niz["Plot"] = $red->Plot;
		$niz["Actors"] = $red->Actors;
		$niz["Writer"] = $red->Writer;
		$niz["Director"] = $red->Director;
		$niz["Genre"] = $red->Genre;
		$niz["Runtime"] = $red->Runtime;
		$niz["Released"] = $red->Released;
		$niz["Year"] = $red->Year;
	}

	//JSON_UNESCAPED_UNICODE parametar je uveden u PHP verziji 5.4
	//Omogućava Unicode enkodiranje JSON fajla
	//Bez ovog parametra, vrši se escape Unicode karaktera
	//Na primer, slovo č će biti \u010
	$json_niz = json_encode ($niz,JSON_UNESCAPED_UNICODE);
	echo indent($json_niz);
	return false;
});



Flight::route('GET /SendSingleMail/@WhoList/@id', function($WhoList,$id){
	
	$htmlContent='<!DOCTYPE html>
<html>
<head>
<style type="text/css">
table {border-collapse:collapse;}
table, td, th {border:0px solid black;}
table, td, th {td font-family:"Arial Black",Gadget,sans-serif;}
table, td {font-size:95%;}
td {text-align:center;}
</style>
</head><body text="Black">
<pre>
<p style="font-size:15px;">
';

	header ("Content-Type: application/json; charset=utf-8");
	$db = Flight::db();
	$db->selectMovie("Movie", "*", $id );
	while ($red=$db->getResult()->fetch_object()){
		$htmlContent.='  
-------------------------------------------
'.$red->Title.'
-------------------------------------------
</p></pre>

	<img src="'.$red->Poster.'"/>
<ul>
	<li>IMDB Rating: '.$red->imdbRating.'</li>
	<li>Language: '.$red->Language.'</li>
	<li>Awards: '.$red->Awards.'</li>
	<li>Plot: '.$red->Plot.'</li>
	<li>Actors: '.$red->Actors.'</li>
	<li>Writer: '.$red->Writer.'</li>
	<li>Director: '.$red->Director.'</li>
	<li>Genre: '.$red->Genre.'</li>
	<li>Runtime: '.$red->Runtime.'</li>
	<li>Released: '.$red->Released.'</li>
	<li>Year: '.$red->Year.'</li>

	<table>
		<tr>
			<td>
				<a title="Tweet this movie" href="http://twitter.com/share?text=I will watch '.$red->Title.'" target="_blank"><img class="image" src="http://www.simplesharebuttons.com/images/somacro/twitter.png" alt="Twitter" /></a> 
			</td>
			<td>
				<a href="http://www.facebook.com/sharer.php?[title]=I will watch '.$red->Title.'" target="_blank"><img class="image" src="http://www.simplesharebuttons.com/images/somacro/facebook.png" alt="Facebook" /></a> 
			</td>
			<td>
				<a href="mailto:?Subject=Movie&Body=Watch this movie!  '.$red->Title.'"><img class="image" src="http://www.simplesharebuttons.com/images/somacro/email.png" alt="Email" /></a>
			</td>
			<td>
				<a href="https://plus.google.com/share?url=http://www.simplesharebuttons.com" target="_blank"><img class="image" src="http://www.simplesharebuttons.com/images/somacro/google.png" alt="Google" /></a>
			</td>
		</tr>
	<table>';

	}
	
	$htmlContent.='</ul>
</p>
</body></html>';


	//JSON_UNESCAPED_UNICODE parametar je uveden u PHP verziji 5.4
	//Omogućava Unicode enkodiranje JSON fajla
	//Bez ovog parametra, vrši se escape Unicode karaktera
	//Na primer, slovo č će biti \u010
	echo $htmlContent;

	$myfile = fopen("/var/www/html/test.html", "w");
	fwrite($myfile, $htmlContent);
	fclose($myfile);

	$output = shell_exec(" sh /var/www/html/mail2.sh $WhoList");

	return false;
});


Flight::route('POST /novosti', function(){
	header ("Content-Type: application/json; charset=utf-8");
	$db = Flight::db();
	$podaci_json = Flight::get("json_podaci");
	$podaci = json_decode ($podaci_json);
	if ($podaci == null){
	$odgovor["poruka"] = "Niste prosledili podatke";
	$json_odgovor = json_encode ($odgovor);
	echo $json_odgovor;
	return false;
	} else {
	if (!property_exists($podaci,'naslov')||!property_exists($podaci,'tekst')||!property_exists($podaci,'kategorija_id')){
			$odgovor["poruka"] = "Niste prosledili korektne podatke";
			$json_odgovor = json_encode ($odgovor,JSON_UNESCAPED_UNICODE);
			echo $json_odgovor;
			return false;
	
	} else {
			$podaci_query = array();
			foreach ($podaci as $k=>$v){
				$v = "'".$v."'";
				$podaci_query[$k] = $v;
			}
			if ($db->insert("novosti", "naslov, tekst, kategorija_id, datumvreme", array($podaci_query["naslov"], $podaci_query["tekst"], $podaci_query["kategorija_id"], 'NOW()'))){
				$odgovor["poruka"] = "Novost je uspešno ubačena";
				$json_odgovor = json_encode ($odgovor,JSON_UNESCAPED_UNICODE);
				echo $json_odgovor;
				return false;
			} else {
				$odgovor["poruka"] = "Došlo je do greške pri ubacivanju novosti";
				$json_odgovor = json_encode ($odgovor,JSON_UNESCAPED_UNICODE);
				echo $json_odgovor;
				return false;
			}
	}
	}	
	}
);
Flight::route('POST /kategorije', function(){
	header ("Content-Type: application/json; charset=utf-8");
	$db = Flight::db();
	$podaci_json = Flight::get("json_podaci");
	$podaci = json_decode ($podaci_json);
	if ($podaci == null){
	$odgovor["poruka"] = "Niste prosledili podatke";
	$json_odgovor = json_encode ($odgovor);
	echo $json_odgovor;
	} else {
	if (!property_exists($podaci,'kategorija')){
			$odgovor["poruka"] = "Niste prosledili korektne podatke";
			$json_odgovor = json_encode ($odgovor,JSON_UNESCAPED_UNICODE);
			echo $json_odgovor;
			return false;
	
	} else {
			$podaci_query = array();
			foreach ($podaci as $k=>$v){
				$v = "'".$v."'";
				$podaci_query[$k] = $v;
			}
			if ($db->insert("kategorije", "kategorija", array($podaci_query["kategorija"]))){
				$odgovor["poruka"] = "Kategorija je uspešno ubačena";
				$json_odgovor = json_encode ($odgovor,JSON_UNESCAPED_UNICODE);
				echo $json_odgovor;
				return false;
			} else {
				$odgovor["poruka"] = "Došlo je do greške pri ubacivanju novosti";
				$json_odgovor = json_encode ($odgovor,JSON_UNESCAPED_UNICODE);
				echo $json_odgovor;
				return false;
			}
	}
	}	


});
Flight::route('PUT /novosti/@id', function($id){
	header ("Content-Type: application/json; charset=utf-8");
	$db = Flight::db();
	$podaci_json = Flight::get("json_podaci");
	$podaci = json_decode ($podaci_json);
	if ($podaci == null){
	$odgovor["poruka"] = "Niste prosledili podatke";
	$json_odgovor = json_encode ($odgovor);
	echo $json_odgovor;
	} else {
	if (!property_exists($podaci,'naslov')||!property_exists($podaci,'tekst')||!property_exists($podaci,'kategorija_id')){
			$odgovor["poruka"] = "Niste prosledili korektne podatke";
			$json_odgovor = json_encode ($odgovor,JSON_UNESCAPED_UNICODE);
			echo $json_odgovor;
			return false;
	
	} else {
			$podaci_query = array();
			foreach ($podaci as $k=>$v){
				$v = "'".$v."'";
				$podaci_query[$k] = $v;
			}
			if ($db->update("novosti", $id, array('naslov','tekst','kategorija_id'),array($podaci->naslov, $podaci->tekst,$podaci->kategorija_id))){
				$odgovor["poruka"] = "Novost je uspešno izmenjena";
				$json_odgovor = json_encode ($odgovor,JSON_UNESCAPED_UNICODE);
				echo $json_odgovor;
				return false;
			} else {
				$odgovor["poruka"] = "Došlo je do greške pri izmeni novosti";
				$json_odgovor = json_encode ($odgovor,JSON_UNESCAPED_UNICODE);
				echo $json_odgovor;
				return false;
			}
	}
	}	




});
Flight::route('PUT /kategorije/@id', function($id){
	header ("Content-Type: application/json; charset=utf-8");
	$db = Flight::db();
	$podaci_json = Flight::get("json_podaci");
	$podaci = json_decode ($podaci_json);
	if ($podaci == null){
	$odgovor["poruka"] = "Niste prosledili podatke";
	$json_odgovor = json_encode ($odgovor);
	echo $json_odgovor;
	} else {
	if (!property_exists($podaci,'kategorija')){
			$odgovor["poruka"] = "Niste prosledili korektne podatke";
			$json_odgovor = json_encode ($odgovor,JSON_UNESCAPED_UNICODE);
			echo $json_odgovor;
			return false;
	
	} else {
			$podaci_query = array();
			foreach ($podaci as $k=>$v){
				$v = "'".$v."'";
				$podaci_query[$k] = $v;
			}
			if ($db->update("kategorije", $id, array('kategorija'),array($podaci->kategorija))){
				$odgovor["poruka"] = "Kategorija je uspešno izmenjena";
				$json_odgovor = json_encode ($odgovor,JSON_UNESCAPED_UNICODE);
				echo $json_odgovor;
				return false;
			} else {
				$odgovor["poruka"] = "Došlo je do greške pri izmeni kategorije";
				$json_odgovor = json_encode ($odgovor,JSON_UNESCAPED_UNICODE);
				echo $json_odgovor;
				return false;
			}
	}
	}	

});
Flight::route('DELETE /novosti/@id', function($id){
		header ("Content-Type: application/json; charset=utf-8");
		$db = Flight::db();
		if ($db->delete("novosti", array("id"),array($id))){
				$odgovor["poruka"] = "Novost je uspešno izbrisana";
				$json_odgovor = json_encode ($odgovor,JSON_UNESCAPED_UNICODE);
				echo $json_odgovor;
				return false;
		} else {
				$odgovor["poruka"] = "Došlo je do greške prilikom brisanja novosti";
				$json_odgovor = json_encode ($odgovor,JSON_UNESCAPED_UNICODE);
				echo $json_odgovor;
				return false;
		
		}		
				
});
Flight::route('DELETE /kategorije/@id', function($id){
		header ("Content-Type: application/json; charset=utf-8");
		$db = Flight::db();
		if ($db->delete("kategorije", array("id"),array($id))){
				$odgovor["poruka"] = "Kategorija je uspešno izbrisana";
				$json_odgovor = json_encode ($odgovor,JSON_UNESCAPED_UNICODE);
				echo $json_odgovor;
				return false;
		} else {
				$odgovor["poruka"] = "Došlo je do greške prilikom brisanja kategorije";
				$json_odgovor = json_encode ($odgovor,JSON_UNESCAPED_UNICODE);
				echo $json_odgovor;
				return false;
		
		}		


});


Flight::start();
?>
