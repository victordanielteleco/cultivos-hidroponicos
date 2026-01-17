<?php    
    include_once ("../bd_funciones.inc");
    
    if (isset($_POST["btn_cancelar"])) { // se ha pulsado el botón cancelar
		//La función header se debe utilizar antes de abrir la etiqueta <html>
        header("Location: operarios_listado.php");
		exit();
    } else {
		try {
			// se toman valores pasados en el formulario (si existen)
			$msg = (isset($_GET["msg"]))?$_GET["msg"]:"";
			$DNIoper_orig = (isset($_POST["DNIoper_orig"])) ? $_POST["DNIoper_orig"] : $_GET["DNIoper"];
			$DNIoper = (isset($_POST["DNIoper"]))?$_POST["DNIoper"]:"";
			$cargooper = (isset($_POST["cargooper"]))?$_POST["cargooper"]:"";
			$nombreoper = (isset($_POST["nombreoper"]))?$_POST["nombreoper"]:"";
			$telefoper = (isset($_POST["telefoper"]))?$_POST["telefoper"]:"";
			$diroper = (isset($_POST["diroper"]))?$_POST["diroper"]:"";
			$localidadoper = (isset($_POST["localidadoper"]))?$_POST["localidadoper"]:"";
			$provinciaoper = (isset($_POST["provinciaoper"]))?$_POST["provinciaoper"]:"";
            
			// Conexión a la base de datos de cultivos
			$conn = Conectarse(SERVIDOR, BD_NOMBRE, BD_USUARIO, BD_PASS);
		
			if (isset($_POST["btn_modificar"])) { // se ha pulsado el botón modificar
				// Se comprueba si el valor de los campos es correcto
				$msg_datos="";
				ComprobarDato($DNIoper, "DNI", true, "cadena", $msg_datos);
				ComprobarDato($cargooper, "Cargo", true, "cadena", $msg_datos);
				ComprobarDato($nombreoper, "Nombre", true, "cadena", $msg_datos);
				ComprobarDato($telefoper, "Teléfono", true, "cadena", $msg_datos);
                ComprobarDato($diroper, "Dirección", true, "cadena", $msg_datos);
				ComprobarDato($localidadoper, "Localidad", true, "cadena", $msg_datos);
				ComprobarDato($provinciaoper, "Provincia", true, "cadena", $msg_datos);
				if ($msg_datos != "")
					throw new Exception($msg_datos);
				// se comprueba si ya existe operario con ese DNI
				// siempre que se haya modificado el valor del DNI del operario
				if ($DNIoper_orig != $DNIoper) {
					$sql = "SELECT * FROM Operarios WHERE DNIoper=" .
                        FormatToSQL($DNIoper,"cadena") . ";";
					if (!($rs = mysqli_query($conn, $sql)))
						throw new Exception("Error al intentar comprobar si existe algún operario con ese DNI.");
					if (mysqli_num_rows($rs) > 0)
						throw new Exception("Imposible modificar el operario.<br>Ya existe un operario con DNI $DNIoper.");
					mysqli_free_result($rs);
				}
				// se modifica el operario
				$sql = "UPDATE Operarios SET DNIoper=" . FormatToSQL($DNIoper,"cadena") .
                    ", cargooper=" . FormatToSQL($cargooper,"cadena") .
                    ", nombreoper=" . FormatToSQL($nombreoper,"cadena") .
                    ", telefoper=" . FormatToSQL($telefoper,"cadena") .
                    ", diroper=" . FormatToSQL($diroper,"cadena") .
                    ", localidadoper=" . FormatToSQL($localidadoper,"cadena") .
                    ", provinciaoper=" . FormatToSQL($provinciaoper,"cadena") .
					" WHERE DNIoper=" . FormatToSQL($DNIoper_orig,"cadena") . ";";
				if (!mysqli_query($conn, $sql))
					throw new Exception("Error al intentar modificar el Operario." . $sql);
				// se ha modificado el operario correctamente - Se modifica valor de $DNIoper_orig
				$DNIoper_orig = $DNIoper;
				$msg = "Operario modificado con éxito.";
			} else {	// primera vez que se accede a la página
						//	- se obtienen los datos del operario
				$sql = "SELECT * FROM Operarios WHERE DNIoper=" .
                        FormatToSQL($DNIoper_orig,"cadena") . ";";
				if (!($rs = mysqli_query($conn, $sql)))
					throw new Exception("Error al intentar obtener los datos del operario");
				if (!($fila = mysqli_fetch_array($rs)))
					throw new Exception("No existe ningún operario con código $DNIoper_orig");
				// datos del operario
				$DNIoper=$fila['DNIoper'];
				$cargooper=$fila['cargooper'];
				$nombreoper=$fila['nombreoper'];
				$telefoper=$fila['telefoper'];
                $diroper=$fila['diroper'];
                $localidadoper=$fila['localidadoper'];
                $provinciaoper=$fila['provinciaoper'];
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
        <form class="cultivos_datos" action="operarios_editar.php" method="post">
			<input type="hidden" name="DNIoper_orig" value="<?php echo($DNIoper_orig);?>"/>
			<table class="cultivos_datos">
				<caption>Operarios: modificar</caption>
				<tr>
                    <td class="celda_etiq"><label class="oblig" for="DNIoper">DNI<sup>*</sup>:</label></td>
                    <td class="celda_datos"><input type="text" name="DNIoper" pattern="[0-9]{8}[A-Z]{1}" maxlength="9" size="9" value="<?php echo($DNIoper);?>" /><span class="nota"> 00000000A</span></td></tr>
                <tr>
                    <td class="celda_etiq"><label class="oblig" for="cargooper">Cargo<sup>*</sup>:</label></td>
                    <td class="celda_datos"><input type="text" name="cargooper" maxlength="30" size="30" value="<?php echo($cargooper);?>" /></td></tr>
                <tr>
                    <td class="celda_etiq"><label class="oblig" for="nombreoper">Nombre<sup>*</sup>:</label></td>
                    <td class="celda_datos"><input type="text" name="nombreoper" maxlength="30" size="30" value="<?php echo($nombreoper);?>" /></td></tr>
                <tr>
                    <td class="celda_etiq"><label class="oblig" for="telefoper">Teléfono<sup>*</sup>:</label></td>
                    <td class="celda_datos"><input type="text" name="telefoper" pattern="[0-9]{3} [0-9]{3} [0-9]{3}" maxlength="11" size="11" value="<?php echo($telefoper);?>" /><span class="nota"> xxx xxx xxx</span></td></tr>
				<tr>
                    <td class="celda_etiq"><label class="oblig" for="diroper">Dirección<sup>*</sup>:</label></td>
                    <td class="celda_datos"><input type="text" name="diroper" maxlength="60" size="60" value="<?php echo($diroper);?>" /></td></tr>
				<tr>
                    <td class="celda_etiq"><label class="oblig" for="localidadoper">Localidad<sup>*</sup>:</label></td>
                    <td class="celda_datos"><input type="text" name="localidadoper" maxlength="15" size="15" value="<?php echo($localidadoper);?>" /></td></tr>
				<tr>
                    <td class="celda_etiq"><label class="oblig" for="provinciaoper">Provincia<sup>*</sup>:</label></td>
                    <td class="celda_datos"><input type="text" name="provinciaoper" maxlength="10" size="10" value="<?php echo($provinciaoper);?>" /></td></tr>
                <tr>
					<td colspan="2">
						<input type="submit" value="Modificar" name="btn_modificar"/>
						<input type="submit" value="Cancelar" name="btn_cancelar"/></td></tr>
                <tr>
				    <td class="nota" colspan="2">
					    <br/><sup>*</sup> Campos obligatorios</td></tr>
            </table>
        </form>
    </section>
</div>
</body>
</html>