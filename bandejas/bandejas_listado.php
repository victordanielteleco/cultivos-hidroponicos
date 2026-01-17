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
		    // Obtención listado de bandejas ordenadas por su descripción
		    $sql = "SELECT idband, torre, alturaband, idespecie, estadoband, fechaPlantado, fechaCosechado FROM " .
		        "bandejas ORDER BY idband";
		    if (!($rs = mysqli_query($conn, $sql)))
		        throw new Exception('Error al realizar la consulta.');
		    // Mostramos el listado de bandejas
	?>
        <table class="cultivos_datos">
            <caption>Bandejas: listado</caption>
	<?php	
		    if (mysqli_num_rows($rs) > 0) {
    ?>
            <tr>
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th class = "encab_listado">id de la bandeja</th>
                        <th class = "encab_listado">torre</th>
                        <th class = "encab_listado">altura</th>
                        <th class = "encab_listado">id de la especie</th>
                        <th class = "encab_listado">estado de la bandeja</th>
                        <th class = "encab_listado">fecha de plantado</th>
                        <th class = "encab_listado">fecha de cosechado</th>
                        <th>&nbsp;</th>
                </thead>
            </tr>
    <?php
	    
				while($fila = mysqli_fetch_array($rs)) {
    ?>
			<tr>
			    <td><a href="./bandejas_editar.php?idband=<?php echo($fila['idband']); ?>">
				    <img src="../images/editar.png" alt="editar"/></a></td>
				<td class="celda_listado"><?php echo($fila['idband']); ?></td>
				<td class="celda_listado"><?php echo($fila['torre']); ?></td>
                <td class="celda_listado"><?php echo($fila['alturaband']); ?></td>
                <td class="celda_listado"><?php echo($fila['idespecie']); ?></td>
                <td class="celda_listado"><?php echo($fila['estadoband']); ?></td>
                <td class="celda_listado"><?php echo($fila['fechaPlantado']); ?></td>
                <td class="celda_listado"><?php echo($fila['fechaCosechado']); ?></td>
				<td><a href="./bandejas_eliminar.php?idband=<?php echo($fila['idband']); ?>">
			        <img src="../images/eliminar.png" alt="eliminar"/></a></td></tr>
    <?php   	}
			} else {
    ?>
            <tr>
                <td class="celda_listado" colspan="5">
					<span class="mensaje">No existe ninguna bandeja aún en la base de datos</span></td></tr>
    <?php
		    }
    ?>
            <tr>
                <td colspan="5">
					<form class="cultivos_datos" action="bandejas_insertar.php" method="post">
						<br/><input type="submit" value="Añadir nueva bandeja" name="btn_nueva_bandeja"/>
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