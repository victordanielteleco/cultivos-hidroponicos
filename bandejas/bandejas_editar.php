<?php
	include_once ("../bd_funciones.inc");
	
    if (isset($_POST["btn_cancelar"])) { // se ha pulsado el botón cancelar
		//La función header se debe utilizar antes de abrir la etiqueta <html>
		header("Location: bandejas_listado.php");
		exit();
    } else {
		try {
			// se toman valores pasados en el formulario (si existen)
			$msg = (isset($_GET["msg"]))?$_GET["msg"]:"";
			// valores del bandeja
			$idband_orig = (isset($_POST["idband_orig"])) ? $_POST["idband_orig"] : $_GET["idband"];
			$idband = (isset($_POST["idband"]))?$_POST["idband"]:"";
			$torre = (isset($_POST["torre"]))?$_POST["torre"]:"";
            $alturaband = (isset($_POST["alturaband"]))?$_POST["alturaband"]:"";
            $idespecie = (isset($_POST["idespecie"]))?$_POST["idespecie"]:"";
            $estadoband = (isset($_POST["estadoband"]))?$_POST["estadoband"]:"";
			$fechaPlantado = (isset($_POST["fechaPlantado"]))?$_POST["fechaPlantado"]:"";
			$fechaCosechado = (isset($_POST["fechaCosechado"]))?$_POST["fechaCosechado"]:"";
            // valores de la planta nueva
            $idplanta_orig = (isset($_POST["idplanta_orig"])) ? $_POST["idplanta_orig"] : ""; 
			$idplanta_nuevo = (isset($_POST["idplanta_nuevo"]))?$_POST["idplanta_nuevo"]:"";
            $porte_actual_nuevo = (isset($_POST["porte_actual_nuevo"]))?$_POST["porte_actual_nuevo"]:"";
			// valores de la operación nueva
            $idoperacion_orig = (isset($_POST["idoperacion_orig"])) ? $_POST["idoperacion_orig"] : ""; 
			$idoperacion_nuevo = (isset($_POST["idoperacion_nuevo"]))?$_POST["idoperacion_nuevo"]:"";  
            $DNIoper_nuevo = (isset($_POST["DNIoper_nuevo"]))?$_POST["DNIoper_nuevo"]:"";          
            $tarea_nuevo = (isset($_POST["tarea_nuevo"]))?$_POST["tarea_nuevo"]:"";
            $fechaOperacion_nuevo = (isset($_POST["fechaOperacion_nuevo"]))?$_POST["fechaOperacion_nuevo"]:"";
            $estado_tarea_nuevo = (isset($_POST["estado_tarea_nuevo"]))?$_POST["estado_tarea_nuevo"]:"";
            $Inicio_nuevo = (isset($_POST["Inicio_nuevo"]))?$_POST["Inicio_nuevo"]:"";
            $tiempo_tarea_nuevo = (isset($_POST["tiempo_tarea_nuevo"]))?$_POST["tiempo_tarea_nuevo"]:"";
            $Final_nuevo = (isset($_POST["Final_nuevo"]))?$_POST["Final_nuevo"]:"";
            $costo_materiales_nuevo = (isset($_POST["costo_materiales_nuevo"]))?$_POST["costo_materiales_nuevo"]:"";   
		
			// Conexión a la base de datos de cultivos
			$conn = Conectarse(SERVIDOR, BD_NOMBRE, BD_USUARIO, BD_PASS);
            
                //////////////////////////////////EDITAR BANDEJA////////////////////////////////////
			if (isset($_POST["btn_modificar"])) { // se ha pulsado el botón modificar
				// Se comprueba si el valor de los campos es correcto
				$msg_datos="";
				ComprobarDato($idband, "Id de bandeja", true, "cadena", $msg_datos);
				ComprobarDato($torre, "Torre", true, "numerico", $msg_datos);
                if (is_numeric($torre))
					if (($torre<=0) || ($torre>100))
						$msg_datos .= "Error: el número de torre debe ser un valor comprendido entre 001 y 100.<br/>";
                ComprobarDato($alturaband, "Altura donde está bandeja", true, "numerico", $msg_datos);           
                if (is_numeric($alturaband))
					if (($alturaband<=0) || ($alturaband>99))
						$msg_datos .= "Error: el número de altura debe ser un valor comprendido entre 01 y 99.<br/>";
				ComprobarDato($idespecie, "Id especie en Bandeja", true, "cadena", $msg_datos);
				ComprobarDato($estadoband, "Estado de Bandeja", true, "cadena", $msg_datos);
                ComprobarDato($fechaPlantado, "Fecha de plantado", false, "fecha", $msg_datos);
				ComprobarDato($fechaCosechado, "Fecha de cosechado", false, "fecha", $msg_datos);
				// se comprueba que la fecha prevista de cosechado es superior a la fecha de plantado
				if (is_date($fechaPlantado) && is_date($fechaCosechado)) {
					if (INPUT_TYPE_DATE){
						$datetime1 = DateTime::createFromFormat("Y-m-d", $fechaPlantado);
						$datetime2 = DateTime::createFromFormat("Y-m-d", $fechaCosechado);					
					} else {
						$datetime1 = DateTime::createFromFormat("d/m/Y", $fechaPlantado);
						$datetime2 = DateTime::createFromFormat("d/m/Y", $fechaCosechado);
					}
					$intervalo= $datetime1->diff($datetime2);
					if ($intervalo->format('%r%a') <= 0)
						$msg_datos .= "Error: la fecha prevista de cosechado deber superior a la " .
							"fecha de plantado.<br/>";
				}
				if ($msg_datos != "")
					throw new Exception($msg_datos);

				// se comprueba si ya existe alguna bandeja con ese id
				// siempre que se haya modificado el valor del id de la bandeja
				if ($idband_orig != $idband) {
					$sql = "SELECT * FROM bandejas WHERE idband=" .
                    FormatToSQL($idband,"cadena") .";";
					if (!($rs = mysqli_query($conn, $sql)))
						throw new Exception("Error al intentar comprobar si existe alguna bandeja con ese id.");
					if (mysqli_num_rows($rs) > 0)
						throw new Exception("Imposible modificar la bandeja.<br>Ya existe una bandeja con id $idband.");
					mysqli_free_result($rs);
				}
				// se modifica la bandeja
				$sql = "UPDATE bandejas SET idband=" . FormatToSQL($idband,"cadena") . 
                    ", torre=" . FormatToSQL($torre,"numerico") .
                    ", alturaband=" . FormatToSQL($alturaband,"numerico") .
                    ", idespecie=" . FormatToSQL($idespecie,"cadena") .
                    ", estadoband=" . FormatToSQL($estadoband,"cadena") .
                    ", fechaPlantado=" . FormatToSQL($fechaPlantado,"fecha") .
                    ", fechaCosechado=" . FormatToSQL($fechaCosechado,"fecha") .
					" WHERE idband=" . FormatToSQL($idband_orig,"cadena") .";";
				if (!mysqli_query($conn, $sql))
					throw new Exception("Error al intentar modificar la Bandeja.");
				// se ha modificado la bandeja correctamente - Se modifica valor de $idband_orig
				$idband_orig = $idband;
				$msg = "Bandeja modificada con Éxito.";
                ////////////////////////////////////////////////////////////////////////////////////////
                
                
                ////////////////////////////////////INSERTAR PLANTA/////////////////////////////////////
			} elseif (isset($_POST["btn_nuevaplanta"])) { // se ha pulsado el botón insertar nueva planta
				// Se comprueba si el valor de los campos de la planta es correcto
				$msg_datos="";
				ComprobarDato($idplanta_nuevo, "Id numérico de planta nueva", true, "numerico", $msg_datos);
				ComprobarDato($porte_actual_nuevo, "Porte (tamaño) actual de planta nueva", true, "numerico", $msg_datos);
				if ($msg_datos != "")
					throw new Exception($msg_datos);
				// se comprueba si ya existe una planta para esa bandeja con ese idplanta
				$sql = "SELECT * FROM Plantas WHERE idband=" .
                    FormatToSQL($idband_orig,"cadena") . " AND idplanta=" .
                    FormatToSQL($idplanta_nuevo,"numerico") . ";";
				if (!($rs = mysqli_query($conn, $sql)))
					throw new Exception("Error al intentar comprobar si ya existe alguna planta " .
										"con número $idplanta_nuevo" .
                                        "para esta bandeja.");
				if (mysqli_num_rows($rs) > 0)
					throw new Exception("Imposible insertar la nueva planta.<br>" .
										"La bandeja $idband_orig ya contiene una planta con número $idplanta_nuevo.");
			    mysqli_free_result($rs);
				// se inserta la nueva planta
			    $sql = "INSERT INTO Plantas (idband, idplanta, porte_actual) VALUES (" .
                    FormatToSQL($idband_orig,"cadena") .
                    ", " . FormatToSQL($idplanta_nuevo,"numerico") .
                    ", " . FormatToSQL($porte_actual_nuevo,"numerico") . ");";
				if (!mysqli_query($conn, $sql))
					throw new Exception("Error al intentar insertar la nueva planta.");
				// si se ha insertado la nueva planta correctamente
				$msg = "Planta de bandeja insertada con Éxito.";
				// se reinicializan los campos para una nueva planta
				$idplanta_nuevo = "";
				$porte_actual_nuevo = "";
                ///////////////////////////////////////////////////////////////////////////////////////
                
                ////////////////////////////////////ELIMINAR PLANTA////////////////////////////////////
			} elseif (isset($_POST["btn_eliminarplanta"])) {
				// se ha pulsado el botón eliminar planta de bandeja
				// se modifica la bandeja
				$sql = "DELETE FROM Plantas WHERE idband=" .
                    FormatToSQL($idband_orig,"cadena") . " AND idplanta=" .
                    FormatToSQL($_POST["btn_eliminarplanta"],"numerico") . ";";
				if (!mysqli_query($conn, $sql))
					throw new Exception("Error al intentar eliminar la planta de bandeja.");
				// se ha eliminado la planta correctamente
				$msg = "Planta de bandeja eliminada con Éxito.";
                ///////////////////////////////////////////////////////////////////////////////////////
                
                ////////////////////////////////////EDITAR PLANTA//////////////////////////////////////
			} elseif (isset($_POST["btn_editarplanta"])) { // se ha pulsado el botón editar planta
                		//	- se obtienen los datos de la planta
                $idplanta_orig=FormatToSQL($_POST["btn_editarplanta"],"numerico");
				$sql = "SELECT * FROM Plantas WHERE idband=" .
                    FormatToSQL($idband_orig,"cadena") . " AND idplanta=" .
                    FormatToSQL($_POST["btn_editarplanta"],"numerico") . ";";
				if (!($rs = mysqli_query($conn, $sql)))
					throw new Exception("Error al intentar obtener los datos de la planta");
				if (!($fila = mysqli_fetch_array($rs)))
					throw new Exception("No existe ninguna planta con id $idplanta_orig");
				// datos de la planta
				//$idplanta_orig=$fila['idplanta'];
				$idplanta_nuevo=$fila['idplanta'];
                $porte_actual_nuevo=$fila['porte_actual'];
				mysqli_free_result($rs);
                
            } elseif (isset($_POST["btn_modificarplanta"])) { // se ha pulsado el botón modificar planta
				// Se comprueba si el valor de los campos es correcto   
				$msg_datos="";
				ComprobarDato($idplanta_nuevo, "Id numérico de planta nueva", true, "numerico", $msg_datos);
				ComprobarDato($porte_actual_nuevo, "Porte (tamaño) actual de planta nueva", true, "numerico", $msg_datos);
				if ($msg_datos != "")
					throw new Exception($msg_datos);
				// se comprueba si ya existe alguna planta con ese id
				// siempre que se haya modificado el valor del id de la planta
				if ($idplanta_orig != $idplanta_nuevo) {
					//$sql = "SELECT * FROM Plantas WHERE idplanta=" .
                    //FormatToSQL($idplanta_nuevo,"numerico") .";";
                    $sql = "SELECT * FROM Plantas WHERE idband=" .
                    FormatToSQL($idband_orig,"cadena") . " AND idplanta=" .
                    FormatToSQL($idplanta_nuevo,"numerico") . ";";
					if (!($rs = mysqli_query($conn, $sql)))
						throw new Exception("Error al intentar comprobar si existe alguna planta con ese id.");
					if (mysqli_num_rows($rs) > 0)
						throw new Exception("Imposible modificar la planta.<br>Ya existe una planta con id $idplanta_nuevo.");
					mysqli_free_result($rs);
				}
				// se modifica la planta
				//$sql = "UPDATE Plantas SET idplanta=" . FormatToSQL($idplanta_nuevo,"numerico") . 
                //    ", porte_actual=" . FormatToSQL($porte_actual_nuevo,"numerico") .
				//	  " WHERE idplanta=" . FormatToSQL($idplanta_orig,"numerico") .";";
                $sql = "UPDATE Plantas SET idplanta=" . FormatToSQL($idplanta_nuevo,"numerico") . 
                    ", porte_actual=" . FormatToSQL($porte_actual_nuevo,"numerico") .
					" WHERE idband=" .
                    FormatToSQL($idband_orig,"cadena") . " AND idplanta=" . 
                    FormatToSQL($idplanta_orig,"numerico") .";";
				if (!mysqli_query($conn, $sql))
					throw new Exception("Error al intentar modificar la Planta.");
				// se ha modificado la planta correctamente - Se modifica valor de $idplanta_orig
				$idplanta_orig = $idplanta_nuevo;
				$msg = "Planta modificada con Éxito.";
                
            } elseif (isset($_POST["btn_cancelarplanta"])) { // se ha pulsado el botón cancelar planta a modificar               
                // se reinicializan los campos para una nueva línea
				$idplanta_nuevo = "";
				$porte_actual_nuevo = "";
                ////////////////////////////////////////////////////////////////////////////////////////
            
                
                //////////////////////////////////INSERTAR OPERACION////////////////////////////////////
			} elseif (isset($_POST["btn_nuevaoperacion"])) { // se ha pulsado el botón insertar nueva operación
				// Se comprueba si el valor de los campos de la operación es correcto
				$msg_datos="";
				ComprobarDato($idoperacion_nuevo, "Id numérico de operación nueva", true, "numerico", $msg_datos);
				ComprobarDato($DNIoper_nuevo, "Operario de la operación nueva", true, "cadena", $msg_datos);
                ComprobarDato($tarea_nuevo, "Descripción de operación nueva", true, "cadena", $msg_datos);
                ComprobarDato($fechaOperacion_nuevo, "Fecha para realizar operación nueva", true, "fechahora", $msg_datos);
                ComprobarDato($estado_tarea_nuevo, "Estado de la operación nueva", true, "cadena", $msg_datos);
                ComprobarDato($Inicio_nuevo, "Fecha que operario comienza la operación nueva", false, "fechahora", $msg_datos);
                ComprobarDato($tiempo_tarea_nuevo, "Tiempo(minutos) realización operación nueva", false, "numerico", $msg_datos);
                ComprobarDato($Final_nuevo, "Fecha que operario termina la operación nueva", false, "fechahora", $msg_datos);
                ComprobarDato($costo_materiales_nuevo, "Coste de materiales usados en operación nueva", false, "numerico", $msg_datos);
                if (is_datetime($Inicio_nuevo) && is_datetime($Final_nuevo)) {
					if (INPUT_TYPE_DATE){
						$datetime1 = DateTime::createFromFormat("Y-m-d\TH:i:s", $Inicio_nuevo);
						$datetime2 = DateTime::createFromFormat("Y-m-d\TH:i:s", $Final_nuevo);					
					} else {
						$datetime1 = DateTime::createFromFormat("d/m/Y\TH:i:s", $Inicio_nuevo);
						$datetime2 = DateTime::createFromFormat("d/m/Y\TH:i:s", $Final_nuevo);
					}
					$intervalo= $datetime1->diff($datetime2);
					if ($intervalo->format('%r%s') < 0)
						$msg_datos .= "Error: la fecha de Final de operación debe ser superior a la " .
							"fecha de Inicio de operación.<br/>";
				}
                
				if ($msg_datos != "")
					throw new Exception($msg_datos);
				// se comprueba si ya existe una operación para esa bandeja con ese idband
				$sql = "SELECT * FROM Operaciones WHERE idband=" .
                    FormatToSQL($idband_orig,"cadena") . " AND idoperacion=" .
                    FormatToSQL($idoperacion_nuevo,"numerico") . ";";
				if (!($rs = mysqli_query($conn, $sql)))
					throw new Exception("Error al intentar comprobar si ya existe alguna operación " .
										"con número $idoperacion_nuevo" .
                                        "para esta bandeja.");
				if (mysqli_num_rows($rs) > 0)
					throw new Exception("Imposible insertar la nueva operación.<br>" .
										"La bandeja $idband_orig ya contiene una operación con número $idoperacion_nuevo.");
			    mysqli_free_result($rs);
				// se inserta la nueva operación
			    $sql = "INSERT INTO Operaciones (idband, idoperacion, DNIoper, tarea, fechaOperacion, estado_tarea, Inicio, tiempo_tarea, Final, costo_materiales) VALUES (" .
                    FormatToSQL($idband_orig,"cadena") .
                    ", " . FormatToSQL($idoperacion_nuevo,"numerico") .
                    ", " . FormatToSQL($DNIoper_nuevo,"cadena") .
                    ", " . FormatToSQL($tarea_nuevo,"cadena") .
                    ", " . FormatToSQL($fechaOperacion_nuevo,"fechahora") .
                    ", " . FormatToSQL($estado_tarea_nuevo,"cadena") .
                    ", " . FormatToSQL($Inicio_nuevo,"fechahora") .
                    ", " . FormatToSQL($tiempo_tarea_nuevo,"numerico") .
                    ", " . FormatToSQL($Final_nuevo,"fechahora") .
                    ", " . FormatToSQL($costo_materiales_nuevo,"numerico") . ");";
				if (!mysqli_query($conn, $sql))
					throw new Exception("Error al intentar insertar la nueva operación.");
				// si se ha insertado la nueva operación correctamente
				$msg = "Operación de bandeja insertada con Éxito.";
				// se reinicializan los campos para una nueva línea
				$idoperacion_nuevo = "";
				$DNIoper_nuevo = "";
                $tarea_nuevo = "";
                $fechaOperacion_nuevo = "";
                $estado_tarea_nuevo = "";
                $Inicio_nuevo = "";
				$tiempo_tarea_nuevo = "";
				$Final_nuevo = "";
				$costo_materiales_nuevo = "";
                ///////////////////////////////////////////////////////////////////////////////////////
                
                //////////////////////////////////ELIMINAR OPERACION///////////////////////////////////
			} elseif (isset($_POST["btn_eliminaroperacion"])) {
				// se ha pulsado el botón eliminar operación de bandeja
				// se modifica la bandeja
				$sql = "DELETE FROM Operaciones WHERE idband=" .
                    FormatToSQL($idband_orig,"cadena") . " AND idoperacion=" .
                    FormatToSQL($_POST["btn_eliminaroperacion"],"numerico") . ";";
				if (!mysqli_query($conn, $sql))
					throw new Exception("Error al intentar eliminar la operación de bandeja.");
				// se ha eliminado la operación correctamente
				$msg = "Operación de bandeja eliminada con Éxito.";
                ///////////////////////////////////////////////////////////////////////////////////////
                
                //////////////////////////////////EDITAR OPERACION/////////////////////////////////////
			} elseif (isset($_POST["btn_editaroperacion"])) { // se ha pulsado el botón editar operación
                		//	- se obtienen los datos de la operación
                $idoperacion_orig=FormatToSQL($_POST["btn_editaroperacion"],"numerico");
				$sql = "SELECT * FROM Operaciones WHERE idband=" .
                    FormatToSQL($idband_orig,"cadena") . " AND idoperacion=" .
                    FormatToSQL($_POST["btn_editaroperacion"],"numerico") . ";";
				if (!($rs = mysqli_query($conn, $sql)))
					throw new Exception("Error al intentar obtener los datos de la operación");
				if (!($fila = mysqli_fetch_array($rs)))
					throw new Exception("No existe ninguna operación con id $idoperacion_orig");
				// datos de la operación
				//$idoperacion_orig=$fila['idoperacion'];
				$idoperacion_nuevo=$fila['idoperacion'];
                $DNIoper_nuevo=$fila['DNIoper'];
                $tarea_nuevo=$fila['tarea'];
                $fechaOperacion_nuevo=$fila['fechaOperacion'];
                $estado_tarea_nuevo=$fila['estado_tarea'];
                $Inicio_nuevo=$fila['Inicio'];
                $tiempo_tarea_nuevo=$fila['tiempo_tarea'];
                $Final_nuevo=$fila['Final'];
                $costo_materiales_nuevo=$fila['costo_materiales'];
				mysqli_free_result($rs);
                
            } elseif (isset($_POST["btn_modificaroperacion"])) { // se ha pulsado el botón modificar operación
				// Se comprueba si el valor de los campos es correcto   
				$msg_datos="";
				ComprobarDato($idoperacion_nuevo, "Id numérico de operación nueva", true, "numerico", $msg_datos);
				ComprobarDato($DNIoper_nuevo, "Operario de la operación nueva", true, "cadena", $msg_datos);
                ComprobarDato($tarea_nuevo, "Descripción de operación nueva", true, "cadena", $msg_datos);
                ComprobarDato($fechaOperacion_nuevo, "Fecha para realizar operación nueva", true, "fechahora", $msg_datos);
                ComprobarDato($estado_tarea_nuevo, "Estado de la operación nueva", true, "cadena", $msg_datos);
                ComprobarDato($Inicio_nuevo, "Fecha que operario comienza la operación nueva", false, "fechahora", $msg_datos);
                ComprobarDato($tiempo_tarea_nuevo, "Tiempo(minutos) realización operación nueva", false, "numerico", $msg_datos);
                ComprobarDato($Final_nuevo, "Fecha que operario termina la operación nueva", false, "fechahora", $msg_datos);
                ComprobarDato($costo_materiales_nuevo, "Coste de materiales usados en operación nueva", false, "numerico", $msg_datos);
				if ($msg_datos != "")
					throw new Exception($msg_datos);
				// se comprueba si ya existe alguna operación con ese id
				// siempre que se haya modificado el valor del id de la operación
				if ($idoperacion_orig != $idoperacion_nuevo) {
					//$sql = "SELECT * FROM Operaciones WHERE idoperacion=" .
                    //FormatToSQL($idoperacion_nuevo,"numerico") .";";
                    $sql = "SELECT * FROM Operaciones WHERE idband=" .
                    FormatToSQL($idband_orig,"cadena") . " AND idoperacion=" .
                    FormatToSQL($idoperacion_nuevo,"numerico") . ";";
					if (!($rs = mysqli_query($conn, $sql)))
						throw new Exception("Error al intentar comprobar si existe alguna operación con ese id.");
					if (mysqli_num_rows($rs) > 0)
						throw new Exception("Imposible modificar la operación.<br>Ya existe una operación con id $idoperacion_nuevo.");
					mysqli_free_result($rs);
				}
				// se modifica la operación
                /* 
				$sql = "UPDATE Operaciones SET idoperacion=" . FormatToSQL($idoperacion_nuevo,"numerico") . 
                    ", DNIoper=" . FormatToSQL($DNIoper_nuevo,"cadena") .
                    ", tarea=" . FormatToSQL($tarea_nuevo,"cadena") .
                    ", fechaOperacion=" . FormatToSQL($fechaOperacion_nuevo,"fechahora") .
                    ", estado_tarea=" . FormatToSQL($estado_tarea_nuevo,"cadena") .
                    ", Inicio=" . FormatToSQL($Inicio_nuevo,"fechahora") .
                    ", tiempo_tarea=" . FormatToSQL($tiempo_tarea_nuevo,"numerico") .
                    ", Final=" . FormatToSQL($Final_nuevo,"fechahora") .
                    ", costo_materiales=" . FormatToSQL($costo_materiales_nuevo,"numerico") .
				    " WHERE idoperacion=" . FormatToSQL($idoperacion_orig,"numerico") .";"; */       
                $sql = "UPDATE Operaciones SET idoperacion=" . FormatToSQL($idoperacion_nuevo,"numerico") . 
                    ", DNIoper=" . FormatToSQL($DNIoper_nuevo,"cadena") .
                    ", tarea=" . FormatToSQL($tarea_nuevo,"cadena") .
                    ", fechaOperacion=" . FormatToSQL($fechaOperacion_nuevo,"fechahora") .
                    ", estado_tarea=" . FormatToSQL($estado_tarea_nuevo,"cadena") .
                    ", Inicio=" . FormatToSQL($Inicio_nuevo,"fechahora") .
                    ", tiempo_tarea=" . FormatToSQL($tiempo_tarea_nuevo,"numerico") .
                    ", Final=" . FormatToSQL($Final_nuevo,"fechahora") .
                    ", costo_materiales=" . FormatToSQL($costo_materiales_nuevo,"numerico") .
					" WHERE idband=" .
                    FormatToSQL($idband_orig,"cadena") . " AND idoperacion=" . 
                    FormatToSQL($idoperacion_orig,"numerico") .";";
				if (!mysqli_query($conn, $sql))
					throw new Exception("Error al intentar modificar la Operación.");
				// se ha modificado la operación correctamente - Se modifica valor de $idoperacion_orig
				$idoperacion_orig = $idoperacion_nuevo;
				$msg = "Operación modificada con Éxito.";
                
            } elseif (isset($_POST["btn_cancelaroperacion"])) { // se ha pulsado el botón cancelar operación a modificar               
                // se reinicializan los campos para una nueva línea
				$idoperacion_nuevo = "";
				$DNIoper_nuevo = "";
                $tarea_nuevo = "";
                $fechaOperacion_nuevo = "";
                $estado_tarea_nuevo = "";
                $Inicio_nuevo = "";
                $tiempo_tarea_nuevo = "";
                $Final_nuevo = "";
                $costo_materiales_nuevo = "";
                ////////////////////////////////////////////////////////////////////////////////////////
                
			} else {	// primera vez que se accede a la página
						//	- se obtienen los datos de la bandeja
				$sql = "SELECT * FROM bandejas WHERE idband=" .
                    FormatToSQL($idband_orig,"cadena") . ";";
				if (!($rs = mysqli_query($conn, $sql)))
					throw new Exception("Error al intentar obtener los datos de la bandeja");
				if (!($fila = mysqli_fetch_array($rs)))
					throw new Exception("No existe ninguna bandeja con id $idband_orig");
				// datos de la bandeja
				//$idband_orig=$fila['idband'];
				$idband=$fila['idband'];
                $torre=$fila['torre'];
                $alturaband=$fila['alturaband'];
                $idespecie=$fila['idespecie'];
                $estadoband=$fila['estadoband'];
                $fechaPlantado=formatear_fecha($fila['fechaPlantado'], false);
                $fechaCosechado=formatear_fecha($fila['fechaCosechado'], false);
				mysqli_free_result($rs);
			}
            
			// se libera la conexión a la base de datos
			mysqli_close($conn);
		}	// FIN try
        catch (Exception $e) {
			$b_error = true;
			$msg = getInformationError($e,$conn,$sql);
		}
	}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Aplicación Cultivos</title>
    <link rel="stylesheet" href="../cultivos.css" />
	
	<script type="text/javascript">
	<!--        
		function ImprimirBandeja(url)
		{
		window.open('./bandejas_informe.php?idband=<?php echo($idband_orig); ?>', 'informeBandeja', 'menubar=yes, scrollbars=yes,toolbar=yes,location=yes,directories=yes,resizable=yes,top=0,left=0');
		}
    
        function hacerclick(){
            document.getElementById('btn_imprimir').onclick = ImprimirBandeja;
        }
    
        window.onload=hacerclick;
	//-->
	</script>
    	
