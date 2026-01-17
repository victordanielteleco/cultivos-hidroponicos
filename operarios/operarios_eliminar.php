<?php ////////////////////////BASADO EN PROVEEDORES_ELIMINAR.PHP
    include_once ("../bd_funciones.inc");
	// Se comprueba si se ha pulsado el botón cancelar de la página
	// para volver al listado de los operarios
	if (isset($_POST["btn_cancelar"])) {	// se ha pulsado el botón cancelar
		// La función header se debe utilizar antes de abrir la etiqueta <html>
		header("Location: operarios_listado.php");
		exit();
	} else {
		try {
			$b_error =false;	//indica si se ha producido algún error en la ejecución del código
			$primer_acceso =false;	// indica si es la primera vez que se accede a la página
			//Obtenemos el valor del código del operario
			$DNIoper = (isset($_POST["DNIoper"])) ? $_POST["DNIoper"] : $_GET["DNIoper"];
			// Conexión a la base de datos de cultivos
			$conn = Conectarse(SERVIDOR, BD_NOMBRE, BD_USUARIO, BD_PASS);
			
            if (isset($_POST["btn_eliminar"])) {	// se ha pulsado el botón eliminar
                // Se elimina el operario
                $sql = "DELETE FROM operarios WHERE DNIoper=\"$DNIoper\"";
				if (!mysqli_query($conn, $sql))
					throw new Exception('Error al intentar eliminar el operario.');
				$msg = "operario eliminado con éxito.";
			} else {	// Primera vez que se accede a la página:
						//		1- Se busca el nombre del operario
						// 		2- se pide confirmación para eliminar el operario
				$primer_acceso =true;
				$sql = "SELECT nombreoper FROM operarios WHERE DNIoper=" .
                    FormatToSQL($DNIoper,"cadena") .";";
				if (!($rs = mysqli_query($conn, $sql)))
					throw new Exception('Error al intentar obtener el nombre del operario.');
				if (!$fila = mysqli_fetch_array($rs))
					throw new Exception('No existe operario con codigo ' . $DNIoper . '.');
				$msg = "Si continúa se eliminará el operario: <strong>" . $fila['nombreoper']  .
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
		<form class="cultivos_datos" action="operarios_eliminar.php" method="post">
			<table class="cultivos_datos">
				<caption>Operarios: eliminar</caption>
				<tr>
					<td><?php echo($msg) ?></td></tr>
	<?php
		if (($primer_acceso) && (!$b_error)) {	// se muestran botones de confirmación y cancelación
	?>
				<tr>
				    <td align="center">
						<input type="hidden" value="<?php echo($DNIoper); ?>" name="DNIoper"/>
						<input type="submit" value="Eliminar" name="btn_eliminar"/>
						<input type="submit" value="Cancelar" name="btn_cancelar"/></td></tr>
	<?php
		} else {	//Ya se ha pulsado el botón de eliminar el operario
	?>
				<tr>
					<td align="center">
						<input type="submit" value="Volver al listado de operarios" name="btn_cancelar"/>
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