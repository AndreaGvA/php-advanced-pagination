<?
class _Paginazione {

	var $next, $prev, $separator, $fullsteps;

	function _Paginazione($next = ">", $prev = "<", $separator = " | ", $fullsteps = " ... ") {
		$this -> next = $next;
		$this -> prev = $prev;
		$this -> separator = $separator;
		$this -> fullsteps = $fullsteps;
	}

	function paginazione($max_righe, $query, $numlink, $method = "POST", $nome_tabella, $parametri = "", $count_where_clause = "1") {
		//Controllo la pagina scelta dall'utente
		if (isset($_REQUEST['page'])) {
			$pagina_attuale = $_REQUEST['page'] - 1;
		} else {
			$pagina_attuale = 0;
		}

		//Eseguo il conteggio del totale dei record
		$query_count = "SELECT count(*) as count from $nome_tabella WHERE $count_where_clause";
		$count_res = mysql_query($query_count) or die(mysql_error());
		$count_row = mysql_fetch_assoc($count_res);
		$count = $count_row['count'];
		// Trovo il totale delle pagine
		$tot_pages = ceil($count / $max_righe);
		// il numero totale di pagine

		// Trovo la riga di partenza della pagina attuale
		$rigaIniziale = $pagina_attuale * $max_righe;

		//Aggiungo il LIMIT per la paginazione alla query e la eseguo
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $rigaIniziale, $max_righe);
		$res = mysql_query($query_limit) or die(mysql_error());

		// Trovo la pagina da cui far partire la navigazione
		$PaginaDiPartenza = $pagina_attuale - $numlink;
		// Trovo la pagina con cui concludere la paginazione
		$PaginaDiChiusura = $pagina_attuale + $numlink;
		// Se il calcolo precedente porta ad una pagina precedente alla prima
		if ($PaginaDiPartenza < 0) {
			//imposto la prima pagina come pagina di partenza
			$PaginaDiPartenza = 0;
			// Ricalcolo la pagina di chiusura
			$PaginaDiChiusura = $numlink + $numlink;
		}
		//Se il totale delle pagine è inferiore al numero di link da generare
		if ($tot_pages > $numlink + $numlink + 1) {
			if ($PaginaDiPartenza > $tot_pages - $numlink - $numlink -1) {
				$PaginaDiPartenza = $tot_pages - $numlink - $numlink -1;
			}
		}

