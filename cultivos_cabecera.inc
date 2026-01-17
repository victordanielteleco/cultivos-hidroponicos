<?php
    // CABECERA DE LA PAGINA index.php

	//Directorio de la aplicación
	const DIR_APP = "/cultivos/";
?>
    <header id="cabecera">
	<nav class="menuppal">

            <div class="opcion_menu_primera"><a href="<?php echo(DIR_APP);?>index.php">Home</a></div>
            <div class="opcion_menu"><a href="<?php echo(DIR_APP);?>operarios/operarios_listado.php">Operarios</a></div>

            <div class="opcion_menu"><a href="<?php echo(DIR_APP);?>bandejas/bandejas_listado.php">Bandejas</a></div>
            <div class="opcion_menu"><a href="<?php echo(DIR_APP);?>especies/especies_listado.php">Especies</a></div>

	</nav>
    </header>
    <div id="linea_menu"></div>
