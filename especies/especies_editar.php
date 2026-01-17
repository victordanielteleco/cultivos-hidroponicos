<?php    
    include_once ("../bd_funciones.inc");
    
    if (isset($_POST["btn_cancelar"])) { // se ha pulsado el botón cancelar
		//La función header se debe utilizar antes de abrir la etiqueta <html>
        header("Location: especies_listado.php");
		exit();
    } else {
		try {
			// se toman valores pasados en el formulario (si existen)
			$msg = (isset($_GET["msg"]))?$_GET["msg"]:"";
			$idespec_orig = (isset($_POST["idespec_orig"])) ? $_POST["idespec_orig"] : $_GET["idespec"];
			$idespec = (isset($_POST["idespec"]))?$_POST["idespec"]:"";
			$nombreespec = (isset($_POST["nombreespec"]))?$_POST["nombreespec"]:"";
			$porteadulto = (isset($_POST["porteadulto"]))?$_POST["porteadulto"]:"";
			$dias = (isset($_POST["dias"]))?$_POST["dias"]:"";
            $humedad_ambiente = (isset($_POST["humedad_ambiente"]))?$_POST["humedad_ambiente"]:"";
            $horas_luz_dia = (isset($_POST["horas_luz_dia"]))?$_POST["horas_luz_dia"]:"";
            $intensidad_de_luz = (isset($_POST["intensidad_de_luz"]))?$_POST["intensidad_de_luz"]:"";
            $N = (isset($_POST["N"]))?$_POST["N"]:"";
            $P = (isset($_POST["P"]))?$_POST["P"]:"";
            $K = (isset($_POST["K"]))?$_POST["K"]:"";
            $PH = (isset($_POST["PH"]))?$_POST["PH"]:"";
            $EC = (isset($_POST["EC"]))?$_POST["EC"]:"";
            $litros_por_hora = (isset($_POST["litros_por_hora"]))?$_POST["litros_por_hora"]:"";
			
			// Conexión a la base de datos de cultivos
			$conn = Conectarse(SERVIDOR, BD_NOMBRE, BD_USUARIO, BD_PASS);
		
			if (isset($_POST["btn_modificar"])) { // se ha pulsado el botón modificar
				// Se comprueba si el valor de los campos es correcto
				$msg_datos="";
				ComprobarDato($idespec, "Id especie", true, "cadena", $msg_datos);
				ComprobarDato($nombreespec, "Nombre especie", true, "cadena", $msg_datos);
				ComprobarDato($porteadulto, "Porte tamaño adulto", true, "numerico", $msg_datos);
				ComprobarDato($dias, "Dias crecimiento", true, "numerico", $msg_datos);
                ComprobarDato($humedad_ambiente, "Humedad ambiente", true, "numerico", $msg_datos);
                ComprobarDato($horas_luz_dia, "Horas de luz diarias", true, "numerico", $msg_datos);
                ComprobarDato($intensidad_de_luz, "Intensidad de luz", true, "numerico", $msg_datos);
                ComprobarDato($N, "N (Nitrógeno)", true, "numerico", $msg_datos);
                ComprobarDato($P, "P (Fósforo)", true, "numerico", $msg_datos);
                ComprobarDato($K, "K (Potasio)", true, "numerico", $msg_datos);
                ComprobarDato($PH, "PH", true, "numerico", $msg_datos);
                ComprobarDato($EC, "EC (ElectroCond.)", true, "numerico", $msg_datos);
                ComprobarDato($litros_por_hora, "Litros por hora", true, "numerico", $msg_datos);
				if ($msg_datos != "")
					throw new Exception($msg_datos);
				// se comprueba si ya existe especie con ese idespec
				// siempre que se haya modificado el valor del idespec de la especie
				if ($idespec_orig != $idespec) {
					$sql = "SELECT * FROM especies WHERE idespec=" .
                    FormatToSQL($idespec,"cadena") .";";
					if (!($rs = mysqli_query($conn, $sql)))
						throw new Exception("Error al intentar comprobar si existe alguna especie con ese idespec.");
					if (mysqli_num_rows($rs) > 0)
						throw new Exception("Imposible modificar la especie.<br>Ya existe una especie con idespec $idespec.");
					mysqli_free_result($rs);
				}
				// se modifica la especie
				$sql = "UPDATE especies SET idespec=". FormatToSQL($idespec,"cadena") .
                    ", nombreespec=" . FormatToSQL($nombreespec,"cadena") .
                    ", porteadulto=" . FormatToSQL($porteadulto,"numerico") .
                    ", dias=" . FormatToSQL($dias,"numerico") .
                    ", humedad_ambiente=" . FormatToSQL($humedad_ambiente,"numerico") .
                    ", horas_luz_dia=" . FormatToSQL($horas_luz_dia,"numerico") .
                    ", intensidad_de_luz=" . FormatToSQL($intensidad_de_luz,"numerico") .
                    ", N=" . FormatToSQL($N,"numerico") .
                    ", P=" . FormatToSQL($P,"numerico") .
                    ", K=" . FormatToSQL($K,"numerico") .
                    ", PH=" . FormatToSQL($PH,"numerico") .
                    ", EC=" . FormatToSQL($EC,"numerico") .
                    ", litros_por_hora=" . FormatToSQL($litros_por_hora,"numerico") .
					"WHERE idespec=" . FormatToSQL($idespec_orig,"cadena") . ";";
				if (!mysqli_query($conn, $sql))
					throw new Exception("Error al intentar modificar la Especie.");
				// se ha modificado la especie correctamente - Se modifica valor de $idespec_orig
				$idespec_orig = $idespec;
				$msg = "Especie modificada con éxito.";
			} else {	// primera vez que se accede a la pagina
						//	- se obtienen los datos de la especie
				$sql = "SELECT * FROM Especies WHERE idespec=" . FormatToSQL($idespec_orig,"cadena") . ";";
				if (!($rs = mysqli_query($conn, $sql)))
					throw new Exception("Error al intentar obtener los datos de la especie");
				if (!($fila = mysqli_fetch_array($rs)))
					throw new Exception("No existe ninguna especie con idespec $idespec_orig");
				// datos de la especie
				//$idespec_orig=$fila['idespec'];
				$idespec=$fila['idespec'];
				$nombreespec=$fila['nombreespec'];
				$porteadulto=$fila['porteadulto'];
				$dias=$fila['dias'];
				$humedad_ambiente=$fila['humedad_ambiente'];
				$horas_luz_dia=$fila['horas_luz_dia'];
				$intensidad_de_luz=$fila['intensidad_de_luz'];
				$N=$fila['N'];
				$P=$fila['P'];
				$K=$fila['K'];
                $PH=$fila['PH'];
                $EC=$fila['EC'];
                $litros_por_hora=$fila['litros_por_hora'];
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
        <form class="cultivos_datos" action="especies_editar.php" method="post">
			<input type="hidden" name="idespec_orig" value="<?php echo($idespec_orig);?>"/>
			<table class="cultivos_datos">
				<caption>Especies: modificar</caption>
                <tr>
                    <td class="celda_etiq"><label class="oblig" for="idespec">Id especie<sup>*</sup>:</label></td>
                    <td class="celda_datos"><input type="text" name="idespec" maxlength="5" size="5" value="<?php echo($idespec);?>" /></td></tr>
                <tr>
                    <td class="celda_etiq"><label class="oblig" for="nombreespec">Nombre especie<sup>*</sup>:</label></td>
                    <td class="celda_datos"><input type="text" name="nombreespec" maxlength="40" size="40" value="<?php echo($nombreespec);?>" /></td></tr>
                <tr>
                    <td class="celda_etiq"><label class="oblig" for="porteadulto">Porte tamaño adulto<sup>*</sup>:</label></td>
                    <td class="celda_datos"><input type="text" name="porteadulto" step="any" maxlength="5" size="5" value="<?php echo($porteadulto);?>" /><span class="nota"> (en mm)</span></td></tr>
                <tr>
                    <td class="celda_etiq"><label class="oblig" for="dias">Días crecimiento<sup>*</sup>:</label></td>
                    <td class="celda_datos"><input type="text" name="dias" maxlength="3" size="3" value="<?php echo($dias);?>" /><span class="nota"> (días)</span></td></tr>
                <tr>
                    <td class="celda_etiq"><label class="oblig" for="humedad_ambiente">Humedad ambiente<sup>*</sup>:</label></td>
                    <td class="celda_datos"><input type="text" name="humedad_ambiente" maxlength="5" size="5" value="<?php echo($humedad_ambiente);?>" /><span class="nota"> % humedad aire</span></td></tr>
                <tr>
                    <td class="celda_etiq"><label class="oblig" for="horas_luz_dia">Horas de luz diarias<sup>*</sup>:</label></td>
                    <td class="celda_datos"><input type="text" name="horas_luz_dia" maxlength="4" size="4" value="<?php echo($horas_luz_dia);?>" /><span class="nota"> (h)</span></td></tr>
                <tr>
                    <td class="celda_etiq"><label class="oblig" for="intensidad_de_luz">Intensidad de luz<sup>*</sup>:</label></td>
                    <td class="celda_datos"><input type="text" name="intensidad_de_luz" maxlength="3" size="3" value="<?php echo($intensidad_de_luz);?>" /><span class="nota"> % intensidad de lámpara (100% = 182mA = 40W/220V)</span></td></tr>
                <tr>
                    <td class="celda_etiq"><label class="oblig" for="N">N (Nitrógeno)<sup>*</sup>:</label></td>
                    <td class="celda_datos"><input type="text" name="N" maxlength="5" size="5" value="<?php echo($N);?>" /><span class="nota"> (ppm en agua)</span></td></tr>
                <tr>
                    <td class="celda_etiq"><label class="oblig" for="P">P (Fósforo)<sup>*</sup>:</label></td>
                    <td class="celda_datos"><input type="text" name="P" maxlength="5" size="5" value="<?php echo($P);?>" /><span class="nota"> (ppm en agua)</span></td></tr>
                <tr>
                    <td class="celda_etiq"><label class="oblig" for="K">K (Potasio)<sup>*</sup>:</label></td>
                    <td class="celda_datos"><input type="text" name="K" maxlength="5" size="5" value="<?php echo($K);?>" /><span class="nota"> (ppm en agua)</span></td></tr>
                <tr>
                    <td class="celda_etiq"><label class="oblig" for="PH">PH<sup>*</sup>:</label></td>
                    <td class="celda_datos"><input type="number" name="PH" min="0" step="any" max="14" value="<?php echo($PH);?>" /><span class="nota"> (0 ácido - 14 básico)</span></td></tr>
                        <style type="text/css">
                            input[type=number]{
                                width: 40px;
                            }
                        </style>    
                 
                <tr>
                    <td class="celda_etiq"><label class="oblig" for="EC">EC (ElectroCond.)<sup>*</sup>:</label></td>
                    <td class="celda_datos"><input type="text" name="EC" maxlength="5" size="5" value="<?php echo($EC);?>" /><span class="nota"> (Siemens/cm)</span></td></tr>
                <tr>
                    <td class="celda_etiq"><label class="oblig" for="litros_por_hora">Litros por hora<sup>*</sup>:</label></td>
                    <td class="celda_datos"><input type="text" name="litros_por_hora" maxlength="5" size="5" value="<?php echo($litros_por_hora);?>" /> <span class="nota"> (l/h)</span></td></tr>
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