--                                                 Alumnos: Victor Daniel Dueñas Martínez
--                                                          José Eduardo Ramos Baluja

-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-12-2022 a las 20:12:44
-- Versión del servidor: 5.7.17
-- Versión de PHP: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cultivos`
--
CREATE DATABASE `cultivosINTENTODIVIDIR` DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizar_estadoband_Bandejas` (IN `nuevo_estadoband` VARCHAR(40) CHARSET utf8mb4, IN `n_idband` CHAR(6) CHARSET utf8mb4, IN `n_idespecie` CHAR(5) CHARSET utf8mb4)  NO SQL
BEGIN
	SET @nuevoestado=nuevo_estadoband;
    
	UPDATE Bandejas 
	SET
		Bandejas.estadoband=nuevo_estadoband,	
        
        Bandejas.fechaPlantado=CASE WHEN @nuevoestado = 'Plantando' THEN CURRENT_TIME ELSE Bandejas.fechaPlantado END,
  		Bandejas.fechaCosechado=CASE WHEN @nuevoestado = 'Cosechando' THEN CURRENT_TIME ELSE Bandejas.fechaCosechado END

	WHERE ( Bandejas.idband=n_idband AND Bandejas.idespecie=n_idespecie);   
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizar_estado_tarea_Operaciones` (IN `nuevo_estado` VARCHAR(20) CHARSET utf8mb4, IN `n_idoperacion` INT(10) UNSIGNED, IN `n_DNIoper` CHAR(9) CHARSET utf8mb4)  NO SQL
BEGIN
	SET @nuevoestado=nuevo_estado;
    
	UPDATE Operaciones 
	SET
		Operaciones.estado_tarea=nuevo_estado,	
        
        Operaciones.Inicio=CASE WHEN @nuevoestado = 'Haciendo' THEN CURRENT_TIME ELSE Operaciones.Inicio END,
  		Operaciones.Final=CASE WHEN @nuevoestado = 'Hecha' THEN CURRENT_TIME ELSE Operaciones.Final END

	WHERE ( Operaciones.idoperacion=n_idoperacion AND Operaciones.DNIoper=n_DNIoper);   
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizar_porte_actual_Plantas` (IN `nuevo_porte` INT(10) UNSIGNED, IN `n_idplanta` INT(1) UNSIGNED, IN `n_idband` CHAR(6) CHARSET utf8mb4)  NO SQL
UPDATE Plantas SET

