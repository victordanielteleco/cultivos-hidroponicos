<?php    
    include_once ("../bd_funciones.inc");
    
    if (isset($_POST["btn_cancelar"])) { // se ha pulsado el botón cancelar
		//La función header se debe utilizar antes de abrir la etiqueta <html>
        header("Location: especies_listado.php");
		exit();
    } else
		try {
			$msg = "";
			// se toman valores pasados en el formulario (si existen)
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
           
			
			if (isset($_POST["btn_insertar"])) { // se ha pulsado el botón insertar
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
				// Conexión a la base de datos de cultivos
				$conn = Conectarse(SERVIDOR, BD_NOMBRE, BD_USUARIO, BD_PASS);
				// se comprueba si ya existe especie con ese código
				$sql = "SELECT * FROM Especies WHERE idespec=" .
                    FormatToSQL($idespec,"cadena") .";";
				if (!($rs = mysqli_query($conn, $sql)))
					throw new Exception("Error al intentar comprobar si existe algúna especie con ese código." . $sql);
				if (mysqli_num_rows($rs) > 0)
					throw new Exception("Imposible insertar la especie.<br>Ya existe una especie con código $idespec.");
			    mysqli_free_result($rs);
				// se inserta la nueva especie
                $sql = "INSERT INTO Especies (idespec, nombreespec, porteadulto, dias, humedad_ambiente, horas_luz_dia, intensidad_de_luz, N, P, K, PH, EC, litros_por_hora) VALUES (" .
                    FormatToSQL($idespec,"cadena") . ", "  .
                    FormatToSQL($nombreespec,"cadena") . ", "  .
                    FormatToSQL($porteadulto,"numerico") . ", "  .
                    FormatToSQL($dias,"numerico") . ", "  .
                    FormatToSQL($humedad_ambiente,"numerico") . ", "  .
                    FormatToSQL($horas_luz_dia,"numerico") . ", "  .
                    FormatToSQL($intensidad_de_luz,"numerico") . ", "  .
                    FormatToSQL($N,"numerico") . ", "  .
                    FormatToSQL($P,"numerico") . ", "  .
                    FormatToSQL($K,"numerico") . ", "  .
                    FormatToSQL($PH,"numerico") . ", "  .
                    FormatToSQL($EC,"numerico") . ", "  .
                    FormatToSQL($litros_por_hora,"numerico") . ");";
				if (!mysqli_query($conn, $sql))
					throw new Exception("Error al intentar insertar la Especie.");
				// se libera la conexión a la base de datos
				mysqli_close($conn);
				// si se ha insertado la especie correctamente se envía el usuario a
				// especies_editar.php
				$msg = "Especie insertada con éxito.";
				header("Location: especies_editar.php?idespec=" .
					$idespec . "&msg=" . $msg);
			}
		}	// FIN try
        catch (Exception $e) {
			$b_error = true;
			$msg = getInformationError($e,$conn,$sql);
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
        <form class="cultivos_datos" action="especies_insertar.php" method="post">
			<table class="cultivos_datos">
                <caption>Especies: insertar</caption>
                <tr>
                    <td class="celda_etiq"><label class="oblig" for="idespec">Id especie<sup>*</sup>:</label></td>
                    <td class="celda_datos"><input type="text" name="idespec" placeholder="AAA_1" minlength="5" maxlength="5" size="5" value="<?php echo($idespec);?>" /><span class="nota"> Ej: RUC_1</span></td></tr>
                <tr>
                    <td class="celda_etiq"><label class="oblig" for="nombreespec">Nombre especie<sup>*</sup>:</label></td>
                    <td class="celda_datos"><input type="text" name="nombreespec" maxlength="40" size="40" value="<?php echo($nombreespec);?>" /><span class="nota"> Ej: RÚCULA_(Eruca Sativa)</span></td></tr>
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
</body>
</html>