<?php    
    include_once ("../bd_funciones.inc");
    
    if (isset($_POST["btn_cancelar"])) { // se ha pulsado el botón cancelar
		//La función header se debe utilizar antes de abrir la etiqueta <html>        
        header("Location: bandejas_listado.php");
		exit();
    } else {
		try {
			$msg = "";
			// se toman valores pasados en el formulario (si existen)
			$idband = (isset($_POST["idband"]))?$_POST["idband"]:"";
            $torre = (isset($_POST["torre"]))?$_POST["torre"]:"";
            $alturaband = (isset($_POST["alturaband"]))?$_POST["alturaband"]:"";
            $idespecie = (isset($_POST["idespecie"]))?$_POST["idespecie"]:"";
            $estadoband = (isset($_POST["estadoband"]))?$_POST["estadoband"]:"";
			$fechaPlantado = (isset($_POST["fechaPlantado"]))?$_POST["fechaPlantado"]:"";
			$fechaCosechado = (isset($_POST["fechaCosechado"]))?$_POST["fechaCosechado"]:"";
			
			if (isset($_POST["btn_insertar"])) { // se ha pulsado el botón insertar
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
						$datetime2 = DateTime::createFromFormat("d/m/Y", $fechaCosechado);
					}
					$intervalo= $datetime1->diff($datetime2);
					if ($intervalo->format('%r%a') <= 0)
						$msg_datos .= "Error: la fecha prevista de cosechado deber superior a la " .
							"fecha de plantado.<br/>";
				}
				if ($msg_datos != "")
					throw new Exception($msg_datos);
				// Conexión a la base de datos de cultivos
				$conn = Conectarse(SERVIDOR, BD_NOMBRE, BD_USUARIO, BD_PASS);
				// se comprueba si ya existe una bandeja con ese id
				$sql = "SELECT * FROM bandejas WHERE idband=" .
                    FormatToSQL($idband,"cadena") .";";
				if (!($rs = mysqli_query($conn, $sql)))
					throw new Exception("Error al intentar comprobar si existe alguna bandeja con ese id.");
				if (mysqli_num_rows($rs) > 0)
					throw new Exception("Imposible insertar la bandeja.<br>Ya existe una bandeja con id $idband.");
			    mysqli_free_result($rs);
				// se inserta el nueva bandeja
			    $sql = "INSERT INTO bandejas (idband, torre, alturaband, idespecie, estadoband, fechaPlantado, fechaCosechado) VALUES (" .
					FormatToSQL($idband,"cadena") . ", " .
                    FormatToSQL($torre,"numerico") . ", " .
                    FormatToSQL($alturaband,"numerico") . ", " .
                    FormatToSQL($idespecie,"cadena") . ", " .
                    FormatToSQL($estadoband,"cadena") . ", " .
                    FormatToSQL($fechaPlantado,"fecha") . ", " .
                    FormatToSQL($fechaCosechado,"fecha") . ");";
				if (!mysqli_query($conn, $sql))
					throw new Exception("Error al intentar insertar la Bandeja.");
				// se libera la conexión a la base de datos
				mysqli_close($conn);
				// si se ha insertado la bandeja correctamente se envía el usuario a
				// bandejas_editar.php
				$msg = "Bandeja insertada con éxito.";
				header("Location: bandejas_editar.php?idband=" .
					$idband . "&msg=" . $msg);
			}
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
	?>
        <form class="cultivos_datos" action="bandejas_insertar.php" method="post">
			<table class="cultivos_datos">
                <caption>bandejas: insertar</caption>
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
                <tr>
				    <td align="center" colspan="2">
						<input type="submit" value="Insertar" name="btn_insertar"/>
						<input type="submit" value="Cancelar" name="btn_cancelar"/></td></tr>
                <tr>
					<td class="nota" colspan="2">
						<br/><sup>*</sup> Campos obligatorios</td></tr>
            </table>
        </form>                    
    </section>
  </div>
      
  <script type="text/javascript">

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
    
</body>    
</html>