Plantas.porte_actual=nuevo_porte WHERE ( Plantas.idplanta=n_idplanta AND Plantas.idband=n_idband)$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bandejas`
--

CREATE TABLE `bandejas` (
  `idband` char(6) COLLATE utf8_unicode_ci NOT NULL COMMENT 'XXX-XX (torre-altura)',
  `torre` int(3) UNSIGNED NOT NULL,
  `alturaband` int(2) UNSIGNED NOT NULL,
  `idespecie` char(5) COLLATE utf8_unicode_ci NOT NULL COMMENT 'RUC_1, RUC_2, CAN_1, RAB_1, RAB_2, RAB_3, ESP_1, ESP_2',
  `estadoband` varchar(40) COLLATE utf8_unicode_ci NOT NULL COMMENT 'requiriendo_Plantar (bandeja vacía), Pantando, Normal, requiriendo_QuitarHierbas, QuitandoHierbas, requiriendo_MedirPorte, MidiendoPorte, requiriendo_Cosechar, Cosechando, requiriendo_Vaciar, Vaciando y limpiando',
  `fechaPlantado` date DEFAULT NULL COMMENT 'rango válido: "1000-01-01" a "9999-12-31"',
  `fechaCosechado` date DEFAULT NULL COMMENT 'rango válido: "1000-01-01" a "9999-12-31"'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



--
-- Disparadores `bandejas`
--
DELIMITER $$
CREATE TRIGGER `bandejas_au` AFTER UPDATE ON `bandejas` FOR EACH ROW BEGIN

SET @idband_nuevo=NEW.idband, 
	@torre_nuevo=NEW.torre, 
    @alturaband_nuevo=NEW.alturaband, 
    @idespecie_nuevo=NEW.idespecie, 
    @estadoband_nuevo=NEW.estadoband,
    @fechaPlantado_nuevo=NEW.fechaPlantado, 
    @fechaCosechado_nuevo=NEW.fechaCosechado;

INSERT INTO control_bandejas 	(idband_viejo,
								idband_nuevo,
								torre_viejo,
								torre_nuevo,
								alturaband_viejo,
								alturaband_nuevo,
								idespecie_viejo,
								idespecie_nuevo,
								estadoband_viejo,
								estadoband_nuevo,
								fechaPlantado_viejo,
								fechaPlantado_nuevo,
								fechaCosechado_viejo,
								fechaCosechado_nuevo,
								usuario,
								modificado)

VALUES (@idband_viejo,
		@idband_nuevo,
		
		@torre_viejo,
		@torre_nuevo,
		
		@alturaband_viejo,
		@alturaband_nuevo,
		
		@idespecie_viejo,
		@idespecie_nuevo,
		
		@estadoband_viejo,
		@estadoband_nuevo,
		
		@fechaPlantado_viejo,
		@fechaPlantado_nuevo,
		
		@fechaCosechado_viejo,
		@fechaCosechado_nuevo,
		
		CURRENT_USER,
		CURRENT_TIME);

	

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `bandejas_bu` BEFORE UPDATE ON `bandejas` FOR EACH ROW SET @idband_viejo=OLD.idband, 
	@torre_viejo=OLD.torre, 
    @alturaband_viejo=OLD.alturaband, 
    @idespecie_viejo=OLD.idespecie, 
    @estadoband_viejo=OLD.estadoband,
    @fechaPlantado_viejo=OLD.fechaPlantado, 
    @fechaCosechado_viejo=OLD.fechaCosechado
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `control_bandejas`
--

CREATE TABLE `control_bandejas` (
  `idband_viejo` char(6) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'XXX-XX (torre-altura)',
  `idband_nuevo` char(6) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'XXX-XX (torre-altura)',
  `torre_viejo` int(3) UNSIGNED DEFAULT NULL,
  `torre_nuevo` int(3) UNSIGNED DEFAULT NULL,
  `alturaband_viejo` int(2) UNSIGNED DEFAULT NULL,
  `alturaband_nuevo` int(2) UNSIGNED DEFAULT NULL,
  `idespecie_viejo` char(5) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'RUC_1, RUC_2, CAN_1, RAB_1, RAB_2, RAB_3, ESP_1, ESP_2',
  `idespecie_nuevo` char(5) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'RUC_1, RUC_2, CAN_1, RAB_1, RAB_2, RAB_3, ESP_1, ESP_2',
  `estadoband_viejo` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'requiriendo_Plantar (bandeja vacía), Pantando, Normal, requiriendo_QuitarHierbas, QuitandoHierbas, requiriendo_MedirPorte, MidiendoPorte, requiriendo_Cosechar, Cosechando, requiriendo_Vaciar, Vaciando y limpiando',
  `estadoband_nuevo` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'requiriendo_Plantar (bandeja vacía), Pantando, Normal, requiriendo_QuitarHierbas, QuitandoHierbas, requiriendo_MedirPorte, MidiendoPorte, requiriendo_Cosechar, Cosechando, requiriendo_Vaciar, Vaciando y limpiando',
  `fechaPlantado_viejo` date DEFAULT NULL COMMENT 'rango válido: "1000-01-01" a "9999-12-31"',
  `fechaPlantado_nuevo` date DEFAULT NULL COMMENT 'rango válido: "1000-01-01" a "9999-12-31"',
  `fechaCosechado_viejo` date DEFAULT NULL COMMENT 'rango válido: "1000-01-01" a "9999-12-31"',
  `fechaCosechado_nuevo` date DEFAULT NULL COMMENT 'rango válido: "1000-01-01" a "9999-12-31"',
  `usuario` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `modificado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `especies`
--

