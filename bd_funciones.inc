<?php
    // -------------------------------------
    // Constantes base de datos de Compras
    // -------------------------------------
    const SERVIDOR = "localhost";
    const BD_NOMBRE = "cultivos";
    const BD_USUARIO = "root";
    const BD_PASS = "";     // Valores por defecto: "" o "root"
	const BD_CHARSET = "utf8";

	// constante que indica si la aplicación está siendo ejecutada en modo debug
    const IS_DEBUG = true;

	// constante que indica si se utilizan etiquetas HTML <input> tipo 
    // "date"/"datetime-local" (true) o tipo "text" (false)
	// para mostrar las datos tipo fecha
	const INPUT_TYPE_DATE =true;

    // -------------------------------------------------------------
    // Funciones relacionadas con operaciones sobre la base de datos
    // -------------------------------------------------------------
    
    /******************************************************************************
     * FUNCION: getInformationError($la_excepcion, $la_conexion, $la_sentenciaSQL) 
     * DESCRIPCION:
     *  - Obtiene la cadena a mostrar en caso de error
     * ARGUMENTOS:
     *  - $la_excepcion: variable de tipo Exception que ha sido "atrapada" en el código
     *  - $la_conexion: variable que guarda la conexión a la base de datos (en caso de existir)
     *  - $la_sentenciaSQL: variable con la cadena $sql ejecutada (en caso de existir)
     * VALOR DEVUELTO:
     *  - Cadena con la información a mostrar al usuarios en caso de error
    ******************************************************************************/
    function getInformationError($la_excepcion, $la_conexion, $la_sentenciaSQL) 
    {
		$el_error = $la_excepcion->getMessage();
		if (IS_DEBUG) {
			if(isset($la_conexion))
				if (strlen(mysqli_error($la_conexion)) > 0)
					$el_error .= "</br>" . "<strong>Error mySQL:</strong> " . mysqli_error($la_conexion);
			if (strlen($la_sentenciaSQL) > 0)
				$el_error .= "</br>" . "<strong>Sentencia SQL:</strong> " . $la_sentenciaSQL ;
		}				
		
		return $el_error;
    }

    /******************************************************************************
     * FUNCION: Conectarse($servidor, $basedatos, $usuario, $password) 
     * DESCRIPCION:
     *  - Permite la conexión a una base de datos 
     * ARGUMENTOS:
     *  - $servidor: nombre o dirección del servidor donde se haya la BD
     *  - $basedatos: nombre de la base de datos a conectarse
     *  - $usuario: id del usuario con el que conectarse a la BD
     *  - $password: contraseña para acceder a la BD
     * VALOR DEVUELTO:
     *  - Devuelve la conexión a la base de datos
     *  - En caso de error devuelve una excepción
    ******************************************************************************/
    function Conectarse($servidor, $basedatos, $usuario, $password) 
    {
        try {
            if (!($conn=mysqli_connect($servidor, $usuario, $password, $basedatos)))
                throw new Exception('Error conectando a la base de datos.' . mysqli_connect_error());
			if (!mysqli_set_charset($conn, BD_CHARSET))
                throw new Exception('Error al asignar la codificación de la base de datos.');
        }
        catch (Exception $e)
        {
            throw $e;
        }
        
        return $conn;
    }
    
	/***************************************************************
     * FUNCION: is_date ($str)
     * DESCRIPCION:
     *  - Comprueba si el valor pasado como argumento representa una
     *  fecha válida
     * ARGUMENTOS:
     *  - $str: string a evaluar como fecha
     * VALOR DEVUELTO:
     *  - TRUE: Si el valor representa una fecha válida
     *  - FALSE: Otro caso
    ***************************************************************/
    function is_date ($str ) 
    {
		if (INPUT_TYPE_DATE){
			// Convertimos a DateTime el valor pasado como argumento
			// con formato "Y-m-d"
			$dtvalue = DateTime::createFromFormat("Y-m-d", $str);
		} else {
			// Convertimos a DateTime el valor pasado como argumento
			// con formato "d/m/Y"
			$dtvalue = DateTime::createFromFormat("d/m/Y", $str);
		}
		if ($dtvalue === false)
		   return false; 
		else
			return true;
    }
    
	/***************************************************************
     * FUNCION: is_datetime ($str)
     * DESCRIPCION:
     *  - Comprueba si el valor pasado como argumento representa una
     *  fecha-hora válida
     * ARGUMENTOS:
     *  - $str: string a evaluar como fecha-hora
     * VALOR DEVUELTO:
     *  - TRUE: Si el valor representa una fecha-hora válida
     *  - FALSE: Otro caso
    ***************************************************************/
    function is_datetime ($str ) 
    {
		if (INPUT_TYPE_DATE){
			// Convertimos a DateTime el valor pasado como argumento
			// con formato "Y-m-d\TH:i"
			$dtvalue = DateTime::createFromFormat("Y-m-d\TH:i:s", $str);
		} else {
			// Convertimos a DateTime el valor pasado como argumento
			// con formato "d/m/Y H:i"
			$dtvalue = DateTime::createFromFormat("d/m/Y H:i:s", $str);
		}
		if ($dtvalue === false)
		   return false; 
		else
			return true;
    }

    /******************************************************************************
     * FUNCION: ComprobarDato($dato, $desc_dato, $obligatorio, $tipodato, &$msg_error) 
     * DESCRIPCION:
     *  - Comprueba si el valor del campo pasado es correcto
     * ARGUMENTOS:
     *  - $dato: valor del campo que hay que comprobar
     *  - $desc_dato: descripción del campo
     *  - $obligatorio: indica si el campo es un dato obligatorio
     *  - $tipodato: tipo de dato que se almacena en el campo ("cadena", "numerico",
     *    "fecha" o "fechahora")
     *  - &$msg_error: devuelve descripción del error producido
     * VALOR DEVUELTO:
     *  - true si la comprobación ha sido satisfactoria
     *  - false en cualquier otro caso
    ******************************************************************************/
    function ComprobarDato($dato, $desc_dato, $obligatorio, $tipodato, &$msg_error) 
    {
        $b_error=false;
        $desc_error="";
        
        if (($obligatorio) && ($dato=="")) {
            $desc_error .=  "dato obligatorio.";
            $b_error = true;
        } elseif ($dato!="") {
            switch ($tipodato) {
                case "cadena":
                    // no se debe comprobar nada
                    break;
                case "numerico":
                    if (!is_numeric($dato)) {
                        $desc_error .= "error formato numérico.";
                        $b_error =true;
                    }
                    break;
                case "fecha":       // comprueba si es de tipo fecha con formato dia/mes/año
					if (!is_date($dato)) {
                        $desc_error .= "error formato fecha.";
                        $b_error = true;
                    }
                    break;
                case "fechahora":   // comprueba si es de tipo fecha con formato dia/mes/año hh:mm
					if (!is_datetime($dato)) {
                        $desc_error .= "error formato fecha-hora.";
                        $b_error = true;
                    }
                    break;
                default:
                    $desc_error .= "tipo de datos especificado ($tipodato) no válido.";
                    $b_error =true;
            }
        }
        if ($desc_error != "") {
            $msg_error .= "<strong>$desc_dato:</strong> $desc_error";
            if (IS_DEBUG)
                $msg_error .= " ($dato)";
            $msg_error .= "<br/>";
        }
        
        return !($b_error);
    }
    
    /******************************************************************************
     * FUNCION: formatear_fecha($fecha, $a_mysql)
     * DESCRIPCION:
     *  - Permite cambio de formato de una fecha a formato utilizado ...
     *      * ... por mySQL "yyyy-mm-dd" ($a_mysql=true)
     *      * ... para mostrárselo al usuario "dd/mm/yyyy" ($a_mysql=false)
     * ARGUMENTOS:
     *  - $fecha: fecha a la que se quiere dar formato
     *  - $a_mysql: indica si queremos pasar la fecha a formato mySQL (true)
     *      o si queremos formatearla para mostrarsela al usuario (false)
     * VALOR DEVUELTO:
     *  - La fecha con el nuevo formato
    ******************************************************************************/
    function formatear_fecha($fecha, $a_mysql) {
        
        //mirar la funcion explode()
        $lafecha = "";
        
        if ($a_mysql) {
            if ($fecha = DateTime::createFromFormat ('d/m/Y', $fecha))
                $lafecha = date_format($fecha, 'Y-m-d');
        } else { 
            if ($fecha = DateTime::createFromFormat ('Y-m-d', $fecha))
				if(INPUT_TYPE_DATE)
					$lafecha = date_format($fecha, 'Y-m-d');
				else
                	$lafecha = date_format($fecha, 'd/m/Y');
        }
        
        if (($lafecha=="") && ($fecha!=""))
            $lafecha ="Formato incorrecto";
        
        return $lafecha; 
    }
    
    /******************************************************************************
     * FUNCION: formatear_fechahora($fechahora, $a_mysql)
     * DESCRIPCION:
     *  - Permite cambio de formato de una fecha a formato utilizado ...
     *      * ... por mySQL "yyyy-mm-dd HH:ii" ($a_mysql=true)
     *      * ... para mostrárselo al usuario "dd/mm/yyyy HH:ii" ($a_mysql=false)
     * ARGUMENTOS:
     *  - $fechahora: fecha-hora a la que se quiere dar formato
     *  - $a_mysql: indica si queremos pasar la fecha-hora a formato mySQL (true)
     *      o si queremos formatearla para mostrarsela al usuario (false)
     * VALOR DEVUELTO:
     *  - La fecha-hora con el nuevo formato
    ******************************************************************************/
    function formatear_fechahora($fechahora, $a_mysql) {
        
        //mirar la funcion explode()
        $lafechahora = "";
        
        if ($a_mysql) {
            if ($fechahora = DateTime::createFromFormat ('d/m/Y\TH:i:s', $fechahora))
                $lafechahora = date_format($fecha, 'Y-m-d H:i:s');
        } else { 
            if ($fechahora = DateTime::createFromFormat ('Y-m-d H:i:s', $fechahora))
				if(INPUT_TYPE_DATE)
					$lafechahora = date_format($fechahora, 'Y-m-d\TH:i:s');
				else
                	$lafechahora = date_format($fechahora, 'd/m/Y H:i:s');
        }
        
        if (($lafechahora=="") && ($fechahora!=""))
            $lafechahora ="Formato incorrecto";
        
        return $lafechahora; 
    }
    
    /******************************************************************************
     * FUNCION: FormatToSQL($dato, $tipodato)
     * DESCRIPCION:
     *  - Permite formatear el dato según de que tipo sea para incluirlo en una
     *  sentencia SQL
     * ARGUMENTOS:
     *  - $dato: dato a formatear
     *  - $tipodato: tipo de dato al que pertenece el dato entre los siguientes
     *  valores: "cadena", "fecha", "numerico"
     * VALOR DEVUELTO:
     *  - el dato formateado para ser incluído en una sentencia SQL
    ******************************************************************************/

    function FormatToSQL($dato, $tipodato) {
        //$eldatoformateado ="";
        if ($dato=="")
            $eldatoformateado = "NULL";
        else
			switch ($tipodato) {
				case "cadena":
					$eldatoformateado = "\"" . $dato . "\"";
					break;
				case "fecha":
					if (INPUT_TYPE_DATE){
						$eldatoformateado = "\"" . trim($dato) . "\"";
					}
					else {
						$eldatoformateado = "\"" . formatear_fecha($dato, true) . "\"";
					}
					break;
				case "fechahora":
					if (INPUT_TYPE_DATE){
						$eldatoformateado = "\"" . trim($dato) . "\"";
					}
					else {
						$eldatoformateado = "\"" . formatear_fechahora($dato, true) . "\"";
					}
					break;
				case "numerico":
					$eldatoformateado = ((is_numeric($dato))?$dato:"NULL");
					break;
				default:
					$eldatoformateado = ($dato);
			}
        
        return $eldatoformateado;
    }
?>