		// Imposto una paginazione di tipo diverso a seconda del METHOD scelto
		switch($method) {
			case 'GET' :
				// imposto il link per la pagina precedente
				switch($pagina_attuale) {
					case 0 :
						$previous = "<span>$this->prev</span>";
						break;
					case 1 :
						$previous = "<a href=\"" . $_SERVER['PHP_SELF'] . "?page=1&" . $parametri . "\" title=\"Vai alla pagina precedente\">" . $this -> prev . "</a>";
						break;
					default :
						$previouspage = $pagina_attuale;
						$previous = "<a href=\"" . $_SERVER['PHP_SELF'] . "?page=" . $previouspage . "&" . $parametri . "\" title=\"Vai alla pagina precedente\">" . $this -> prev . "</a>";
						break;
				}
				// IMPOSTO I NUMERI DI NAVIGAZIONE
				$num = "";
				//imposto il link alla prima pagina
				// se la pagina successiva è la seconda imposto il link col separatore
				if ($PaginaDiPartenza == $numlink - 1) {
					$num .= "<a href=\"" . $_SERVER['PHP_SELF'] . "?page=1&" . $parametri . "\" title=\"Vai alla prima pagina\">1</a>" . $this -> separator;
				}
				// se la pagina successiva è maggiore della seconda imposto il link coi ...
				if ($PaginaDiPartenza > $numlink - 1) {
					$num .= "<a href=\"" . $_SERVER['PHP_SELF'] . "?page=1&" . $parametri . "\" title=\"Vai alla prima pagina\">1</a>$this->fullsteps";
				}
				// Genero i link della navigazione con un ciclo for
				for ($p = $PaginaDiPartenza; $p <= $PaginaDiChiusura; $p++) {
					// Se sto stampando la pagina attuale inserisco il numero di pagina senza il link
					if ($p == $pagina_attuale) {
						$pagina = $pagina_attuale + 1;
						// Se la pagina è una sola non stampo niente, altrimenti stampo il numero
						if ($tot_pages > 1) {
							$num .= "<span>$pagina</span>";
						} else {
							$num = "";
						}
						// inserisco il separatore
						if ($pagina != $tot_pages && $pagina != $PaginaDiChiusura + 1) {
							$num .= $this -> separator;
						}
					}
					// Se sto stampando pagine diverse da quella attuale stampo i link
					if ($p != $pagina_attuale && $p < $tot_pages) {
						$pagina = $p + 1;
						if ($pagina > 0) {
							$num .= "<a href=\"" . $_SERVER['PHP_SELF'] . "?page=" . $pagina . "&" . $parametri . "\" title=\"Vai alla pagina " . $pagina . "\">" . $pagina . "</a>";
							// Inserisco il separatore
							if ($pagina != $tot_pages && $pagina != $PaginaDiChiusura + 1) {
								$num .= $this -> separator;
							}
						}
					}
				}
				//imposto il link all'ultima pagina
				if ($PaginaDiChiusura == $tot_pages - $numlink) {
					$num .= $this -> separator . "<a href=\"" . $_SERVER['PHP_SELF'] . "?page=" . $tot_pages . "&" . $parametri . "\" title=\"Vai all'ultima pagina\">" . $tot_pages . "</a>";
				}
				if ($PaginaDiChiusura < $tot_pages - $numlink) {
					$num .= "$this->fullsteps<a href=\"" . $_SERVER['PHP_SELF'] . "?page=" . $tot_pages . "&" . $parametri . "\" title=\"Vai all'ultima pagina\">" . $tot_pages . "</a>";
				}
				//Imposto il next
				if ($pagina_attuale == $tot_pages - 1 || $tot_pages == 0) {
					$next = "<span>$this->next</span>";
				} else {
					$nextpage = $pagina_attuale + 2;
					$next = "<a href=\"" . $_SERVER['PHP_SELF'] . "?page=" . $nextpage . "&" . $parametri . "\" title=\"Vai alla pagina successiva\"> " . $this -> next . "</a>";
				}
				// Compongo la barra di navigazione
				if ($tot_pages > 1) {
					$navigazione = $previous . $num . $next;
				} else { $navigazione = "";
				}

				// restituisco l'output
				return array($res, $navigazione, $count);
				break;
			case 'POST' :
				// imposto il link per la pagina precedente
				switch($pagina_attuale) {
					case 0 :
						$previous = "<span>$this->prev</span>";
						break;
					case 1 :
						$previous = "<a href=\"javascript:jumpToOffset(1)\" title=\"Vai alla pagina precedente\">" . $this -> prev . "</a>";
						break;
					default :
						$previouspage = $pagina_attuale;
						$previous = "<a href=\"javascript:jumpToOffset(" . $previouspage . ")\" title=\"Vai alla pagina precedente\">" . $this -> prev . "</a>";
						break;
				}
				// IMPOSTO I NUMERI DI NAVIGAZIONE
				$num = "";
				//imposto il link alla prima pagina
				// se la pagina successiva è la seconda imposto il link col separatore
				if ($PaginaDiPartenza == $numlink - 1) {
					$num .= "<a href=\"javascript:jumpToOffset(1)\" title=\"Vai alla prima pagina\">1</a>" . $this -> separator;
				}
				// se la pagina successiva è maggiore della seconda imposto il link coi ...
				if ($PaginaDiPartenza > $numlink - 1) {
					$num .= "<a href=\"javascript:jumpToOffset(1)\" title=\"Vai alla prima pagina\">1</a>$this->fullsteps";
				}
				// Genero i link della navigazione con un ciclo for
				for ($p = $PaginaDiPartenza; $p <= $PaginaDiChiusura; $p++) {
					// Se sto stampando la pagina attuale inserisco il numero di pagina senza il link
					if ($p == $pagina_attuale) {
						$pagina = $pagina_attuale + 1;
						// Se la pagina è una sola non stampo niente, altrimenti stampo il numero
						if ($tot_pages > 1) {
							$num .= "<span>$pagina</span>";
						} else {
							$num = "";
						}
						// inserisco il separatore
						if ($pagina != $tot_pages && $pagina != $PaginaDiChiusura + 1) {
							$num .= $this -> separator;
						}
					}
					// Se sto stampando pagine diverse da quella attuale stampo i link
					if ($p != $pagina_attuale && $p < $tot_pages) {
						$pagina = $p + 1;
						if ($pagina > 0) {
							$num .= "<a href=\"javascript:jumpToOffset(" . $pagina . ")\" title=\"Vai alla pagina " . $pagina . "\">" . $pagina . "</a>";
							// Inserisco il separatore
							if ($pagina != $tot_pages && $pagina != $PaginaDiChiusura + 1) {
								$num .= $this -> separator;
							}
						}
					}
				}
				//imposto il link all'ultima pagina
				if ($PaginaDiChiusura == $tot_pages - $numlink) {
					$num .= $this -> separator . "<a href=\"javascript:jumpToOffset(" . $tot_pages . ")\" title=\"Vai all'ultima pagina\">" . $tot_pages . "</a>";
				}
				if ($PaginaDiChiusura < $tot_pages - $numlink) {
					$num .= "$this->fullsteps<a href=\"javascript:jumpToOffset(" . $tot_pages . ")\" title=\"Vai all'ultima pagina\">" . $tot_pages . "</a>";
				}
				//Imposto il next
				if ($pagina_attuale == $tot_pages - 1 || $tot_pages == 0) {
					$next = "<span>$this->next</span>";
				} else {
					$nextpage = $pagina_attuale + 2;
					$next = "<a href=\"javascript:jumpToOffset(" . $nextpage . ")\" title=\"Vai alla pagina successiva\"> " . $this -> next . "</a>";
				}

				//preparo la form
				if ($parametri != "") {
					$par = explode("&", $parametri);
					$quanti = count($par);
					$par_form = " ";
					foreach ($par as $parametro) {
						$PAR = explode("=", $parametro);
						$par_form .= '<INPUT type="hidden" name="' . $PAR[0] . '" value="' . $PAR[1] . '">';
					}
				}
				$script = '<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
						   function jumpToOffset(offset) {
	 	 					   document.jumpForm.page.value=offset;
	 	 					   document.jumpForm.submit();
	 					   }
							</SCRIPT>';
				$form = '<FORM name="jumpForm" action="?" method="post">
	  					 	<INPUT type="hidden" name="page" value="">
						 	' . $par_form . '
						 </FORM>';

				// Compongo la barra di navigazione
				if ($tot_pages > 1) {
					$navigazione = $previous . $num . $next;
				} else { $navigazione = "";
				}
				//restituisco l'output
				return array($res, $navigazione, $count, $script, $form);

				break;
		}

	}

}


?>
