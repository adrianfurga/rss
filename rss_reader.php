<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<?php
	// sprawdzenie czy jest przekazywany parametr i czy ma wartość
	if(!isset($_GET['param']) OR ($_GET['param']=='')){
		echo "Brak podanego parametru ?param=XXXXX gdzie XXXXX to szukana fraza w oposie posta.";
	} else {
		// określenie namespace by pobrać "content"
		$namespace = array('content' => 'http://purl.org/rss/1.0/modules/content/');
		// inicjalizacja sesji i zwrócenie uchwytu do $curl
        $curl = curl_init("http://xlab.pl/feed/");
		// ustawienie opcji połczenia curl
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		// wykonanie polaczenia i przypisany zwracanych danych do zmiennej $data
		$data = curl_exec($curl);
		// zamknięcie sesji i zwolnienie zasobów
		curl_close($curl);
		// utworzenie XML na podstawie danych zawartych w zmiennej $data
		$xml = new SimpleXmlElement($data, LIBXML_NOCDATA);
		// zliczenie ilości wystapień taga <item> na potrzeby pętli for
		$xml_item_count = count($xml->channel->item);
		// przypisanie zawartości parametru do zmiennej $find
		$find = $_GET['param'];
		// petla sprawdzajca czy w danym rekordzie w tagu <description> znajduje się tekst przekazany w parametrze
		// jeśli tekst zostaje znaleziony to poszczególne dane wpisu na blogu sa wypisywane na ekranie
		$found = 0;
		for($i=0; $i<$xml_item_count; $i++){
			if(stristr($xml->channel->item[$i]->description,$find)!==false){
				echo "<br>Tytuł: ".$xml->channel->item[$i]->title;
				echo "<br>Link: <a href=\"".$xml->channel->item[$i]->link."\" target=\"_blank\">".$xml->channel->item[$i]->title."</a>";
				echo "<br>Opis: ".$xml->channel->item[$i]->description;
				$content = $xml->channel->item[$i]->children($namespace['content']);
				echo "<br>Treść: ".$content->encoded;
				$found++;
			}
		}
		if ($found == 0){
			echo "<br>Niestety nie znaleziono wpisu zawierajacego w opisie podany parametr \"".$find."\"";
		}
	}
?>
</body>
</html>