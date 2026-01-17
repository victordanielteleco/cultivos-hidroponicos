<?php
    include_once ("../bd_funciones.inc");
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
		try {
		    // Conexión a la base de datos de cultivos
		    $conn = Conectarse(SERVIDOR, BD_NOMBRE, BD_USUARIO, BD_PASS);
		    // Obtención listado de proveedores ordenados por su descripción
		    $sql = "SELECT DNIoper, cargooper, nombreoper, telefoper, diroper, localidadoper, provinciaoper FROM " .
		        "operarios ORDER BY DNIoper";
		    if (!($rs = mysqli_query($conn, $sql)))
		        throw new Exception('Error al realizar la consulta.');
		    // Mostramos el listado de proveedores
	?>
        <table class="cultivos_datos">
            <caption>Operarios: listado</caption>
	<?php	
		    if (mysqli_num_rows($rs) > 0) {
    ?>
            <tr>
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th class = "encab_listado">DNI</th>
                        <th class = "encab_listado">Cargo del operario</th>
                        <th class = "encab_listado">Nombre del operario</th>
                        <th class = "encab_listado">Teléfono del operario</th>
                        <th class = "encab_listado">Dirección del operario</th>
                        <th class = "encab_listado">Localidad Operario</th>
                        <th class = "encab_listado">Provincia del Operario</th>
                        <th>&nbsp;</th>
                </thead>
            </tr>
    <?php
	    
				while($fila = mysqli_fetch_array($rs)) {
    ?>
			<tr>
			    <td><a href="./operarios_editar.php?DNIoper=<?php echo($fila['DNIoper']); ?>">
				    <img src="../images/editar.png" alt="editar"/></a></td>
				<td class="celda_listado"><?php echo($fila['DNIoper']); ?></td>
				<td class="celda_listado"><?php echo($fila['cargooper']); ?></td>
				<td class="celda_listado"><?php echo($fila['nombreoper']); ?></td>
                <td class="celda_listado"><?php echo($fila['telefoper']); ?></td>
                <td class="celda_listado"><?php echo($fila['diroper']); ?></td>
                <td class="celda_listado"><?php echo($fila['localidadoper']); ?></td>
				<td class="celda_listado"><?php echo($fila['provinciaoper']); ?></td>
				<td><a href="./operarios_eliminar.php?DNIoper=<?php echo($fila['DNIoper']); ?>">
			        <img src="../images/eliminar.png" alt="eliminar"/></a></td></tr>
    <?php   	}
			} else {
    ?>
            <tr>
                <td class="celda_listado" colspan="5">
					<span class="mensaje">No existe ningún operario aún en la base de datos</span></td></tr>
    <?php
		    }
    ?>
            <tr>
                <td colspan="5">
					<form class="cultivos_datos" action="operarios_insertar.php" method="post">
						<br/><input type="submit" value="Añadir nuevo operario" name="btn_nuevo_operario"/>
					</form></td></tr>
        </table>
    <?php
			mysqli_free_result($rs); 
			mysqli_close($conn);
		}	// FIN try
        catch (Exception $e) {			
			$el_error = "<p class=\"mensaje\">";
			//if (!isset($conn)) $conn="";
			if (!isset($sql)) $sql="";
			$el_error .= getInformationError($e,$conn,$sql);
			$el_error .= "</p>";
			echo($el_error);
        }
    ?>
    </section>
</div>

</body>
</html>