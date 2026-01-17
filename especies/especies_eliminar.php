<?php ////////////////////////////////////////////BASADO EN ARTICULOS_ELIMINAR.php
    include_once ("../bd_funciones.inc");
	// Se Comprueba si se ha pulsado el botón cancelar de la página
	// para volver al listado de las especies
	if (isset($_POST["btn_cancelar"])) {	// se ha pulsado el botón cancelar
		// La función header se debe utilizar antes de abrir la etiqueta <html>
		header("Location: especies_listado.php");
		exit();
	} else {
		try {
			$b_error =false;	//indica si se ha producido algún error en la ejecución del código
			$primer_acceso =false;	// indica si es la primera vez que se accede a la página
			//Obtenemos el valor del código de la especie
			$idespec = (isset($_POST["idespec"])) ? $_POST["idespec"] : $_GET["idespec"];
			// Conexión a la base de datos de cultivos
			$conn = Conectarse(SERVIDOR, BD_NOMBRE, BD_USUARIO, BD_PASS);
			
            if (isset($_POST["btn_eliminar"])) {	// se ha pulsado el botón eliminar
                // Se elimina la especie
                $sql = "DELETE FROM especies WHERE idespec=" . FormatToSQL($idespec,"cadena") . ";";
				if (!mysqli_query($conn, $sql))
					throw new Exception('Error al intentar eliminar el especie.');
				$msg = "especie eliminado con éxito.";
			} else {	// Primera vez que se accede a la página:
						//		1- Se busca la descripción del especie
						// 		2- se pide confirmación para eliminar el especie
				$primer_acceso =true;
				$sql = "SELECT nombreespec FROM especies WHERE idespec=" .
                    FormatToSQL($idespec,"cadena") . ";";
				if (!($rs = mysqli_query($conn, $sql)))
					throw new Exception('Error al intentar obtener el nombre de la especie.');
				if (!$fila = mysqli_fetch_array($rs))
					throw new Exception('No existe especie con codigo ' . $idespec . '.');
				$msg = "Si continúa se eliminará la especie: <strong>" . $fila['nombreespec']  .
					"</strong>.<br/>¿Desea continuar?";
				// se libera el recorset y la conexión a la base de datos
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
    <title>Aplicación cultivos</title>
    <link rel="stylesheet" href="../cultivos.css" />
</head>
<body>
<div id="contenedor-general">
	<?php
		include ("../cultivos_cabecera.inc");
	?>
    <section id="contenido">
		<form class="cultivos_datos" action="especies_eliminar.php" method="post">
			<table class="cultivos_datos">
				<caption>Especies: eliminar</caption>
				<tr>
					<td><?php echo($msg) ?></td></tr>
	<?php
		if (($primer_acceso) && (!$b_error)) {	// se muestran botones de confirmación y cancelación
	?>
				<tr>
				    <td align="center">
						<input type="hidden" value="<?php echo($idespec); ?>" name="idespec"/>
						<input type="submit" value="Eliminar" name="btn_eliminar"/>
						<input type="submit" value="Cancelar" name="btn_cancelar"/></td></tr>
	<?php
		} else {	// ya se ha pulsado el botón de eliminar el especie
	?>
				<tr>
					<td align="center">
						<input type="submit" value="Volver al listado de especies" name="btn_cancelar"/>
					</td></tr>
	<?php
		}
	?>
			</table>
		</form>
    </section>
</div>
</body>
</html>