</head>
<body>
<div id="contenedor-general">
	<?php
		include ("../cultivos_cabecera.inc");
	?>
    <section id="contenido">
    <?php
		// se muestra mensaje almacenado en $msg
        if ($msg != "")
			echo ("<p class=\"mensaje\">$msg</p>");
                ////////////////////////////////////////////////////////////////////////// BANDEJA //////////////////////////////////////////////////////////////////////////        
	?>
        <form class="cultivos_datos" action="bandejas_editar.php" method="post">
			<input type="hidden" name="idband_orig" value="<?php echo($idband_orig);?>"/>
            <!--
			<table class="cultivos_datos"> -->
                
            <table class="BANDEJA_EDITAR">     
				<caption>Bandejas: modificar</caption>                
                <tr>
                    <td class="celda_etiq"><label class="oblig" for="idband">Id bandeja:</label></td>
                    <td class="celda_datos"><input type="text" id="idband" name="idband" minlength="6" maxlength="6" size="6" value="<?php echo($idband);?>" readonly/><span class="nota"> torre-altura</span></td></tr>                
                <tr>
                    <td class="celda_etiq"><label class="oblig" for="torre">Torre<sup>*</sup>:</label></td>
                    <td class="celda_datos"><input type="text" id="torre" name="torre" placeholder="000" minlength="3" maxlength="3" size="3" value="<?php echo($torre);?>" /></td></tr>
                <tr>
                    <td class="celda_etiq"><label class="oblig" for="alturaband">Altura donde está bandeja<sup>*</sup>:</label></td>
                    <td class="celda_datos"><input type="text" id="alturaband" name="alturaband" placeholder="00" minlength="2" maxlength="2" size="2" value="<?php echo($alturaband);?>" /></td></tr>
                <tr>
                    <td class="celda_etiq"><label class="oblig" for="idespecie">Id especie en bandeja<sup>*</sup>:</label></td>
                    <td class="celda_datos">
						<select name="idespecie">
							<option value="" <?php if ($idespecie=="") echo("SELECTED"); ?>></option>
	<?php
		// rellenamos desplegable con el listado de especies
		try {
		    // Conexión a la base de datos de cultivos
		    $conn = Conectarse(SERVIDOR, BD_NOMBRE, BD_USUARIO, BD_PASS);
		    // Obtención de las especies
		    $sql = "SELECT idespec, nombreespec FROM especies ORDER BY idespec;";
		    if (!($rs = mysqli_query($conn, $sql)))
		        throw new Exception('Error al buscar las especies.');
			while($fila = mysqli_fetch_array($rs)) {
				echo ("<option value=\"" . $fila['idespec'] . "\" ");
				if ($idespecie == $fila['idespec'])
					echo("SELECTED ");
				echo ("> " .$fila['idespec'] . ", " . $fila['nombreespec'] . "</option><br/>");
			}
		}	// FIN try
        catch (Exception $e) {
			$b_error = true;			
			$el_error = "</select><p class=\"mensaje\">";
			if (!isset($conn)) $conn="";
			if (!isset($sql)) $sql="";
			$el_error .= getInformationError($e,$conn,$sql);
			$el_error .= "</p>";
			echo($el_error);
        }
	?>
						</select></td></tr>
                <tr>
                    <td class="celda_etiq"><label class="oblig" for="estadoband">Estado de bandeja<sup>*</sup>:</label></td>
                    <td class="celda_datos">
						<select name="estadoband">
							<option value="" <?php if ($estadoband=="") echo("SELECTED"); ?>></option>
							<option value="requiriendo_Plantar (bandeja vacía)" <?php if ($estadoband=="requiriendo_Plantar (bandeja vacía)") echo("SELECTED"); ?>>requiriendo_Plantar (bandeja vacía)</option>
							<option value="Plantando" <?php if ($estadoband=="Plantando") echo("SELECTED"); ?>>Plantando</option>
                            <option value="Normal" <?php if ($estadoband=="Normal") echo("SELECTED"); ?>>Normal</option>
                            <option value="requiriendo_QuitarHierbas" <?php if ($estadoband=="requiriendo_QuitarHierbas") echo("SELECTED"); ?>>requiriendo_QuitarHierbas</option>
							<option value="QuitandoHierbas" <?php if ($estadoband=="QuitandoHierbas") echo("SELECTED"); ?>>QuitandoHierbas</option>
                            <option value="requiriendo_MedirPorte" <?php if ($estadoband=="requiriendo_MedirPorte") echo("SELECTED"); ?>>requiriendo_MedirPorte</option>
							<option value="MidiendoPorte" <?php if ($estadoband=="MidiendoPorte") echo("SELECTED"); ?>>MidiendoPorte</option>
                            <option value="requiriendo_Cosechar" <?php if ($estadoband=="requiriendo_Cosechar") echo("SELECTED"); ?>>requiriendo_Cosechar</option>
							<option value="Cosechando" <?php if ($estadoband=="Cosechando") echo("SELECTED"); ?>>Cosechando</option>
                            <option value="requiriendo_Vaciar" <?php if ($estadoband=="requiriendo_Vaciar") echo("SELECTED"); ?>>requiriendo_Vaciar</option>
							<option value="Vaciando y Limpiando" <?php if ($estadoband=="Vaciando y Limpiando") echo("SELECTED"); ?>>Vaciando y Limpiando</option>
						</select></td></tr>
                <tr>
                    <td class="celda_etiq"><label class="oblig" for="fechaPlantado">Fecha de plantado:</label></td>
                    <td class="celda_datos"><input type="date" name="fechaPlantado" maxlength="19" size="19" value="<?php echo($fechaPlantado);?>" /></td></tr>
				<tr>
                    <td class="celda_etiq"><label class="oblig" for="fechaCosechado">Fecha de cosechado:</label></td>
                    <td class="celda_datos"><input type="date" name="fechaCosechado" maxlength="19" size="19" value="<?php echo($fechaCosechado);?>" /></td></tr>
             
                
                
                
	           <tr>
					<td colspan="2">
						<input type="submit" value="Modificar" name="btn_modificar"/>
						<input type="submit" value="Cancelar" name="btn_cancelar"/></td></tr>
                
                                     
                <tr>
					<td class="nota" colspan="2">
						<br/><sup>*</sup> Campos obligatorios</td></tr> 
   
              </table>                                     
            
            
    <?php                    
			/**************************************************************************
			************************* PLANTAS EN BANDEJA ******************************
			**************************************************************************/
	?>
            <!--
			<table class="cultivos_datos">  -->
            <br/> 
        
				<tr>
                    <td class="celda_etiq" colspan="2">
                        <input type="hidden" name="idplanta_orig" value="<?php echo($idplanta_orig);?>"/>
						<table class="cultivos_subdatos" id="Plantas">
							<caption>Plantas</caption>
							<thead>
							    <tr>
						        <th>&nbsp;</th>
						        <th>Nº planta</th>
                                <th>Tamaño actual</th>
                                <th>&nbsp;</th>    
                                <th>Tamaño adulto &nbsp;<br/>(especie)</th>
                                <th>Días crecimiento <br/>(especie)</th></tr>    
							</thead>
	<?php
		try {
            $plantasenbandeja=false;
            
			// Conexión a la base de datos de cultivos
			$conn = Conectarse(SERVIDOR, BD_NOMBRE, BD_USUARIO, BD_PASS);
		    // Obtención listado de plantas de la bandeja ordenadas por su idplanta
            $sql = "SELECT  Plantas.*, Especies.porteadulto, Especies.dias FROM ".
                   "Plantas INNER JOIN Bandejas  ON  Plantas.idband=Bandejas.idband ".
                          " INNER JOIN Especies  ON  Bandejas.idespecie=Especies.idespec ".
                   "WHERE Bandejas.idband=" . FormatToSQL($idband_orig, "cadena") .
                   " ORDER BY idplanta";
            
		    if (!($rs = mysqli_query($conn, $sql)))
		        throw new Exception('Error al obtener las plantas asociadas a la bandeja.');
			while($fila = mysqli_fetch_array($rs)) {
				$plantasenbandeja = true;
                $tamaño_adulto = $fila['porteadulto'];
                
    ?>
							<tr>
							    <td><button type="submit" name="btn_eliminarplanta" value="<?php echo($fila['idplanta']); ?>">
									<img src="../images/eliminar_subdatos.png" alt="eliminar planta"/></button></td>
								<td><?php echo($fila['idplanta']); ?></td>
                                <td><?php echo($fila['porte_actual']); ?> mm</td>
                                <td>&nbsp;</td>
                                <td><?php printf("%d", $fila['porteadulto']); ?> mm</td>
                                <td><?php printf("%d", $fila['dias']); ?> días</td>
                                <td><button type="submit" name="btn_editarplanta" value="<?php echo($fila['idplanta']); ?>">
									<img src="../images/editar.png" alt="editar planta"/></button></td>
                            </tr>
                        
                            
    <?php
			}
			mysqli_free_result($rs);
			mysqli_close($conn);
		}	// FIN try
        catch (Exception $e) {
			$b_error = true;			
			$el_error = "<p class=\"mensaje\">";
			if (!isset($conn)) $conn="";
			if (!isset($sql)) $sql="";
			$el_error .= getInformationError($e,$conn,$sql);
			$el_error .= "</p>";
			echo($el_error);
		}
			
		// se inserta fila para poder introducir una nueva planta
	?> 
                            <tr>
								<td class="celda_datos">
									<button type="submit" name="btn_nuevaplanta">
										<img src="../images/insertar_subdatos.png" alt="insertar planta"/></button></td>
                                
								<td class="celda_datos"><input type="number" name="idplanta_nuevo" min="1" step="any" max="6" value="<?php echo($idplanta_nuevo);?>" /></td>
                                    <style type="text/css">
                                        input[type=number]{
                                            width: 40px;
                                        }
                                    </style>
                                
                                <td class="celda_datos"><input type="text" name="porte_actual_nuevo" maxlength="5" size="5" value="<?php echo($porte_actual_nuevo);?>" /></td>
                                
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td colspan="2"> <input type="submit" value="Modificar" name="btn_modificarplanta"/> </td>
                            </tr>
                            
                            <tr>
                                <td colspan="6">&nbsp;</td>
                                <td colspan="2"> <input type="submit" value="Cancelar" name="btn_cancelarplanta"/>   </td>
							</tr>	
	<?php
		if($plantasenbandeja) {
	?>
                          
                           <!-- 
							<tr>
                                <td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td class="totalcostes_operacionesbandeja"><?php printf("%d", $tamaño_adulto); ?> mm</td></tr> -->

	<?php
		}
	?>
						</table>
					</td></tr>
            
	<?php
		/**************************************************************************
		************************* FIN PLANTAS DE BANDEJA **************************
		**************************************************************************/
	?>
                   
            
            
    <?php                    
			/**************************************************************************
			*********************** OPERACIONES EN BANDEJA ****************************
			**************************************************************************/
	?>
            <!--
			<table class="cultivos_datos">  -->          
            
				<tr>
                    <td class="celda_etiq" colspan="2">
                        <input type="hidden" name="idoperacion_orig" value="<?php echo($idoperacion_orig);?>"/>
						<table class="cultivos_subdatos" id="Operaciones">
							<caption>Operaciones</caption>
							<thead>
							    <tr>
						        <th>&nbsp;</th>
						        <th>Nº operación</th>
                                <th>Operación</th>
                                <th>Operario</th>    
                                <th>Fecha a realizar operación</th>      
                                <th>Estado</th>       
						        <th>Tiempo realización operación</th>
						        <th>Coste materiales necesarios</th></tr>
							</thead>
	<?php
		try {
			$operacionesenbandeja=false;
            $totalcostes_operacionesbandeja=0;
            $totaltiempos_operacionesbandeja=0;
            
			// Conexión a la base de datos de cultivos
			$conn = Conectarse(SERVIDOR, BD_NOMBRE, BD_USUARIO, BD_PASS);
		    // Obtención listado de operaciones de la bandeja ordenadas por su idoperacion
		    $sql = "SELECT Operaciones.*, Operarios.*, Operarios.cargooper, Operarios.nombreoper FROM ".
                "Operarios INNER JOIN Operaciones " .
				"ON Operaciones.DNIoper=Operarios.DNIoper WHERE idband=" . FormatToSQL($idband_orig, "cadena") .
                " ORDER BY idoperacion";
		    if (!($rs = mysqli_query($conn, $sql)))
		        throw new Exception('Error al obtener las operaciones asociadas a la bandeja.');
			while($fila = mysqli_fetch_array($rs)) {
				$operacionesenbandeja = true;
                $totalcostes_operacionesbandeja += $fila['costo_materiales'];
                $totaltiempos_operacionesbandeja += $fila['tiempo_tarea'];
                
    ?>
							<tr>
							    <td><button type="submit" name="btn_eliminaroperacion" value="<?php echo($fila['idoperacion']); ?>">
									<img src="../images/eliminar_subdatos.png" alt="eliminar operacion"/></button></td>
								<td><?php echo($fila['idoperacion']); ?></td>
                                <td><?php echo($fila['tarea']); ?></td>   
                                <td><?php echo($fila['DNIoper']); ?></td>
                                <td><?php echo($fila['fechaOperacion']); ?></td>
                                <td><?php echo($fila['estado_tarea']); ?></td>
                                <td><?php echo($fila['tiempo_tarea']); ?> min.</td>
                                <td><?php echo($fila['costo_materiales']); ?> &euro;</td>
                                <td><button type="submit" name="btn_editaroperacion" value="<?php echo($fila['idoperacion']); ?>">
									<img src="../images/editar.png" alt="editar operacion"/></button></td>
                            </tr>
                        
                            <tr>
							    <td colspan="5">&nbsp;</td>
                                <th align="left"> <i>Inicio:</i> </th> 
                                <td>&nbsp;</td>
								<td>&nbsp;</td></tr>
                            <tr>
							    <td colspan="5">&nbsp;</td>
                                <td> <i><?php echo($fila['Inicio']); ?></i> </td>
                                <td>&nbsp;</td>
								<td>&nbsp;</td></tr>
                            <tr>
							    <td colspan="5">&nbsp;</td>
                                <th align="left"> <i>Final:</i> </th> 
                                <td>&nbsp;</td>
								<td>&nbsp;</td></tr>
                            <tr>
							    <td colspan="5">&nbsp;</td>
								<td> <i><?php echo($fila['Final']); ?></i> </td>
                                <td>&nbsp;</td>
								<td>&nbsp;</td></tr>
    <?php
			}
			mysqli_free_result($rs);
			mysqli_close($conn);
		}	// FIN try
        catch (Exception $e) {
			$b_error = true;			
			$el_error = "<p class=\"mensaje\">";
			if (!isset($conn)) $conn="";
			if (!isset($sql)) $sql="";
			$el_error .= getInformationError($e,$conn,$sql);
			$el_error .= "</p>";
			echo($el_error);
		}
			
		// se inserta fila para poder introducir una nueva operación
	?> 
                            <tr>
								<td class="celda_datos">
									<button type="submit" name="btn_nuevaoperacion">
										<img src="../images/insertar_subdatos.png" alt="insertar operacion"/></button></td>
                                
								<td class="celda_datos"><input type="text" name="idoperacion_nuevo" maxlength="10" size="4" value="<?php echo($idoperacion_nuevo);?>" /></td>
                                <style type="text/css">
                                        input[name=idoperacion_nuevo]{
                                            width: 25px;
                                        }
                                </style>
                                
                                <td class="celda_datos">
                                    <select name="tarea_nuevo">
                                        <option value="" <?php if ($tarea_nuevo=="") echo("SELECTED"); ?>></option>
                                        <option value="A) plantar" <?php if ($tarea_nuevo=="A) plantar") echo("SELECTED"); ?>>A) plantar</option>
                                        <option value="B) quitar_hierbas" <?php if ($tarea_nuevo=="B) quitar_hierbas") echo("SELECTED"); ?>>B) quitar_hierbas</option>
                                        <option value="C) medir_porte" <?php if ($tarea_nuevo=="C) medir_porte") echo("SELECTED"); ?>>C) medir_porte</option>
                                        <option value="D) cosechar" <?php if ($tarea_nuevo=="D) cosechar") echo("SELECTED"); ?>>D) cosechar</option>
                                        <option value="E) vaciar y limpiar" <?php if ($tarea_nuevo=="E) vaciar y limpiar") echo("SELECTED"); ?>>E) vaciar y limpiar</option>
                                        <option value="F) calibrar_sensores" <?php if ($tarea_nuevo=="F) calibrar_sensores") echo("SELECTED"); ?>>F) calibrar_sensores</option>
                                    </select></td>
                                <style type="text/css">
                                        select[name=tarea_nuevo]{
                                            width: 150px;
                                        }
                                </style>
                                           
                                    
                                <td class="celda_datos">
	<?php
		try {
			// Conexión a la base de datos de cultivos
			$conn = Conectarse(SERVIDOR, BD_NOMBRE, BD_USUARIO, BD_PASS);
			// Rellenamos desplegable con el listado de operarios
		    $sql = "SELECT DNIoper, cargooper, nombreoper FROM Operarios ORDER BY DNIoper;";
		    if (!($rs = mysqli_query($conn, $sql)))
		        throw new Exception('Error al buscar el nombre de los operarios.');
	?>
									<select name="DNIoper_nuevo">
										<option value="" <?php if ($DNIoper_nuevo=="") echo("SELECTED"); ?>></option>	
	<?php
			while($fila = mysqli_fetch_array($rs)) {
				echo ("<option value=\"" . $fila['DNIoper'] . "\" ");
				if ($DNIoper_nuevo == $fila['DNIoper'])
					echo("SELECTED ");
				echo ("> ". $fila['cargooper'] . ", " . $fila['nombreoper'] . "</option><br/>");
			}
	?>
									</select>
	<?php
			mysqli_free_result($rs);
			mysqli_close($conn);
		}	// FIN try
        catch (Exception $e) {
			$b_error = true;			
			$el_error = "<p class=\"mensaje\">";
			if (!isset($conn)) $conn="";
			if (!isset($sql)) $sql="";
			$el_error .= getInformationError($e,$conn,$sql);
			$el_error .= "</p>";
			echo($el_error);
		}
	?>
								</td>
                                <style type="text/css">
                                        select[name=DNIoper_nuevo]{
                                            width: 200px;
                                        }
                                </style>
                                
                                
                                <td class="celda_datos"><input type="datetime-local" step="1" id="fechaOperacion_nuevo" name="fechaOperacion_nuevo" maxlength="19" size="19" value="<?php echo($fechaOperacion_nuevo);?>" /></td>
                                
                                <td class="celda_datos">
                                    <center>
                                    <select id="estado_tarea_nuevo" onchange="cambia()" name="estado_tarea_nuevo">
                                        <option value="" <?php if ($estado_tarea_nuevo=="") echo("SELECTED"); ?> ></option>
                                        <option value="Por_hacer" <?php if ($estado_tarea_nuevo=="Por_hacer") echo("SELECTED"); ?> >Por_hacer</option>
                                        <option value="Haciendo" <?php if ($estado_tarea_nuevo=="Haciendo") echo("SELECTED"); ?> >Haciendo</option>
                                        <option value="Hecha" <?php if ($estado_tarea_nuevo=="Hecha") echo("SELECTED"); ?> >Hecha</option>
                                    </select>
                                    </center></td> 
                                
								<td class="celda_datos"> <center> <input type="text" id="tiempo_tarea_nuevo" name="tiempo_tarea_nuevo" maxlength="13" size="5" value="<?php echo($tiempo_tarea_nuevo);?>" readonly/> min.</center></td>
                                
								<td class="celda_datos"> <center> <input type="text" name="costo_materiales_nuevo" maxlength="5" size="5" value="<?php echo($costo_materiales_nuevo);?>" /> &euro;</center></td>
                                
                                <td colspan="2"> <input type="submit" value="Modificar" name="btn_modificaroperacion"/> </td>
                            </tr>
                            <!--
                            <tr>
                                <td colspan="8">&nbsp;</td>
                                <td colspan="2"> <input type="submit" value="Cancelar" name="btn_cancelaroperacion"/>   </td> 
							</tr> -->
                                
                            <tr>
							    <td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
                                <th align="left">Inicio:</th> 
                                <td>&nbsp;</td>
								<td>&nbsp;</td>
                                <td colspan="2"> <input type="submit" value="Cancelar" name="btn_cancelaroperacion"/>   </td> </tr>
                            
                            <tr>
							    <td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td class="celda_datos"><input type="datetime-local" step="1" id="Inicio_nuevo" onchange="cambia()" name="Inicio_nuevo" maxlength="19" size="19" value="<?php echo($Inicio_nuevo);?>" /></td>
                                <td>&nbsp;</td>
								<td>&nbsp;</td></tr>
                            
                            <tr>
							    <td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
                                <th align="left">Final:</th> 
                                <td>&nbsp;</td>
								<td>&nbsp;</td></tr>
                           
                            <tr>
							    <td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td class="celda_datos"><input type="datetime-local" step="1" id="Final_nuevo" onchange="cambia()" name="Final_nuevo" maxlength="19" size="19" value="<?php echo($Final_nuevo);?>" /></td>
                                <td>&nbsp;</td>
								<td>&nbsp;</td></tr> 
                            
								
	<?php
		if($operacionesenbandeja) {
	?>
                          
                            
							<tr>
							    <td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
                                <td class="totaltiempos_operacionesbandeja"><?php printf("%d", $totaltiempos_operacionesbandeja); ?> min.</td>
								<td class="totalcostes_operacionesbandeja"><?php printf("%.2f", $totalcostes_operacionesbandeja); ?> &euro;</td></tr>

	<?php
		}
	?>
						</table>
					</td></tr>
	<?php
		/**************************************************************************
		*********************** FIN OPERACIONES DE BANDEJA ************************
		**************************************************************************/
                
		//$total_ivabandeja = $total_lineasbandeja * $ivaped / 100;
		//$total_bandeja = $total_lineasbandeja + $total_ivabandeja;
	?>
                <!--
				<tr>
					<td colspan="2" class="totales_bandeja">IVA: <?php //printf("%.2f", $total_ivabandeja); ?> &euro;</td></tr>		
				<tr>
				<tr>
					<td colspan="2" class="totales_bandeja">TOTAL PEDIDO: <?php //printf("%.2f", $total_bandeja); ?> &euro;</td></tr>	
                -->
                
				<!--
				<tr>
					<td colspan="2">
						<input type="submit" value="Modificar" name="btn_modificar"/>
						<input type="submit" value="Cancelar" name="btn_cancelar"/></td></tr>
                <tr>
					<td class="nota" colspan="2">
						<br/><sup>*</sup> Campos obligatorios</td></tr>   -->
            <!--    
            </table> -->
               <br/>
               <br/>
            
    <?php
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
						/******************* BOTON PARA IMPRIMIR BANDEJA **************************/
	?>
				<tr>
					<td class="imprimir" colspan="2"> Imprimir formulario <button class="imprimir" title="imprimir bandeja" name="btn_imprimir" id="btn_imprimir" type="button"> <img class="imprimir" src="../images/imprimir1.png"/> </button>
                    </td></tr>
                    
                <br/>
                <br/> 
            
                
	<?php
						/******************* FIN BOTON PARA IMPRIMIR BANDEJA ***********************/
    ?>  
            
        </form>
    </section>
  </div>
