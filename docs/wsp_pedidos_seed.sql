-- =============================================================================
-- Datos de prueba: wsp_pedidos (replicados desde entorno local)
-- Generado: 2026-04-24
-- Ejecutar en producción luego de correr las migraciones de Laravel:
--   mysql -u root -p siscon < docs/wsp_pedidos_seed.sql
-- =============================================================================

USE `siscon`;

INSERT IGNORE INTO `wsp_pedidos`
    (`pedido_id`, `usuario_id`, `sucursal_id`, `cliente_nombre`, `cliente_dni`,
     `medicamentos`, `total`, `tipo_envio`, `ubicacion`, `metodo_pago`, `estado`, `pedido_at`, `created_at`, `updated_at`)
VALUES

-- ── Axel / 5437 ──────────────────────────────────────────────────────────────

('PED-20260331161224-5437', '5968775437', NULL, NULL, NULL,
 '[{"codigo":"0000042612","nombre":"IBUPROFENO TAURO 400","presentacion":"IBUPROFENO TAURO 400 | comp.rec.x 20"}]',
 3579.76, NULL, NULL, NULL, 'CONFIRMADO', '2026-03-31 16:12:24', NOW(), NOW()),

('PED-20260331161521-5437', '5968775437', NULL, 'N/A', 'N/A',
 '[{"codigo":"0000009474","nombre":"IBUPROFENO 600 FECOFAR","presentacion":"IBUPROFENO 600 FECOFAR | 600 mg comp.x 20"}]',
 2174.59, NULL, NULL, NULL, 'CONFIRMADO', '2026-03-31 16:15:21', NOW(), NOW()),

('PED-20260331174715-5437', '5437', NULL, 'Axel Facundo Guardia', '39300300',
 '[{"nombre":"ACTOS","cantidad":1,"subtotal":273.01}]',
 273.01, NULL, NULL, NULL, 'CONFIRMADO', '2026-03-31 17:47:15', NOW(), NOW()),

('PED-20260408161527-5437', '5437', NULL, 'Axel Facundo Guardia', '39300300',
 '[{"nombre":"ACTOS","cantidad":1,"subtotal":273.01}]',
 273.01, NULL, NULL, NULL, 'CONFIRMADO', '2026-04-08 16:15:27', NOW(), NOW()),

('PED-20260408163921-loop', 'loop', NULL, 'Usuario_loop', 'NO_REGISTRADO',
 '[{"codigo":"0000063627","nombre":"IBUPIRAC PLUS MAX","cantidad":1,"precio_unitario":4189.63,"subtotal":4189.63}]',
 4189.63, 'A_DOMICILIO', 'Av San Martin 450, Formosa', 'mercadopago', 'CONFIRMADO', '2026-04-08 16:39:21', NOW(), NOW()),

('PED-20260408164334-5437', '5437', NULL, 'Usuario_5437', 'NO_REGISTRADO',
 '[{"codigo":"0000000005","nombre":"ACTOS","cantidad":1,"precio_unitario":273.01,"subtotal":273.01}]',
 273.01, 'A_DOMICILIO', 'Cabo Perez 2001, La Rioja, Argentina', 'mercadopago', 'CONFIRMADO', '2026-04-08 16:44:21', NOW(), NOW()),

('PED-20260408164654-5437', '5437', NULL, 'Usuario_5437', '39300300',
 '[{"codigo":"0000000005","nombre":"ACTOS","cantidad":2,"precio_unitario":191.11,"subtotal":382.21},{"codigo":"0000064809","nombre":"FABOGESIC NIÑOS 4%","cantidad":1,"precio_unitario":8050.00,"subtotal":8050.00},{"codigo":"0000053590","nombre":"FABOGESIC 400 RAPIDA ACCION","cantidad":1,"precio_unitario":2127.67,"subtotal":2127.67}]',
 10559.89, 'A_DOMICILIO', 'https://maps.app.goo.gl/BJhwnZoZBCSTHy2WA', 'mercadopago', 'CONFIRMADO', '2026-04-08 16:47:38', NOW(), NOW()),

('PED-20260409190148-5437', '5437', NULL, 'Usuario_5437', '39300300',
 '[{"codigo":"0000053590","nombre":"FABOGESIC 400 RAPIDA ACCION","cantidad":1,"precio_unitario":2127.67,"subtotal":2127.67},{"codigo":"0000000005","nombre":"ACTOS","cantidad":1,"precio_unitario":191.11,"subtotal":191.11}]',
 2318.78, 'A_DOMICILIO', 'CABO PEREZ 2001 BARRIO ALTA RIOJA', 'mercadopago', 'CONFIRMADO', '2026-04-09 19:02:09', NOW(), NOW()),

