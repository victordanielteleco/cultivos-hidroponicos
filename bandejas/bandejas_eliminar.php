<?php /////////////////////////////////////////////BASADO EN PEDIDOS_ELIMINAR.PHP
    include_once ("../bd_funciones.inc");
	// Se comprueba si se ha pulsado el botón cancelar de la página
	// para volver al listado de los bandejas
	if (isset($_POST["btn_cancelar"])) {	// se ha pulsado el botón cancelar
		// La función header se debe utilizar antes de abrir la etiqueta <html>
		header("Location: bandejas_listado.php");
		exit();
	} else {
		try {
			$b_error =false;	//indica si se ha producido algún error en la ejecución del código
			$primer_acceso =false;	// indica si es la primera vez que se accede a la página
			$plantas_asociadas=false;// indica si hay plantas asociadas a la bandeja
            $operaciones_asociadas=false;// indica si hay operaciones asociadas a la bandeja
            
            // hacerlo tanto para plantas como para operaciones
			//Obtenemos el valor del id de la bandeja
			$idband = (isset($_POST["idband"])) ? $_POST["idband"] : $_GET["idband"];
			// Conexión a la base de datos de cultivos
			$conn = Conectarse(SERVIDOR, BD_NOMBRE, BD_USUARIO, BD_PASS);
			
            if (isset($_POST["btn_eliminar"])) {	// se ha pulsado el botón eliminar
                // Se elimina la bandeja
                $sql = "DELETE FROM Bandejas WHERE idband=" .
                    FormatToSQL($idband,"cadena") .";";
				if (!mysqli_query($conn, $sql))
					throw new Exception('Error al intentar eliminar la bandeja.');
				$msg = "bandeja eliminada con éxito.";
                
			} else {	// Primera vez que se accede a la página:
						//		1- Se comprueba si la bandeja tiene plantas u operaciones asociadas
						// 		2- se pide confirmación para eliminar el bandeja
				$primer_acceso =true;
				$sql = "SELECT count(*) AS numplantas FROM Plantas ".
                    "WHERE idband=" .
                    FormatToSQL($idband,"cadena") .";";
                $sql2 = "SELECT count(*) AS numoperaciones FROM Operaciones ".
                    "WHERE idband=" .
                    FormatToSQL($idband,"cadena") .";";
				if (!($rs = mysqli_query($conn, $sql)))
					throw new Exception('Error al intentar obtener las plantas que tiene ' .
										'asociadas la bandeja.');
                if (!($rs2 = mysqli_query($conn, $sql2)))
					throw new Exception('Error al intentar obtener las operaciones que tiene ' .
										'asociadas la bandeja.');
				$fila = mysqli_fetch_array($rs);
				if ($fila['numplantas']>0) {
					$plantas_asociadas = true;
                }
                $fila2 = mysqli_fetch_array($rs2);
                if ($fila2['numoperaciones']>0) {
					$operaciones_asociadas = true;
                }
                if ( ($plantas_asociadas == true) && ($operaciones_asociadas == true) ) {
					throw new Exception ("No es posible eliminar la bandeja.<br/>Existen " .
						 $fila['numplantas'] . " plantas y " .
                         $fila2['numoperaciones'] . " operaciones asociadas a la misma");
				}
                if ($plantas_asociadas == true) {
					throw new Exception ("No es posible eliminar la bandeja.<br/>Existen " .
						 $fila['numplantas'] . " plantas asociadas a la misma");
				}
                if ($operaciones_asociadas == true) {
					throw new Exception ("No es posible eliminar la bandeja.<br/>Existen " .
						 $fila2['numoperaciones'] . " operaciones asociadas a la misma");
				}
				$msg = "Si continúa se eliminará la bandeja: <strong>" . $idband  .
					"</strong>.<br/>¿Desea continuar?";
				// se libera el recorset y la conexión a la base de datos
				mysqli_free_result($rs);
                mysqli_free_result($rs2);
			}
			
			// se libera la conexión a la base de datos
			mysqli_close($conn);
		}	// FIN try
        catch (Exception $e) {
			$b_error = true;
			$msg = getInformationError($e,$conn,$sql);
            $msg2 = getInformationError($e,$conn,$sql2);    
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
		<form class="cultivos_datos" action="bandejas_eliminar.php" method="post">
			<table class="cultivos_datos">
				<caption>Bandejas: eliminar</caption>
				<tr>
					<td><?php echo($msg) ?></td></tr>
	<?php
		if (($primer_acceso) && (!$b_error)) {	// se muestran botones de confirmación y cancelación
	?>
				<tr>
				    <td align="center">
						<input type="hidden" value="<?php echo($idband); ?>" name="idband"/>
						<input type="submit" value="Eliminar" name="btn_eliminar"/>
						<input type="submit" value="Cancelar" name="btn_cancelar"/></td></tr>
	<?php
		} else {	// La bandeja tiene plantas u operaciones asociadas, o ya se ha pulsado el botón eliminar
					// Se muestra botón para volver al listado de bandejas
	?>
				<tr>
				    <td align="center">
						<input type="submit" value="Volver al listado bandejas" name="btn_cancelar"/>
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