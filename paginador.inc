<?php


		/* ##### PAGINADOR ##### */		
		$numPags = ceil( $numRegs / 10) ;
		// montamos el paginador
		if ($numPags > 1){
			$paginador = "<div id=\"paginador\">";
			$aux = 0;
			$btnPuntos = "<span>...</span>";
			for($i = 1; $i<= $numPags; $i++){
				if($pagActual > 1 && $aux == 0){
					$paginador .= "<span id=\"".($pagActual-1)."\" class=\"btn-paginador\" >&laquo;</span>" ;
					$aux = 1;
				}

				// SIEMPRE PINTAMOS LA PAG 1
				if ( $i == 1){
					$paginador .=  "<span id=\"1\" class=\"btn-paginador";
					$paginador .= ($pagActual == $i)? " btn-activo\">" : "\">";
					$paginador .= "1</span>" ;	
				}

				
				//|| ($pagActual <= ($numPags-5) && $i >= ($pagActual+5)
				
				// LAST 5
				if  ( $pagActual>=($numPags-4) && $pagActual <= $numPags &&  $i < $numPags  && $i >= ($numPags-5) && $i > 1){
					if ($i ==($numPags-5)){ $paginador .= "$btnPuntos"; }
					$paginador .=  "<span id=\"".$i."\" class=\"btn-paginador";
					$paginador .= ($pagActual == $i)? " btn-activo\">" : "\">";
					$paginador .= $i . "</span>" ;								
				// MEDIO
				}else if( ($pagActual >= 5 && $pagActual <= ($numPags-5) && $i != 1 && ( $i <= ($pagActual+2) && $i >= ($pagActual-2)) ) ) {
					if ($i ==($pagActual-2)){ $paginador .= "$btnPuntos"; }
					$paginador .=  "<span id=\"".$i."\" class=\"btn-paginador";
					$paginador .= ($pagActual == $i)? " btn-activo\">" : "\">";
					$paginador .= $i . "</span>" ;	
					if ($i == ($pagActual+2)){ $paginador .= $btnPuntos; }
				// TOP 5
				} else if ( $pagActual>=1 && $pagActual <= 5 && $i > 1 && $i <= 5 && ($i >= ($pagActual-2)) && $i < $numPags){
					$paginador .=  "<span id=\"".$i."\" class=\"btn-paginador";
					$paginador .= ($pagActual == $i)? " btn-activo\">" : "\">";
					$paginador .= $i . "</span>" ;	
					if ($i == 5 && $numPags > 6){ $paginador .= $btnPuntos; }				
				}
				
				// SIEMPRE PINTAMOS LA PAG FINAL
				if ( $i == $numPags && $i > 1){
					$paginador .=  "<span id=\"".$numPags."\" class=\"btn-paginador";
					$paginador .= ($pagActual == $numPags)? " btn-activo\">" : "\">";
					$paginador .= $numPags . "</span>" ;	
				}
			}
			if ($numPags > $pagActual) {
				$paginador .=  "<span id=\"".($pagActual+1)."\" class=\"btn-paginador\">&raquo;</span>" ;
			}
			$paginador .= "</div>";
		}

?>