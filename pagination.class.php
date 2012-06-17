<?
class _Pagination
{	
	var $next, $prev, $separator, $fullsteps;
	var $ajax_url, $ajax_res;
	
	function __construct(){
		$this->next=">";
		$this->prev="<";
		$this->separator=" | ";
		$this->fullsteps=" ... ";
	}
	
	function set_arrows($next=">", $prev="<", $separator = " | ", $fullsteps = " ... "){
		$this->next=$next;
		$this->prev=$prev;
		$this->separator=$separator;
		$this->fullsteps=$fullsteps;
	}
	
	function setAjaxFields($ajax_url="", $ajax_res=""){
		$this->ajax_url=$ajax_url;
		$this->ajax_res=$ajax_res;
	}
	
	function paginazione ($maxrighe, $testo_query, $numlink, $method="POST", $parametri="", $pagina_start = "")
    {
        $max_righe = $maxrighe;
        $query = $testo_query;
        if (isset($_POST['page'])) {
            $pagina_iniziale = $_POST['page'] - 1;
        } else if(isset($_GET['page'])) {
            $pagina_iniziale = $_GET['page'] - 1;
        } else {
            if ($pagina_start != "") {
                $pagina_iniziale = $pagina_start - 1;
            } else {
                $pagina_iniziale = 0;
            }
        }
        $rigaIniziale = $pagina_iniziale * $max_righe; // Da quale riga inizio a prendere i dati sul db?
        $query_limit = sprintf("%s LIMIT %d, %d", $query, $rigaIniziale, $max_righe);
        $Articoli = mysql_query($query_limit) or die (mysql_error());
        $all_Articoli = mysql_query($query);
        $totRighe = mysql_num_rows($all_Articoli);
        $tot_pages = ceil($totRighe / $max_righe); // il numero totale di pagine
        //configuro link paginazione
        $editFormAction = "?";
        if (isset($_SERVER['QUERY_STRING'])) {
            $editFormAction .= $_SERVER['QUERY_STRING'];
        }
        // Imposto il link PAGINA PRECEDENTE
        if ($pagina_iniziale == 0) {
            $previous = "";
        } else 
            if ($pagina_iniziale == 1) {
				if ($method=="POST" || $method=="ajax")
				{
                	$previous = "<a href=\"javascript:jumpToOffset(1)\" title=\"Vai alla pagina precedente\"> ".$this->prev." </a>";
				} else
				if ($method=="GET")
				{
                	$previous = "<a href=\"".$_SERVER['PHP_SELF']."?page=1&".$parametri."\" title=\"Vai alla pagina precedente\"> ".$this->prev." </a>";
				}
            } else {
                $previouspage = $pagina_iniziale;
				if ($method=="POST" || $method=="ajax")
				{
                	$previous = "<a href=\"javascript:jumpToOffset(" . $previouspage . ")\" title=\"Vai alla pagina precedente\"> ".$this->prev." </a>";
				} else
				if ($method=="GET")
				{
                	$previous = "<a href=\"".$_SERVER['PHP_SELF']."?page=".$previouspage."&".$parametri."\" title=\"Vai alla pagina precedente\"> ".$this->prev." </a>";
				}
                
            }
        $num = "";
		
        $PaginaDiPartenza = $pagina_iniziale - $numlink;
        $UltimoLink = $pagina_iniziale + $numlink;
        if ($PaginaDiPartenza < 0) {
            $UltimoLink = $numlink + $numlink;
            $PaginaDiPartenza = 0;
        }
        if ($tot_pages > numlink + $numlink + 1) {
            if ($PaginaDiPartenza > $tot_pages - $numlink - $numlink - 1) {
                $PaginaDiPartenza = $tot_pages - $numlink - $numlink - 1;
            }
        }
		
		
		
        //imposto il link alla prima pagina
        if ($PaginaDiPartenza == $numlink - 1) {
			if ($method=="POST" || $method=="ajax")
				{
                	$num .= "<a href=\"javascript:jumpToOffset(1)\" title=\"Vai alla prima pagina\">1</a>" . $this->separator;
				} else
				if ($method=="GET")
				{
                	$num.= "<a href=\"".$_SERVER['PHP_SELF']."?page=1&".$parametri."\" title=\"Vai alla prima pagina\">1</a>". $this->separator;
				}
            
        }
        if ($PaginaDiPartenza > $numlink - 1) {
            if ($method=="POST" || $method=="ajax")
				{
                	$num .= "<a href=\"javascript:jumpToOffset(1)\" title=\"Vai alla prima pagina\">1</a>" . $this->fullsteps;
				} else
				if ($method=="GET")
				{
                	$num.= "<a href=\"".$_SERVER['PHP_SELF']."?page=1&".$parametri."\" title=\"Vai alla prima pagina\">1</a>" . $this->fullsteps;
				};
        }
        //Se il totale delle pagine è uguale a 0
        for ($p = $PaginaDiPartenza; $p <= $UltimoLink; $p ++) {
            if ($p == $pagina_iniziale) {
                $pagina = $pagina_iniziale + 1;
                if ($tot_pages > 1) {
                    $num .= "$pagina";
                } else {
                    $num = "";
                }
                if ($pagina != $tot_pages && $pagina != $UltimoLink + 1) {
                    $num .= $this->separator;
                }
            }
            if ($p != $pagina_iniziale && $p < $tot_pages) {
                $pagina = $p + 1;
                if ($pagina>0) 
				{
                	 if ($method=="POST" || $method=="ajax")
					{
                		$num .= "<a href=\"javascript:jumpToOffset(" . $pagina . ")\" title=\"Vai alla pagina " . $pagina . "\">" . $pagina . "</a>";
					} else
					if ($method=="GET")
					{
                		$num.= "<a href=\"".$_SERVER['PHP_SELF']."?page=".$pagina."&".$parametri."\" title=\"Vai alla pagina " . $pagina . "\">" . $pagina . "</a>";
					};
					
					if ($pagina != $tot_pages && $pagina != $UltimoLink + 1) 
					{
                    	$num .= $this->separator;
                	}
				}
            }
        }
        //imposto il link all'ultima pagina
        if ($UltimoLink == $tot_pages - $numlink) {
			 if ($method=="POST" || $method=="ajax")
					{
                		$num .= $this->separator . "<a href=\"javascript:jumpToOffset(" . $tot_pages . ")\" title=\"Vai all'ultima pagina\">" . $tot_pages . "</a>";
					} else
					if ($method=="GET")
					{
                		$num .= $this->separator . "<a href=\"".$_SERVER['PHP_SELF']."?page=".$tot_pages."&".$parametri."\" title=\"Vai all'ultima pagina\">" . $tot_pages . "</a>";
					};
            
        }
        if ($UltimoLink < $tot_pages - $numlink) {
			 if ($method=="POST" || $method=="ajax")
					{
                		$num .= $this->fullsteps . "<a href=\"javascript:jumpToOffset(" . $tot_pages . ")\" title=\"Vai all'ultima pagina\">" . $tot_pages . "</a>";
					} else
					if ($method=="GET")
					{
                		$num.= $this->fullsteps ."<a href=\"".$_SERVER['PHP_SELF']."?page=".$tot_pages."&".$parametri."\" title=\"Vai all'ultima pagina\">" . $tot_pages . "</a>";
					};
            
        }
        //Imposto il next
        if ($pagina_iniziale == $tot_pages - 1 || $tot_pages == 0) {
            $next = "";
        } else {
            $nextpage = $pagina_iniziale + 2;
			if ($method=="POST" || $method=="ajax")
					{
                		$next = "<a href=\"javascript:jumpToOffset(" . $nextpage . ")\" title=\"Vai alla pagina successiva\"> ".$this->next." </a>";
					} else
					if ($method=="GET")
					{
                		$next = "<a href=\"".$_SERVER['PHP_SELF']."?page=".$nextpage."&".$parametri."\" title=\"Vai alla pagina successiva\"> ".$this->next." </a>";
					};
            
        }
        $navigazione = $previous . '&nbsp;' . $num . '&nbsp;' . $next;
		
		if ($method=="POST")
		{
			if ($parametri!="")
			{
				$par=explode("&", $parametri);
				$quanti=count($par);
				$par_form="";
				foreach ($par as $parametro)
				{
					$PAR=explode("=", $parametro);
					$par_form.='<INPUT type="hidden" name="'.$PAR[0].'" value="'.$PAR[1].'">';
				}
			}
        	$script = ' <SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
						function jumpToOffset(offset)
 						{
 	 						document.jumpForm.page.value=offset;
 	 						document.jumpForm.submit();
 						}
						</SCRIPT>';
        	$form = '<FORM name="jumpForm" action="?" method="post">
  					 <INPUT type="hidden" name="page" value="">
					 '.$par_form.'
					 </FORM>';
        	return array($script , $Articoli , $navigazione , $form);
		} else if($method='ajax')
		{
			if ($parametri!="")
			{
				$par=explode("&", $parametri);
				$quanti=count($par);
				$par_form="";
				foreach ($par as $parametro)
				{
					$PAR=explode("=", $parametro);
					$par_form.='<INPUT type="hidden" name="'.$PAR[0].'" value="'.$PAR[1].'">';
				}
			}
        	$script = "<SCRIPT LANGUAGE='JavaScript' TYPE='text/javascript'>
						function jumpToOffset(offset)
						 { 
						 //ajax...passing 'op'eration
						 	document.jumpForm.page.value=offset;
							data=$('#jumpForm').serializeArray();
							proceed=true;
							 
							// ready....set....go!!!!! ;)    
							if( proceed )
							 {
							   $('$this->ajax_res').load('$this->ajax_url', data, 
							   $('#loading').ajaxStart(function(){ 
          $(this).html(\"<img src='img/loading.gif' height='15' />\");
      })
							   );
							 }
						 }
						</SCRIPT>";
        	$form = '<FORM name="jumpForm" id="jumpForm" action="?" method="post">
  					 <INPUT type="hidden" name="page" value="">
					 '.$par_form.'
					 </FORM>';
        	return array($script , $Articoli , $navigazione , $form);
		}
		if ($method=="GET")
		{
        	return array($Articoli , $navigazione);
		};
		
       
    }
}
?>