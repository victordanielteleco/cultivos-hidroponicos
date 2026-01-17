<?php ///////////////////////////////////////////////////////////////basado en pedidos_informe.php
/*
La idea es que para sacar plantas hag algo como lo que el hace para sacar las líneas de operaciones
un ciclo ya que tengo que sacar mas de un registro

http://localhost/cultivos/bandejas/bandejas_informe.php?idband=006-05
*/


	// *****************   IMPORTANTE   ****************************
	// Utililizar en el fichero codificación "ISO-8859-1" no "UTF-8"
	//**************************************************************
   require ('../fpdf17/fpdf.php');
   include_once ("../bd_funciones.inc");

	define('EURO', chr(128));
	$idband = 1;
	$distancias_entre_operaciones = 7;	// distancia entre las líneas del bandeja

	function changeCharSet ($stringUTF8) {
		return iconv('utf-8', 'ISO-8859-1', $stringUTF8);
	}
		

   /*************************************************************************
    ********************* DEFINICION CLASE PDF *****************************/
   class PDF extends FPDF
   {
		//Cabecera de pÃ¡gina
		function Header()
		{
			$this->Image('../images/logo_cultivos.png',160,10,33);
			$this->Line(20, 23, 160, 23);
		}
      
		//Pie de pá¡gina
		function Footer()
		{
			$this->SetY(-10);
			$this->SetFont('Arial','I',8);
			$this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
		}
   }
   /****************** FIN DEFINICION CLASE PDF ****************************
   ************************************************************************/
   
   $pdf=new PDF();
   $pdf->AliasNbPages();	//Es la función que nos declara un Alias por defecto para 
							//obtener el número de páginas máximo
    						//se añade página definiendo los margenes de la misma
   $pdf->AddPage();
   $pdf->SetMargins(20, 20, 20);
      
   // se muestra título del informe
   $pdf->SetFont('Arial','B',16);
   $pdf->SetY(40);
   $pdf->Cell(0,10,'Informe de la bandeja',1,1,'C');
   
   $pdf->SetFont('Arial','B',10);
   
   // se obtiene informaciÃ³n del bandeja
   try {
		// se toma el nÃºmero de bandeja que se quiere imprimir
		$idband = (isset($_GET["idband"]))?$_GET["idband"]:"";
		// ConexiÃ³n a la base de datos de cultivos
		$conn = Conectarse(SERVIDOR, BD_NOMBRE, BD_USUARIO, BD_PASS);
		// se obtienen datos de la cabecera del bandeja
		$sql = "SELECT `bandejas`.*, `especies`.*, `bandejas`.`idband` " .
        "FROM `bandejas` ".
        "LEFT JOIN `especies` ON `bandejas`.`idespecie` = `especies`.`idespec` " .
        "WHERE `bandejas`.`idband` =" .
        
                    FormatToSQL($idband,"cadena") .";";
		if (!($rs = mysqli_query($conn, $sql)))
			throw new Exception('Error al realizar la consulta.' . $sql);
		if (!($fila = mysqli_fetch_array($rs)))                                           
			throw new Exception("No existe ninguna bandeja con número $idband_orig");
	   	$idband = changeCharSet($fila['idband']); // mete los datos resultantes de la consulta en variables
        $torre = changeCharSet($fila['torre']);
        $alturaband = changeCharSet($fila['alturaband']);
       // $idespecie = changeCharSet($fila['idespecie']);
        $estadoband = changeCharSet($fila['estadoband']);
        $fechaPlantado = changeCharSet(formatear_fecha($fila['fechaPlantado'], false));
        $fechaCosechado = changeCharSet(formatear_fecha($fila['fechaCosechado'], false));
	   //	$fechaped = changeCharSet(formatear_fecha($fila['fechaped'], false));// que es el false? es la forma en la que muestra la fecha, esta declarado en funciones.inc en formatear fecha
	   	
        $idespec = changeCharSet($fila['idespec']);
	 //  	$ivaped = changeCharSet($fila['ivaped']);
	 //  	$fentrped = changeCharSet(formatear_fecha($fila['fentrped'], false));
	   	$nombreespec = changeCharSet($fila['nombreespec']);
	   	$porteadulto = changeCharSet($fila['porteadulto']);

	   	$dias = changeCharSet($fila['dias']);
	   	$humedad_ambiente = changeCharSet($fila['humedad_ambiente']);
	   	$horas_luz_dia = changeCharSet($fila['horas_luz_dia']);
       
       	$intensidad_de_luz = changeCharSet($fila['intensidad_de_luz']);
       
        $N = changeCharSet($fila['N']);  
        $P = changeCharSet($fila['P']);  
        $K = changeCharSet($fila['K']);  
        $PH = changeCharSet($fila['PH']);
        $EC = changeCharSet($fila['EC']);  
        $litros_por_hora = changeCharSet($fila['litros_por_hora']);
       
       
		mysqli_free_result($rs);
	  
		// Mostramos datos de la cabecera de la bandeja
		// *****************************************
		$pdf->SetXY(20, 50);
		$pdf->Cell(30,10,'Nº bandeja: ',0,0,'L');
		$pdf->SetXY(30, 50);
		$pdf->Cell(30,10,$idband,0,0,'R');
		//$pdf->SetXY(120, 60);
       
        $pdf->SetXY(20, 54);
		$pdf->Cell(30,10,'Torre: ',0,0,'L');
		$pdf->SetXY(30, 54);
		$pdf->Cell(30,10,$torre,0,0,'R');
		//$pdf->SetXY(120, 60);
       
        $pdf->SetXY(20, 58);
		$pdf->Cell(30,10,'Altura: ',0,0,'L');
		$pdf->SetXY(30, 58);
		$pdf->Cell(30,10,$alturaband,0,0,'R');
		$pdf->SetXY(120, 60);
       
        /*
        $pdf->SetXY(20, 60);
		$pdf->Cell(30,10,'Id especie: ',0,0,'R');
		$pdf->SetXY(50, 60);
		$pdf->Cell(30,10,$idespecie,0,0,'L');
		$pdf->SetXY(120, 60);        
        */
       
        $pdf->SetXY(20, 62);
		$pdf->Cell(30,10,'Estado bandeja: ',0,0,'L');
		$pdf->SetXY(90, 62);
		$pdf->Cell(30,10,$estadoband,0,0,'R');
		
       
       
        $pdf->SetXY(20, 66);
		$pdf->Cell(30,10,'Fecha plantado:',0,0,'L');
		$pdf->SetXY(90, 66);
		$pdf->Cell(30,10,$fechaPlantado,0,0,'R'); //////////////////////////////////no funciona
		
        $pdf->SetXY(20, 70);
		$pdf->Cell(30,10,'Fecha de Cosechado:',0,0,'L');
		$pdf->SetXY(90, 70);
		$pdf->Cell(30,10,$fechaCosechado,0,0,'R'); //////////////////////////////////no funciona
       
       
       
       
		// se muestra rectangulo con el texto Especie
		$pdf->SetFont('Arial','B',12);
		$pdf->Rect(20, 85,170,57);
		$pdf->setfillcolor(255);
		$pdf->Rect(23, 80, 22, 10, 'F');
		$pdf->setXY(25, 80);
		$pdf->Cell(30,10,'Especie ',0,0,'L');
		// se muestran datos de la Especie
		$pdf->SetFont('Arial','B',10);
		$pdf->setXY(25, 87);
		$pdf->Cell(30,10,'Id Especie: ',0,0,'L');
		$pdf->setXY(64, 87);
		$pdf->Cell(10,10,$idespec,0,0,'L');
		$pdf->setXY(84, 87);
		$pdf->Cell(30,10,$nombreespec,0,0,'L');
		
        
        $pdf->setXY(25, 91);
		$pdf->Cell(30,10,'Porte adulto: ',0,0,'L');
        $pdf->setXY(64, 91);
		$pdf->Cell(30,10,$porteadulto,0,0,'L');
		
        $pdf->setXY(25, 95);
		$pdf->Cell(30,10,'Días: ',0,0,'L');
        $pdf->setXY(64, 95);
		$pdf->Cell(10,10,$dias,0,0,'L');
		
        $pdf->setXY(25, 99);
		$pdf->Cell(30,10,'Humedad ambiente: ',0,0,'L');
        $pdf->setXY(64, 99);       
		$pdf->Cell(20,10,$humedad_ambiente,0,0,'L');
		
        
        $pdf->setXY(25, 103);
		$pdf->Cell(30,10,'Horas de luz al dia: ',0,0,'L');
		$pdf->setXY(64, 103);
		$pdf->Cell(30,10,$horas_luz_dia,0,0,'L');
       
        $pdf->setXY(25, 107);
		$pdf->Cell(30,10,'Intensidad de luz: ',0,0,'L');
        $pdf->setXY(64, 107);
		$pdf->Cell(30,10,$intensidad_de_luz,0,0,'L');
        $pdf->setXY(69, 107);
		$pdf->Cell(30,10,'%',0,0,'L');
        
        $pdf->setXY(25, 111);
		$pdf->Cell(30,10,'Nitrógeno (ppm): ',0,0,'L');
        $pdf->setXY(64, 111);
		$pdf->Cell(30,10,$N,0,0,'L');
       
        $pdf->setXY(25, 115);
		$pdf->Cell(30,10,'Fósforo (ppm): ',0,0,'L');  
        $pdf->setXY(64, 115);
		$pdf->Cell(30,10,$P,0,0,'L');
       
        $pdf->setXY(25, 119);
		$pdf->Cell(30,10,'Nitrógeno (ppm): ',0,0,'L');        
        $pdf->setXY(64, 119);
		$pdf->Cell(30,10,$K,0,0,'L');
       
        $pdf->setXY(25, 123);
		$pdf->Cell(30,10,'PH:',0,0,'L');       
        $pdf->setXY(64, 123);
		$pdf->Cell(30,10,$PH,0,0,'L');  
       
        $pdf->setXY(25, 127);
		$pdf->Cell(30,10,'Electroconductividad: ',0,0,'L');
        $pdf->setXY(64, 127);
		$pdf->Cell(30,10,$EC,0,0,'L');
       
        $pdf->setXY(25, 131);
		$pdf->Cell(30,10,'Litros por hora: ',0,0,'L');
        $pdf->setXY(64, 131);
		$pdf->Cell(30,10,$litros_por_hora,0,0,'L');     
       
      
	  
		// Mostramos datos de las líneas del bandeja
		// ****************************************
		//Encabezado tabla (el nombre de las columnas)
		$pdf->setXY(20, 145);
		$pdf->Cell(12,10,'id operacion','B',0,'C');
		$pdf->setXY(35, 145);
		$pdf->Cell(18,10,'DNIoper','B',0,'C');
		$pdf->setXY(50, 145);
		$pdf->Cell(50,10,'tarea','B',0,'C');
		$pdf->setXY(95, 145);
		$pdf->Cell(10,10,'fecha operacion','B',0,'C');
		$pdf->setXY(122, 145);
		$pdf->Cell(28,10,'estado de la tarea','B',0,'C');
		$pdf->setXY(153, 145);
		$pdf->Cell(10,10,'inicio','B',0,'C');
		$pdf->setXY(160, 145);
		$pdf->Cell(10,10,'minutos tarea','B',0,'C');  
		$pdf->setXY(165, 145);
		$pdf->Cell(10,10,'final','B',0,'C');
       
       
		$pdf->setXY(166, 145);
		$pdf->Cell(24,10,'costo de materiales','B',0,'C');
		//operaciones del bandeja
		$pdf->SetFont('Arial','B',9);
		$PosY_linea = 155; // posición Y de la línea a imprimir
		$total_operacionesbandeja=0;
		$operacionesenbandeja=false;
		// Obtención listado de bandejas ordenados por su número //!!!!!!!!!FALTAAAAAAAAAAAAA!!!!!
       ///////////////////////////////////////////////////////////////////////////////////
       // HACER INNER JOIN
	    $sql = "SELECT `bandejas`.`idband`, `operaciones`.*, `operaciones`.`idband` " .
            "FROM `operaciones` " .
            "LEFT JOIN `bandejas` ON `operaciones`.`idband` = `bandejas`.`idband` " .
            "WHERE `operaciones`.`idband` =" .
            FormatToSQL($idband,"cadena") ." ORDER BY idoperacion;";
	        //throw new Exception($sql);
		if (!($rs = mysqli_query($conn, $sql)))
	        throw new Exception('Error al obtener las operaciones asociadas a la bandeja.');
		
       //////////////OJOOO pone un while para ir sacando cada linea!!!!!
       while($fila = mysqli_fetch_array($rs)) {
			$operacionesenbandeja = true;
			//$total_linea = $fila['unilin'] * $fila['preunlin'] * (1 - ($fila['desculin']/100));
			//$total_operacionesbandeja += $total_linea;
			$pdf->SetXY(20,$PosY_linea);
			$pdf->Cell(8,10,changeCharSet($fila['idoperacion']),0,0,'R');
			$pdf->SetXY(35,$PosY_linea);
			$pdf->Cell(14,10,changeCharSet($fila['DNIoper']),0,0,'R');
			//$pdf->SetXY(58,$PosY_linea);
			//$pdf->Cell(50,10,changeCharSet($fila['idband']),0,0,'L');
			$pdf->SetXY(55,$PosY_linea);
			$pdf->Cell(10,10,changeCharSet($fila['tarea']),0,0,'L');
            
            $pdf->SetXY(109,$PosY_linea);
			$pdf->Cell(10,10,changeCharSet($fila['fechaOperacion']),0,0,'R');
            $pdf->SetXY(120,$PosY_linea);
			$pdf->Cell(10,10,changeCharSet($fila['estado_tarea']),0,0,'R');
            $pdf->SetXY(109,$PosY_linea);
			$pdf->Cell(10,10,changeCharSet($fila['Inicio']),0,0,'R');
            $pdf->SetXY(109,$PosY_linea);
			$pdf->Cell(10,10,changeCharSet($fila['tiempo_tarea']),0,0,'R');
            $pdf->SetXY(109,$PosY_linea);
			$pdf->Cell(10,10,changeCharSet($fila['Final']),0,0,'R');
            
			$pdf->SetXY(155,$PosY_linea);
			$pdf->Cell(26,10,changeCharSet($fila['costo_materiales']) . ' eur',0,0,'R');
			//$pdf->SetXY(153,$PosY_linea);
			/*$pdf->Cell(10,10,changeCharSet($fila['desculin']),0,0,'R');
			$pdf->SetXY(166,$PosY_linea);*/
//			$pdf->Cell(22,10, sprintf("%.2f", $total_linea) . ' ' . EURO,0,0,'R');
			// obtenemos posición de la siguiente línea
			$PosY_linea += $distancias_entre_operaciones;
		}
/*		if ($operacionesenbandeja) {           // tendría que quitar lo del iva porque creo que no lo vamos a usar pero si que tengo que calcular totales, es más, tndré que calcular también los minutos totales
			$pdf->SetFont('Arial','B',12);
			$total_IVA = $total_operacionesbandeja * $ivaped / 100;
			$total_bandeja = $total_operacionesbandeja + $total_IVA;
			// se muestra el total del bandeja
			$pdf->line(166,$PosY_linea+4,188,$PosY_linea+4);
			
			$pdf->setXY (100,$PosY_linea + 5);
			$pdf->Cell(62,10,'Total sin IVA:',0,0,'R');
			$pdf->setXY (166,$PosY_linea + 5);
			$pdf->Cell(22,10,sprintf("%.2f", $total_operacionesbandeja) . ' ' . EURO,0,0,'R');
			
			$pdf->setXY (100,$PosY_linea + 15);
			$pdf->Cell(62,10,'IVA (' . $ivaped . '):',0,0,'R');
			$pdf->setXY (166,$PosY_linea + 15);
			$pdf->Cell(22,10,sprintf("%.2f", $total_IVA) . ' ' . EURO,0,0,'R');
			
			$pdf->setXY (100,$PosY_linea + 25);
			$pdf->Cell(62,10,'Suma total:',0,0,'R');
			$pdf->setXY (166,$PosY_linea + 25);
			$pdf->Cell(22,10,sprintf("%.2f", $total_bandeja) . ' ' . EURO,1,0,'R');
		}
*/   
   
	  // se libera la conexión a la base de datos
	  mysqli_close($conn);
	}	// FIN try
	catch (Exception $e) {
		$b_error = true;
		$msg = getInformationError($e,$conn,$sql);
		$pdf->Write(10, $msg);  
	}
 
	$pdf->Output();
?>