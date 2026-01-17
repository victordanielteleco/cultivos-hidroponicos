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
		    // Obtención listado de especies ordenadas por su descripción
		    $sql = "SELECT idespec, nombreespec, porteadulto, dias, humedad_ambiente, horas_luz_dia, intensidad_de_luz, N, P, K, PH, EC, litros_por_hora FROM " .
		        "especies ORDER BY idespec";
		    if (!($rs = mysqli_query($conn, $sql)))
		        throw new Exception('Error al realizar la consulta.');
		    // Mostramos el listado de especies
	?>
        <table class="cultivos_datos">
            <caption>Especies: listado</caption>
	<?php
		    if (mysqli_num_rows($rs) > 0) {	
    ?>
            <tr>
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th class = "encab_listado">Id especie</th>
                        <th class = "encab_listado">Nombre especie</th>
                        <th class = "encab_listado">Porte adulto</th>
                        <th class = "encab_listado">Días crecim.</th>
                        <th class = "encab_listado">Humedad ambiente</th>
                        <th class = "encab_listado">Horas de luz al día</th>
                        <th class = "encab_listado">Intens. de luz</th>
                        <th class = "encab_listado">N</th>
                        <th class = "encab_listado">P</th>
                        <th class = "encab_listado">K</th>
                        <th class = "encab_listado">PH (0 ácido - 14 básico)</th>
                        <th class = "encab_listado">Electro Cond.</th>
                        <th class = "encab_listado">litros por hora</th>
                        <th>&nbsp;</th>
                </thead>
            </tr>
    <?php	    
				while($fila = mysqli_fetch_array($rs)) {
    ?>
			<tr>
			    <td><a href="./especies_editar.php?idespec=<?php echo($fila['idespec']); ?>">
				    <img src="../images/editar.png" alt="editar"/></a></td>
				<td class="celda_listado"><?php echo($fila['idespec']); ?></td>
				<td class="celda_listado"><?php echo($fila['nombreespec']); ?></td>
				<td class="celda_listado"><?php echo($fila['porteadulto']); ?></td>
                <td class="celda_listado"><?php echo($fila['dias']); ?></td>
                <td class="celda_listado"><?php echo($fila['humedad_ambiente']); ?></td>
                <td class="celda_listado"><?php echo($fila['horas_luz_dia']); ?></td>
                <td class="celda_listado"><?php echo($fila['intensidad_de_luz']); ?></td>
                <td class="celda_listado"><?php echo($fila['N']); ?></td>
                <td class="celda_listado"><?php echo($fila['P']); ?></td>
                <td class="celda_listado"><?php echo($fila['K']); ?></td>
                <td class="celda_listado"><?php echo($fila['PH']); ?></td>
                <td class="celda_listado"><?php echo($fila['EC']); ?></td>
                <td class="celda_listado"><?php echo($fila['litros_por_hora']); ?></td>
				<td><a href="./especies_eliminar.php?idespec=<?php echo($fila['idespec']); ?>">
			        <img src="../images/eliminar.png" alt="eliminar"/></a></td></tr>
    <?php   	}
			} else {
    ?>
            <tr>
                <td class="celda_listado" colspan="5">
                    <span class="mensaje">No existe ninguna especie aún en la base de datos</span></td></tr>
    <?php
		    }
    ?>
            <tr>
                <td colspan="5">
					<form class="cultivos_datos" action="especies_insertar.php" method="post">
						<br/><input type="submit" value="Añadir nueva especie" name="btn_nueva_especie"/>
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