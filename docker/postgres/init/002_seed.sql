-- =========================================================
-- CATÁLOGOS INICIALES
-- =========================================================
INSERT INTO privacy.data_category (code, name, is_sensitive)
VALUES
('ID','Identificación',TRUE),
('CONTACT','Contacto',FALSE)
ON CONFLICT DO NOTHING;

INSERT INTO privacy.legal_basis (code, name)
VALUES
('CONSENT','Consentimiento'),
('CONTRACT','Ejecución de contrato')
ON CONFLICT DO NOTHING;





INSERT INTO core.org (org_id, name, ruc, industry)
VALUES (1, 'Organiz. de Prueba', '9999999999', 'Cooperativa')
ON CONFLICT DO NOTHING;


INSERT INTO privacy.country (iso_code, name) VALUES
('EC', 'Ecuador'),
('US', 'Estados Unidos'),
('MX', 'México'),
('ES', 'España'),
('FR', 'Francia')
ON CONFLICT DO NOTHING; 


INSERT INTO privacy.recipient (org_id, name, recipient_type, contact_email, is_third_party) VALUES
(1, 'Proveedor Cloud XYZ', 'Proveedor', 'contacto@cloudxyz.com', TRUE),
(1, 'Departamento Legal', 'Interno', 'legal@empresa.com', FALSE),
(1, 'Proveedor Analytics', 'Proveedor', 'analytics@proveedor.com', TRUE)
ON CONFLICT DO NOTHING;


INSERT INTO privacy.data_category (code, name, is_sensitive, description) VALUES
('ID', 'Identificación', TRUE, 'Datos de identificación como cédula, pasaporte'),
('CONTACT', 'Contacto', FALSE, 'Teléfono, email, dirección'),
('FIN', 'Financieros', TRUE, 'Cuenta bancaria, tarjeta de crédito'),
('HEALTH', 'Salud', TRUE, 'Datos de salud, historial médico'),
('EMP', 'Empleo', FALSE, 'Datos laborales y experiencia profesional')
ON CONFLICT DO NOTHING;


-- 


-- 1. Limpiar tabla (Cuidado: esto borra las relaciones en role_permission y user_role si no hay cascada)
TRUNCATE TABLE iam.role CASCADE;

-- 2. Reiniciar secuencia (opcional, para que empiece en 1)
ALTER SEQUENCE iam.role_role_id_seq RESTART WITH 1;


INSERT INTO iam.role (name, description, status) VALUES 
('TITULAR', 'Titular (usuario / dueño de los datos)', 'activo'),
('RESPONSABLE_TRATAMIENTO', 'Responsable del tratamiento (controlador)', 'activo'),
('ENCARGADO_TRATAMIENTO', 'Encargado del tratamiento (procesador)', 'activo'),
('DESTINATARIO', 'Destinatario (receptor de datos)', 'activo'),
('DELEGADO_DPO', 'Delegado de Protección de Datos (DPO)', 'activo'),
('AUTORIDAD', 'Autoridad de Protección de Datos Personales', 'activo');

INSERT INTO iam.role (name, description, status) VALUES 
('ADMIN_SISTEMA', 'Administrador del sistema (TI)', 'activo'),
('ADMIN_SEGURIDAD', 'Administrador de seguridad', 'activo'),
('AUDITOR_INTERNO', 'Auditor interno', 'activo'),
('ATENCION_SOLICITUDES_ARCO', 'Atención de solicitudes (ARCO+)', 'activo'),
('GESTOR_CONSENTIMIENTOS', 'Gestor de consentimientos', 'activo'),
('GESTOR_INCIDENTES', 'Gestor de incidentes', 'activo');

-- superadministrador
INSERT INTO iam.app_user(unit_id,email, full_name, status, created_at, password) values (1,'alex.31roldan@gmail.com','Adminstrador del Sistema (TI)','activo','2026-03-26 20:56:01','$2y$12$QtKL2h8ANP1GPQCt3xRNzeLgf9rUJSmFI0sOpmqJ/ERZDl1skAtmW');

--rol del superadministrador
INSERT INTO iam.user_role (user_id, role_id) SELECT 1, role_id FROM iam.role WHERE name = 'ADMIN_SISTEMA';