CREATE TABLE `especies` (
  `idespec` char(5) COLLATE utf8_unicode_ci NOT NULL COMMENT 'RUC_1, RUC_2, CAN_1, RAB_1, RAB_2, RAB_3, ESP_1, ESP_2',
  `nombreespec` varchar(40) COLLATE utf8_unicode_ci NOT NULL COMMENT 'RÚCULA_(Eruca sativa), RÚCULA_(Diplotaxis tenuifolia), CANÓNIGO_(Valerianella locusta), RÁBANO_(Daikon o blanco), RÁBANO_(Negro), RÁBANO_(Pequeño), ESPINACA_(Rizada), ESPINACA_(Lisa)',
  `porteadulto` int(5) UNSIGNED NOT NULL COMMENT 'tamaño final que debe medir (en milímetros)',
  `dias` int(3) UNSIGNED NOT NULL COMMENT 'número de días desde fechaPlantado hata fechaCosechado',
  `humedad_ambiente` decimal(5,2) UNSIGNED NOT NULL COMMENT '% de humedad en el aire',
  `horas_luz_dia` decimal(4,2) UNSIGNED NOT NULL,
  `intensidad_de_luz` int(3) UNSIGNED NOT NULL COMMENT 'porcentaje de la lámpara',
  `N` decimal(5,2) UNSIGNED NOT NULL COMMENT 'ppm nitrógeno en agua',
  `P` decimal(5,2) UNSIGNED NOT NULL COMMENT '	ppm fósforo en agua',
  `K` decimal(5,2) UNSIGNED NOT NULL COMMENT 'ppm potasio en agua',
  `PH` int(2) UNSIGNED NOT NULL COMMENT '0 (más ácido) - 14 (menos ácido)',
  `EC` decimal(5,2) UNSIGNED NOT NULL COMMENT 'Electroconductividad del agua en Siemens por centímetro (S/cm)',
  `litros_por_hora` decimal(5,2) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `operaciones`
--

CREATE TABLE `operaciones` (
  `idoperacion` int(10) UNSIGNED NOT NULL,
  `DNIoper` char(9) COLLATE utf8_unicode_ci NOT NULL,
  `idband` char(6) COLLATE utf8_unicode_ci NOT NULL COMMENT 'XXX-XX (torre-altura)',
  `tarea` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT 'A) plantar, B) quitar_hierbas, C) medir_porte, D) cosechar, E) vaciar y limpiar, F) calibrar_sensores',
  `fechaOperacion` datetime NOT NULL COMMENT 'rango válido: "1000-01-01 00:00:00" a "9999-12-31 23:59:59"',
  `estado_tarea` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Por_hacer' COMMENT 'Por_hacer, Haciendo, Hecha',
  `Inicio` datetime DEFAULT NULL COMMENT 'rango válido: "1000-01-01 00:00:00" a "9999-12-31 23:59:59"',
  `tiempo_tarea` int(10) UNSIGNED DEFAULT NULL COMMENT 'tiempo realización de la tarea en minutos',
  `Final` datetime DEFAULT NULL COMMENT 'rango válido: "1000-01-01 00:00:00" a "9999-12-31 23:59:59"',
  `costo_materiales` decimal(5,2) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;



--
-- Disparadores `operaciones`
--
DELIMITER $$
CREATE TRIGGER `operaciones_ai` AFTER INSERT ON `operaciones` FOR EACH ROW BEGIN
    IF NEW.costo_materiales IS NULL THEN
    
        INSERT INTO Tabla_mensajes ( DNIoper, campo_mensaje )
        
        VALUES( NEW.DNIoper, CONCAT('El operario tiene costo_materiales a NULL') );
        
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `operarios`
--

CREATE TABLE `operarios` (
  `DNIoper` char(9) COLLATE utf8_unicode_ci NOT NULL,
  `cargooper` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Cargo o función del operario',
  `nombreoper` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `telefoper` varchar(15) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Teléfono: 6xx xxx xxx',
  `diroper` varchar(60) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Dirección: calle, número, planta',
  `localidadoper` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `provinciaoper` varchar(10) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



--
-- Disparadores `operarios`
--
DELIMITER $$
CREATE TRIGGER `operarios_ad` AFTER DELETE ON `operarios` FOR EACH ROW INSERT INTO operarios_baja (DNIoper_baja,cargooper_baja,nombreoper_baja,telefoper_baja,diroper_baja,localidadoper_baja,provinciaoper_baja)
VALUES(@DNIoper_baja,@cargooper_baja,@nombreoper_baja,@telefoper_baja,@diroper_baja,@localidadoper_baja,@provinciaoper_baja)
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `operarios_bd` BEFORE DELETE ON `operarios` FOR EACH ROW SET @DNIoper_baja=OLD.DNIoper,
    @cargooper_baja=OLD.cargooper,
    @nombreoper_baja=OLD.nombreoper,
    @telefoper_baja=OLD.telefoper,
    @diroper_baja=OLD.diroper,
    @localidadoper_baja=OLD.localidadoper,
    @provinciaoper_baja=OLD.provinciaoper
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `operarios_baja`
--

CREATE TABLE `operarios_baja` (
  `DNIoper_baja` char(9) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cargooper_baja` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Cargo o función del operario',
  `nombreoper_baja` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telefoper_baja` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Teléfono: 6xx xxx xxx',
  `diroper_baja` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Dirección: calle, número, planta',
  `localidadoper_baja` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `provinciaoper_baja` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Disparadores `operarios_baja`
--
DELIMITER $$
CREATE TRIGGER `operarios_baja_ad` AFTER DELETE ON `operarios_baja` FOR EACH ROW INSERT INTO operarios (DNIoper,cargooper,nombreoper,telefoper,diroper,localidadoper,provinciaoper)
VALUES(@DNIoper,@cargooper,@nombreoper,@telefoper,@diroper,@localidadoper,@provinciaoper)
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `operarios_baja_bd` BEFORE DELETE ON `operarios_baja` FOR EACH ROW SET @DNIoper=OLD.DNIoper_baja,
    @cargooper=OLD.cargooper_baja,
    @nombreoper=OLD.nombreoper_baja,
    @telefoper=OLD.telefoper_baja,
    @diroper=OLD.diroper_baja,
    @localidadoper=OLD.localidadoper_baja,
    @provinciaoper=OLD.provinciaoper_baja
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plantas`
--

CREATE TABLE `plantas` (
  `idplanta` int(1) UNSIGNED NOT NULL COMMENT '1 - 6',
  `idband` char(6) COLLATE utf8_unicode_ci NOT NULL COMMENT 'XXX-XX (torre-altura)',
  `porte_actual` int(10) UNSIGNED NOT NULL COMMENT 'tamaño de la planta en milímetros'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;



--
-- Disparadores `plantas`
--
DELIMITER $$
CREATE TRIGGER `plantas_bi` BEFORE INSERT ON `plantas` FOR EACH ROW BEGIN

	DECLARE errorMessage VARCHAR(255);
    SET errorMessage = CONCAT('La planta sólo puede tener un id numérico del 1 al 6, incluidos');

    IF NEW.idplanta<1 OR NEW.idplanta>6  THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = errorMessage;
    END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tabla_mensajes`
--

CREATE TABLE `tabla_mensajes` (
  `DNIoper` char(9) COLLATE utf8_unicode_ci DEFAULT NULL,
  `campo_mensaje` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------



--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bandejas`
--
ALTER TABLE `bandejas`
  ADD PRIMARY KEY (`idband`),
  ADD KEY `idespecie` (`idespecie`) USING BTREE;

--
-- Indices de la tabla `especies`
--
ALTER TABLE `especies`
  ADD PRIMARY KEY (`idespec`);

--
-- Indices de la tabla `operaciones`
--
ALTER TABLE `operaciones`
  ADD PRIMARY KEY (`idoperacion`),
  ADD KEY `idbandeja` (`idband`) USING BTREE,
  ADD KEY `DNIoper` (`DNIoper`) USING BTREE;

--
-- Indices de la tabla `operarios`
--
ALTER TABLE `operarios`
  ADD PRIMARY KEY (`DNIoper`);

--
-- Indices de la tabla `plantas`
--
ALTER TABLE `plantas`
  ADD PRIMARY KEY (`idplanta`,`idband`),
  ADD KEY `idband` (`idband`);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bandejas`
--
ALTER TABLE `bandejas`
  ADD CONSTRAINT `bandejas_ibfk_1` FOREIGN KEY (`idespecie`) REFERENCES `especies` (`idespec`);

--
-- Filtros para la tabla `operaciones`
--
ALTER TABLE `operaciones`
  ADD CONSTRAINT `operaciones_ibfk_4` FOREIGN KEY (`idband`) REFERENCES `bandejas` (`idband`),
  ADD CONSTRAINT `operaciones_ibfk_5` FOREIGN KEY (`DNIoper`) REFERENCES `operarios` (`DNIoper`);

--
-- Filtros para la tabla `plantas`
--
ALTER TABLE `plantas`
  ADD CONSTRAINT `plantas_ibfk_1` FOREIGN KEY (`idband`) REFERENCES `bandejas` (`idband`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