</body>
    
  <script type="text/javascript"  name="autorellenar idband">

    let cajaFirst = document.getElementById('idband');
    let cajaSecond = document.getElementById('torre');
    let cajaThird = document.getElementById('alturaband');
    cajaSecond.addEventListener('input', AutoRellena1);
    cajaThird.addEventListener('input', AutoRellena2);


    function AutoRellena1( {target} ) {
        let dato1 = target.value;
        if (dato1.length > 0) {
          cajaFirst.value = dato1 + "-" + cajaThird.value;      
        }else{
          cajaFirst.value  = '';
        }    
    }

    function AutoRellena2( {target} ) {
        let dato2 = target.value;
        if (dato2.length > 0) {
          cajaFirst.value=  cajaSecond.value + "-" + dato2;   
        }else{
          cajaFirst.value = '';
        }    
    }

  </script>
    
   
    
  <script type="text/javascript"  name="comprueba fechaOperacion">

    let caja = document.getElementById('fechaOperacion_nuevo');

	function minimoDosDigitos(n) {
      return (n < 10 ? '0' : '') + n;
    } 
	  
    fechaOperacion_nuevo.addEventListener('input', () => {
        
        var fecha = new Date(); //Fecha actual
        
        var ano = fecha.getFullYear(); //obteniendo año
        var mes = fecha.getMonth()+1; //obteniendo mes
        var dia = fecha.getDate(); //obteniendo dia
        var hora = fecha.getHours(); //obteniendo hora
        
        var current_time =ano+"-"+minimoDosDigitos(mes)+"-"+minimoDosDigitos(dia)+"\T"+minimoDosDigitos(hora);
        
        
        let valor_caja = new Date(caja.value);
        
        var ano_caja = valor_caja.getFullYear(); //obteniendo año
        var mes_caja = valor_caja.getMonth()+1; //obteniendo mes
        var dia_caja = valor_caja.getDate(); //obteniendo dia
        var hora_caja = valor_caja.getHours(); //obteniendo hora
        
        var current_time_caja =ano_caja+"-"+minimoDosDigitos(mes_caja)+"-"+minimoDosDigitos(dia_caja)+"\T"+minimoDosDigitos(hora_caja);
        
        
        if (current_time_caja == current_time) {
        	alert("OPCIONAL: \n\nFecha a realizar operación debería ser al menos \"1 día\" superior al de la fecha de hoy." + 
                  "\n(Para darle tiempo al operario a preparase)");
        }
        
    }); 
              
  </script>
   
    
   
  <script type="text/javascript"  name="autorrellenar fechas (Inicio y Final)">

    let cajaPrimera = document.getElementById('estado_tarea_nuevo');
    cajaPrimera.addEventListener('input', AutoRellenaFechas);
    let cajaSegunda = document.getElementById('Inicio_nuevo');
    let cajaTercera = document.getElementById('Final_nuevo');


    function minimoDosDigitos(n) {
      return (n < 10 ? '0' : '') + n;
    }

    function AutoRellenaFechas( {target} ) {
        var fecha = new Date(); //Fecha actual
        
        var ano = fecha.getFullYear(); //obteniendo año
        var mes = fecha.getMonth()+1; //obteniendo mes
        var dia = fecha.getDate(); //obteniendo dia
        var hora = fecha.getHours(); //obteniendo hora
        var minutos = fecha.getMinutes(); //obteniendo minuto
        var segundos = fecha.getSeconds(); //obteniendo segundo

        var current_time =ano+"-"+minimoDosDigitos(mes)+"-"+minimoDosDigitos(dia)+"\T"+minimoDosDigitos(hora)+":"+minimoDosDigitos(minutos)+":"+minimoDosDigitos(segundos);
        
        let dato = target.value;
        
        if (dato == 'Por_Hacer') {
          cajaSegunda.value  = '';
          cajaTercera.value  = '';
        }else if (dato == 'Haciendo') {
          cajaSegunda.value  = current_time;
          cajaTercera.value  = '';
        }else if (dato == 'Hecha') {
              var fechaInicio = document.getElementById("Inicio_nuevo");
              var valor = fechaInicio.value;
              
              if (valor  == ''){
                  alert("ERROR: no se puede poner Estado=\'Hecha\' sin haber puesto antes Estado=\'Haciendo\' alguna vez.");
                  cajaPrimera.value  = '';
                  cajaSegunda.value  = '';
          		  cajaTercera.value  = ''; 
                }
                else{
                  cajaSegunda.value  = cajaSegunda.value;
                  cajaTercera.value  = current_time;
                }
            
        }else{
          cajaSegunda.value  = '';
          cajaTercera.value  = '';    
        }    
    }
            
 </script>

 <script type="text/javascript"  name="calcula tiempo_tarea">

	  function minimoDosDigitos(n) {
		  return (n < 10 ? '0' : '') + n;
	  }
	  
	  function calcularMinutos(laFecha){
		  var ano_laFecha = laFecha.getFullYear(); //obteniendo año
		  var mes_laFecha = laFecha.getMonth()+1; //obteniendo mes
		  var dia_laFecha = laFecha.getDate(); //obteniendo dia
		  var hora_laFecha = laFecha.getHours(); //obteniendo hora
		  var minutos_laFecha = laFecha.getMinutes(); //obteniendo minuto
		  var segundos_laFecha = laFecha.getSeconds(); //obteniendo segundo
        
		  /***Parseamos a minutos cada variable***/
      	  //(Ejemplo: si estamos en 2022, queremos todos los minutos contenidos en cada año hasta el final de 2021)
      	  var minutos_anoanterior_laFecha = (ano_laFecha - 1)     * 365 * 24 * 60; 
  
      	  //(Ejemplo: si estamos en Diciembre, queremos todos los minutos contenidos en cada mes hasta final de Noviembre)
      	  var minutos_mesanterior_laFecha = (mes_laFecha - 1)      * 30 * 24 * 60;
        
      	  //(Ejemplo: si estamos a día 9/Diciembre, queremos todos los minutos contenidos en cada día de Diciembre hasta el final del 8/Diciembre)
      	  var minutos_diaanterior_laFecha = (dia_laFecha - 1)           * 24 * 60;
        
      	  //(Ejemplo: si estamos a la hora 16h, queremos todos los minutos contenidos en cada hora hasta el final de las 15h)
      	  var minutos_horaanterior_laFecha = (hora_laFecha - 1)              * 60;
       	  var minutos_minutos_laFecha =      minutos_laFecha;
		  var minutos_segundos_laFecha =     segundos_laFecha                      / 60;
        
          /***************************************/      
        
          var minutos_laFecha = minutos_anoanterior_laFecha + minutos_mesanterior_laFecha + minutos_diaanterior_laFecha + minutos_horaanterior_laFecha + minutos_minutos_laFecha + minutos_segundos_laFecha;

		  return minutos_laFecha;
	  }
    
      function cambia(){
		  let losMinutos1, losMinutos2;
		  
		  let cajaInicio = document.getElementById('Inicio_nuevo');
		  let cajaFinal = document.getElementById('Final_nuevo');
		  let cajaTiempoTarea = document.getElementById('tiempo_tarea_nuevo');

		  let valor_cajaInicio = new Date(cajaInicio.value);

		  let valor_cajaFinal = new Date(cajaFinal.value);
          
		  if ((isNaN(valor_cajaInicio)) || (isNaN(valor_cajaFinal))) {
			  cajaTiempoTarea.value  = "";
		  }
		  else {
			  losMinutos1 = calcularMinutos (valor_cajaInicio);
			  losMinutos2 = calcularMinutos (valor_cajaFinal);
              cajaTiempoTarea.value  = parseInt(losMinutos2-losMinutos1);
		  }
	  }
	  
  </script>    
    

</html>