-- =============================================================================
-- SETUP COMPLETO: Base de datos whatsapp_agent + apos
-- Sistema FarmanorBot + SISCON
-- Generado: 2026-04-24
-- =============================================================================
-- Ejecutar como root:
--   mysql -u root -p < whatsapp_agent_setup.sql
-- =============================================================================

SET NAMES utf8mb4;
SET time_zone = '-03:00';

-- ---------------------------------------------------------------------------
-- 1. USUARIOS DE BASE DE DATOS
-- ---------------------------------------------------------------------------

-- Usuario para el agente WhatsApp (acceso a whatsapp_agent + siscon)
-- IMPORTANTE: cambiar la contraseña antes de correr en producción.
-- Debe cumplir la política: mínimo 8 caracteres, mayúscula, número y símbolo.
CREATE USER IF NOT EXISTS 'openclaw'@'localhost' IDENTIFIED BY 'Openclaw2026!';
CREATE USER IF NOT EXISTS 'openclaw'@'%'          IDENTIFIED BY 'Openclaw2026!';

-- ---------------------------------------------------------------------------
-- 2. BASE DE DATOS: whatsapp_agent
-- ---------------------------------------------------------------------------

CREATE DATABASE IF NOT EXISTS `whatsapp_agent`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `whatsapp_agent`;