('PED-20260409193941-5437', '5437', 2, 'Usuario_5437', 'NO_REGISTRADO',
 '[{"codigo":"0000073662","id_articulo":"0000073662","nombre":"ANAFLEX PLUS RAPIDA ACCION","cantidad":1,"precio_unitario":4467.92,"subtotal":4467.92},{"codigo":"0000057905","id_articulo":"0000057905","nombre":"ASPIRINETAS","cantidad":2,"precio_unitario":1891.00,"subtotal":3782.00}]',
 8249.92, 'A_DOMICILIO', 'Cabo Pérez 2001 La Rioja', 'mercadopago', 'CONFIRMADO', '2026-04-09 19:40:08', NOW(), NOW()),

-- ── Dardo / 3115 ─────────────────────────────────────────────────────────────

('PED-20260413173025-3115', '3115', 2, 'Usuario_3115', 'NO_REGISTRADO',
 '[{"codigo":"0000021851","id_articulo":"0000021851","nombre":"LOTRIAL","cantidad":2,"precio_unitario":5705.00,"subtotal":11410.00},{"codigo":"0000016807","id_articulo":"0000016807","nombre":"T4 MONTPELLIER 112","cantidad":1,"precio_unitario":32565.45,"subtotal":32565.45}]',
 43975.45, 'RETIRO_LOCAL', 'Retiro en sucursal', 'transferencia', 'CONFIRMADO', '2026-04-13 17:30:25', NOW(), NOW()),

('PED-20260414142127-3115', '3115', 2, 'Usuario_3115', '27052075',
 '[{"codigo":"0000021855","id_articulo":"0000021855","nombre":"LOTRIAL","cantidad":1,"precio_unitario":8672.30,"subtotal":8672.30},{"codigo":"0000054935","id_articulo":"0000054935","nombre":"TANVIMIL EBA","cantidad":2,"precio_unitario":14061.17,"subtotal":28122.33},{"codigo":"0000073650","id_articulo":"0000073650","nombre":"CENTRUM HOMBRE","cantidad":1,"precio_unitario":11613.06,"subtotal":11613.06}]',
 48407.69, 'RETIRO_LOCAL', 'Retiro en sucursal', 'mercadopago', 'CONFIRMADO', '2026-04-14 14:21:49', NOW(), NOW()),

('PED-20260414144034-3115', '3115', 2, 'dardo reyna', '27052075',
 '[{"codigo":"0000016814","id_articulo":"0000016814","nombre":"T4 MONTPELLIER 50","cantidad":1,"precio_unitario":9672.68,"subtotal":9672.68},{"codigo":"0000041712","id_articulo":"0000041712","nombre":"NOVALGINA","cantidad":1,"precio_unitario":14936.84,"subtotal":14936.84},{"codigo":"0000045405","id_articulo":"0000045405","nombre":"JABON TOCADOR DOVE EXFOLIANTE BLANCO","cantidad":2,"precio_unitario":1320.54,"subtotal":2641.09}]',
 27250.61, 'A_DOMICILIO', 'AV BENAVIDEZ 327', 'tarjeta', 'CONFIRMADO', '2026-04-14 14:43:23', NOW(), NOW()),

('PED-20260414144927-3115', '3115', 2, 'Usuario_3115', '27052075',
 '[{"codigo":"0000021855","id_articulo":"0000021855","nombre":"LOTRIAL","cantidad":2,"precio_unitario":8672.30,"subtotal":17344.60},{"codigo":"0000016808","id_articulo":"0000016808","nombre":"T4 MONTPELLIER 125","cantidad":1,"precio_unitario":25432.85,"subtotal":25432.85},{"codigo":"0000041714","id_articulo":"0000041714","nombre":"NOVALGINA","cantidad":1,"precio_unitario":6735.64,"subtotal":6735.64}]',
 49513.09, 'A_DOMICILIO', 'AV BENAVIDEZ 993', 'mercadopago', 'CONFIRMADO', '2026-04-14 14:50:31', NOW(), NOW()),

('PED-20260417190038-3115', '3115', 2, 'Usuario_3115', 'NO_REGISTRADO',
 '[{"codigo":"0000055417","id_articulo":"0000055417","nombre":"GRIPABEN PLUS","cantidad":1,"precio_unitario":9167.00,"subtotal":9167.00},{"codigo":"0000080849","id_articulo":"0000080849","nombre":"IBULGIA","cantidad":1,"precio_unitario":6578.00,"subtotal":6578.00}]',
 15745.00, 'RETIRO_LOCAL', 'Retiro en sucursal', 'mercadopago', 'CONFIRMADO', '2026-04-17 19:00:44', NOW(), NOW());

-- Verificación
SELECT pedido_id, cliente_nombre, total, estado, DATE_FORMAT(pedido_at,'%d/%m/%Y') AS fecha
FROM wsp_pedidos
ORDER BY pedido_at;