-- Clientes registrados por el bot
CREATE TABLE IF NOT EXISTS `clientes` (
    `id`              INT           NOT NULL AUTO_INCREMENT,
    `usuario_id`      VARCHAR(20)   NOT NULL,
    `nombre`          VARCHAR(120)  NOT NULL,
    `dni`             VARCHAR(20)   NOT NULL,
    `telefono`        VARCHAR(20)   DEFAULT NULL,
    `sucursal_id`     INT           DEFAULT 2,
    `afiliado_activo` TINYINT(1)    DEFAULT 0,
    `plan`            VARCHAR(100)  DEFAULT NULL,
    `fecha_registro`  DATETIME      DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_usuario_id` (`usuario_id`),
    UNIQUE KEY `uq_dni`        (`dni`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pedidos (formato legacy: 1 fila por ítem)
CREATE TABLE IF NOT EXISTS `pedidos` (
    `id`                  INT            NOT NULL AUTO_INCREMENT,
    `pedido_id`           VARCHAR(50)    NOT NULL,
    `cliente_id`          INT            NOT NULL,
    `medicamento_codigo`  VARCHAR(25)    NOT NULL,
    `medicamento_nombre`  VARCHAR(120)   NOT NULL,
    `cantidad`            INT            NOT NULL,
    `precio_unitario`     DECIMAL(12,2)  NOT NULL,
    `total`               DECIMAL(12,2)  NOT NULL,
    `estado`              VARCHAR(20)    DEFAULT 'CONFIRMADO',
    `fecha_pedido`        DATETIME       DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_pedido_id` (`pedido_id`),
    KEY `idx_cliente_id`   (`cliente_id`),
    CONSTRAINT `fk_pedidos_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Direcciones de entrega asociadas a pedidos
CREATE TABLE IF NOT EXISTS `direcciones` (
    `id`               INT          NOT NULL AUTO_INCREMENT,
    `pedido_id`        VARCHAR(50)  NOT NULL,
    `direccion`        TEXT,
    `ubicacion_link`   VARCHAR(500) DEFAULT NULL,
    `tipo_envio`       VARCHAR(20)  DEFAULT 'A_DOMICILIO',
    `fecha_registro`   DATETIME     DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_pedido_id` (`pedido_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sugerencias de medicamentos alternativos
CREATE TABLE IF NOT EXISTS `laboratorios_sugeridos` (
    `id`                        INT          NOT NULL AUTO_INCREMENT,
    `pedido_id`                 VARCHAR(50)  NOT NULL,
    `medicamento_solicitado`    VARCHAR(120) NOT NULL,
    `medicamento_sugerido_codigo` VARCHAR(25) NOT NULL,
    `medicamento_sugerido_nombre` VARCHAR(120) NOT NULL,
    `razon`                     VARCHAR(200) DEFAULT NULL,
    `aceptado`                  TINYINT(1)   DEFAULT 0,
    `fecha_sugerencia`          DATETIME     DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_pedido_id` (`pedido_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Log de auditoría de todas las interacciones
CREATE TABLE IF NOT EXISTS `auditoria` (
    `id`         INT         NOT NULL AUTO_INCREMENT,
    `usuario_id` VARCHAR(20) DEFAULT NULL,
    `evento`     VARCHAR(50) DEFAULT NULL,
    `detalles`   JSON        DEFAULT NULL,
    `fecha`      DATETIME    DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_usuario_id` (`usuario_id`),
    KEY `idx_fecha`      (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Permisos al usuario openclaw
GRANT SELECT, INSERT, UPDATE, DELETE ON `whatsapp_agent`.* TO 'openclaw'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON `whatsapp_agent`.* TO 'openclaw'@'%';


-- ---------------------------------------------------------------------------
-- 3. BASE DE DATOS: apos (validación de afiliados)
-- ---------------------------------------------------------------------------

CREATE DATABASE IF NOT EXISTS `apos`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `apos`;

CREATE TABLE IF NOT EXISTS `afiliados` (
    `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `obra_social_id`   INT             DEFAULT NULL,
    `nroAfiliado`      VARCHAR(255)    DEFAULT NULL,
    `documento`        VARCHAR(255)    DEFAULT NULL,
    `apeynombres`      VARCHAR(255)    DEFAULT NULL,
    `localidad`        VARCHAR(255)    DEFAULT NULL,
    `telefonos`        VARCHAR(255)    DEFAULT NULL,
    `email`            VARCHAR(255)    DEFAULT NULL,
    `sexo`             VARCHAR(255)    DEFAULT NULL,
    `obra_social`      VARCHAR(100)    DEFAULT NULL,
    `fecha_nacimiento` DATE            DEFAULT NULL,
    `zona_residencia`  VARCHAR(100)    DEFAULT NULL,
    `domicilio`        VARCHAR(255)    DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_documento` (`documento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

GRANT SELECT ON `apos`.* TO 'openclaw'@'localhost';
GRANT SELECT ON `apos`.* TO 'openclaw'@'%';


-- ---------------------------------------------------------------------------
-- 4. PERMISOS EN siscon (para leer articulosZafiro y escribir wsp_pedidos)
-- ---------------------------------------------------------------------------

GRANT SELECT           ON `siscon`.`articulosZafiro` TO 'openclaw'@'localhost';
GRANT SELECT           ON `siscon`.`articulosZafiro` TO 'openclaw'@'%';
GRANT SELECT, INSERT, UPDATE ON `siscon`.`wsp_pedidos`    TO 'openclaw'@'localhost';
GRANT SELECT, INSERT, UPDATE ON `siscon`.`wsp_pedidos`    TO 'openclaw'@'%';

FLUSH PRIVILEGES;


-- ---------------------------------------------------------------------------
-- 5. DATOS DE PRUEBA — clientes
-- ---------------------------------------------------------------------------

USE `whatsapp_agent`;

INSERT IGNORE INTO `clientes`
    (`id`, `usuario_id`, `nombre`, `dni`, `telefono`, `sucursal_id`, `afiliado_activo`, `plan`, `fecha_registro`)
VALUES
    (1,  '5968775437', 'Axel Facundo Guardia',       '39300300',  '5968775437', 2, 1, 'APOS',          '2026-03-31 16:12:00'),
    (2,  '1604143115', 'Dardo Reyna',                 '27052075',  '1604143115', 2, 0, 'SIN COBERTURA', '2026-04-13 17:30:00'),
    (3,  '5491122334', 'María Fernanda López',        '32456789',  '5491122334', 2, 1, 'APOS',          '2026-04-14 10:00:00'),
    (4,  '5493312244', 'Carlos Eduardo Espeche',      '25874136',  '5493312244', 2, 1, 'APOS',          '2026-04-15 09:30:00'),
    (5,  '5493398765', 'Luciana Romero',              '41230987',  '5493398765', 2, 0, 'SIN COBERTURA', '2026-04-16 14:00:00');


-- ---------------------------------------------------------------------------
-- 6. DATOS DE PRUEBA — pedidos
-- ---------------------------------------------------------------------------

INSERT IGNORE INTO `pedidos`
    (`pedido_id`, `cliente_id`, `medicamento_codigo`, `medicamento_nombre`, `cantidad`, `precio_unitario`, `total`, `estado`, `fecha_pedido`)
VALUES
    ('PED-20260331161224-5437', 1, '0000042612', 'IBUPROFENO TAURO 400',        1,  3579.76,  3579.76, 'ENTREGADO',  '2026-03-31 16:12:24'),
    ('PED-20260408164334-5437', 1, '0000000005', 'ACTOS',                        1,   273.01,   273.01, 'ENTREGADO',  '2026-04-08 16:44:21'),
    ('PED-20260408164654-5437', 1, '0000000005', 'ACTOS',                        2,   191.11,   382.21, 'ENTREGADO',  '2026-04-08 16:47:38'),
    ('PED-20260408164654-5437', 1, '0000064809', 'FABOGESIC NIÑOS 4%',           1,  8050.00,  8050.00, 'ENTREGADO',  '2026-04-08 16:47:38'),
    ('PED-20260408164654-5437', 1, '0000053590', 'FABOGESIC 400 RAPIDA ACCION',  1,  2127.67,  2127.67, 'ENTREGADO',  '2026-04-08 16:47:38'),
    ('PED-20260413173025-3115', 2, '0000021851', 'LOTRIAL',                      2,  5705.00, 11410.00, 'PROCESANDO', '2026-04-13 17:30:25'),
    ('PED-20260413173025-3115', 2, '0000016807', 'T4 MONTPELLIER 112',           1, 32565.45, 32565.45, 'PROCESANDO', '2026-04-13 17:30:25'),
    ('PED-20260414144034-3115', 2, '0000016814', 'T4 MONTPELLIER 50',            1,  9672.68,  9672.68, 'PROCESANDO', '2026-04-14 14:43:23'),
    ('PED-20260414144034-3115', 2, '0000041712', 'NOVALGINA',                    1, 14936.84, 14936.84, 'PROCESANDO', '2026-04-14 14:43:23'),
    ('PED-20260417190038-3115', 2, '0000055417', 'GRIPABEN PLUS',                1,  9167.00,  9167.00, 'CONFIRMADO', '2026-04-17 19:00:44'),
    ('PED-20260417190038-3115', 2, '0000080849', 'IBULGIA',                      1,  6578.00,  6578.00, 'CONFIRMADO', '2026-04-17 19:00:44'),
    -- Pedidos de prueba nuevos
    ('PED-20260420120000-3344', 3, '0000055837', 'MUCOBRON FORTE',               2, 10999.90, 21999.80, 'CONFIRMADO', '2026-04-20 12:00:00'),
    ('PED-20260421090000-5566', 4, '0000007387', 'EUTHYROX',                     1, 54509.71, 54509.71, 'PROCESANDO', '2026-04-21 09:00:00'),
    ('PED-20260422150000-7788', 5, '0000064225', 'GRIPABEN T DESCONGESTIVO VL',  3, 13184.00, 39552.00, 'CONFIRMADO', '2026-04-22 15:00:00'),
    ('PED-20260423180000-5437', 1, '0000056808', 'INVOKANA',                     1,136576.39,136576.39, 'CONFIRMADO', '2026-04-23 18:00:00');


-- ---------------------------------------------------------------------------
-- 7. DATOS DE PRUEBA — direcciones
-- ---------------------------------------------------------------------------

INSERT IGNORE INTO `direcciones` (`pedido_id`, `direccion`, `tipo_envio`) VALUES
    ('PED-20260408164334-5437', 'Cabo Pérez 2001, Barrio Alta Rioja, La Rioja', 'A_DOMICILIO'),
    ('PED-20260413173025-3115', 'Retiro en sucursal',                           'RETIRO_LOCAL'),
    ('PED-20260414144034-3115', 'Av. Benavidez 327, La Rioja',                  'A_DOMICILIO'),
    ('PED-20260417190038-3115', 'Retiro en sucursal',                           'RETIRO_LOCAL'),
    ('PED-20260420120000-3344', 'San Martín 1450, La Rioja',                    'A_DOMICILIO'),
    ('PED-20260421090000-5566', 'Retiro en sucursal',                           'RETIRO_LOCAL'),
    ('PED-20260422150000-7788', 'Rivadavia 890, La Rioja',                      'A_DOMICILIO'),
    ('PED-20260423180000-5437', 'Cabo Pérez 2001, Barrio Alta Rioja, La Rioja', 'A_DOMICILIO');


-- ---------------------------------------------------------------------------
-- 8. DATOS DE PRUEBA — apos.afiliados
-- ---------------------------------------------------------------------------

USE `apos`;

INSERT IGNORE INTO `afiliados`
    (`id`, `obra_social_id`, `nroAfiliado`, `documento`, `apeynombres`, `localidad`, `obra_social`, `sexo`)
VALUES
    (1,  3, '13297287003', '58413671', 'GONZALEZ, ANA PAULA',            'La Rioja',  'APOS', 'F'),
    (2,  3, '13970051900', '39700519', 'TORRES MORALES, GUILLERMO NAHUEL','LA RIOJA',  'APOS', 'M'),
    (3,  3, '10000039300', '39300300', 'GUARDIA, AXEL FACUNDO',           'LA RIOJA',  'APOS', 'M'),
    (4,  3, '10000032456', '32456789', 'LOPEZ, MARIA FERNANDA',           'LA RIOJA',  'APOS', 'F'),
    (5,  3, '10000025874', '25874136', 'ESPECHE, CARLOS EDUARDO',         'LA RIOJA',  'APOS', 'M'),
    (6,  3, '10000027052', '27052075', 'REYNA, DARDO',                    'LA RIOJA',  'APOS', 'M'),
    -- Afiliados sin cobertura para probar flujo negativo
    (7,  NULL, NULL,       '41230987', 'ROMERO, LUCIANA',                 'LA RIOJA',  NULL,   'F'),
    (8,  NULL, NULL,       '99999999', 'GARCIA, JUAN PRUEBA',             'LA RIOJA',  NULL,   'M');


-- ---------------------------------------------------------------------------
-- 9. DATOS DE PRUEBA — auditoria (eventos recientes)
-- ---------------------------------------------------------------------------

USE `whatsapp_agent`;

INSERT INTO `auditoria` (`usuario_id`, `evento`, `detalles`, `fecha`) VALUES
    ('5968775437', 'PEDIDO_COMPLETADO',  '{"pedido_id":"PED-20260423180000-5437","items":1,"total":136576.39}', '2026-04-23 18:05:00'),
    ('1604143115', 'PEDIDO_COMPLETADO',  '{"pedido_id":"PED-20260417190038-3115","items":2,"total":15745.00}',  '2026-04-17 19:05:00'),
    ('5493312244', 'PEDIDO_COMPLETADO',  '{"pedido_id":"PED-20260421090000-5566","items":1,"total":54509.71}',  '2026-04-21 09:05:00'),
    ('5491122334', 'PEDIDO_COMPLETADO',  '{"pedido_id":"PED-20260420120000-3344","items":1,"total":21999.80}',  '2026-04-20 12:05:00'),
    ('5968775437', 'BUSQUEDA',           '{"termino":"INVOKANA","encontrado":true}',                             '2026-04-23 17:55:00'),
    ('1604143115', 'BUSQUEDA',           '{"termino":"GRIPABEN","encontrado":true}',                             '2026-04-17 18:50:00'),
    ('5493398765', 'BUSQUEDA',           '{"termino":"AMOXICILINA","encontrado":false}',                         '2026-04-22 10:00:00'),
    ('5493398765', 'BLOQUEADO',          '{"razon":"palabra prohibida"}',                                        '2026-04-22 10:01:00');


-- ---------------------------------------------------------------------------
-- FIN DEL SCRIPT
-- Verificación rápida:
-- ---------------------------------------------------------------------------
SELECT 'whatsapp_agent.clientes'    AS tabla, COUNT(*) AS filas FROM whatsapp_agent.clientes
UNION ALL
SELECT 'whatsapp_agent.pedidos',               COUNT(*)         FROM whatsapp_agent.pedidos
UNION ALL
SELECT 'whatsapp_agent.direcciones',           COUNT(*)         FROM whatsapp_agent.direcciones
UNION ALL
SELECT 'whatsapp_agent.auditoria',             COUNT(*)         FROM whatsapp_agent.auditoria
UNION ALL
SELECT 'apos.afiliados',                       COUNT(*)         FROM apos.afiliados;
