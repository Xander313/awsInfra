-- =========================================================
-- CREACIÓN DE SCHEMAS
-- =========================================================
CREATE SCHEMA IF NOT EXISTS core;
CREATE SCHEMA IF NOT EXISTS iam;
CREATE SCHEMA IF NOT EXISTS privacy;
CREATE SCHEMA IF NOT EXISTS risk;
CREATE SCHEMA IF NOT EXISTS audit;

-- =========================================================
-- CORE
-- =========================================================
CREATE TABLE core.org (
    org_id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    ruc VARCHAR(50) UNIQUE,
    industry VARCHAR(100),
    created_at TIMESTAMP NOT NULL DEFAULT now()
);

-- =========================================================
-- IAM (Usuarios y Seguridad)
-- =========================================================
CREATE TABLE iam.app_user (
    user_id BIGSERIAL PRIMARY KEY,
    unit_id BIGINT,
    email VARCHAR(255) NOT NULL UNIQUE,
    full_name VARCHAR(255) NOT NULL,
    status VARCHAR(50) NOT NULL,
    last_login_at TIMESTAMP,
    created_at TIMESTAMP NOT NULL DEFAULT now()
);

CREATE TABLE iam.role (
    role_id BIGSERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

CREATE TABLE iam.permission (
    perm_id BIGSERIAL PRIMARY KEY,
    code VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

CREATE TABLE iam.role_permission (
    role_id BIGINT REFERENCES iam.role(role_id) ON DELETE CASCADE,
    perm_id BIGINT REFERENCES iam.permission(perm_id) ON DELETE CASCADE,
    PRIMARY KEY (role_id, perm_id)
);

-- =========================================================
-- PRIVACY – SUJETOS Y ACTIVIDADES
-- =========================================================
CREATE TABLE privacy.data_subject (
    subject_id BIGSERIAL PRIMARY KEY,
    org_id BIGINT NOT NULL REFERENCES core.org(org_id),
    id_type VARCHAR(50) NOT NULL,
    id_number VARCHAR(100) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(50),
    address TEXT,
    verified_level INTEGER DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT now(),
    UNIQUE (org_id, id_type, id_number)
);

CREATE INDEX idx_data_subject_org ON privacy.data_subject(org_id);

CREATE TABLE privacy.processing_activity (
    pa_id BIGSERIAL PRIMARY KEY,
    org_id BIGINT NOT NULL REFERENCES core.org(org_id),
    owner_unit_id BIGINT,
    name VARCHAR(255) NOT NULL
);

CREATE INDEX idx_pa_org ON privacy.processing_activity(org_id);

-- =========================================================
-- PRIVACY – CATEGORÍAS Y BASE LEGAL
-- =========================================================
CREATE TABLE privacy.data_category (
    data_cat_id BIGSERIAL PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    is_sensitive BOOLEAN NOT NULL DEFAULT FALSE,
    description TEXT
);

CREATE TABLE privacy.legal_basis (
    legal_basis_id BIGSERIAL PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT
);

CREATE TABLE privacy.pa_data_category (
    pa_id BIGINT REFERENCES privacy.processing_activity(pa_id) ON DELETE CASCADE,
    data_cat_id BIGINT REFERENCES privacy.data_category(data_cat_id) ON DELETE CASCADE,
    collection_source TEXT,
    PRIMARY KEY (pa_id, data_cat_id)
);

-- =========================================================
-- CONSENTIMIENTO
-- =========================================================
CREATE TABLE privacy.consent (
    consent_id BIGSERIAL PRIMARY KEY,
    subject_id BIGINT NOT NULL REFERENCES privacy.data_subject(subject_id),
    notice_ver_id BIGINT,
    purpose_id BIGINT,
    given_at TIMESTAMP DEFAULT now(),
    revoked_at TIMESTAMP
);

-- =========================================================
-- DOCUMENTOS
-- =========================================================
CREATE TABLE privacy.document (
    doc_id BIGSERIAL PRIMARY KEY,
    org_id BIGINT REFERENCES core.org(org_id),
    title VARCHAR(255),
    doc_type VARCHAR(100),
    classification VARCHAR(50),
    created_by BIGINT REFERENCES iam.app_user(user_id),
    created_at TIMESTAMP DEFAULT now()
);

CREATE TABLE privacy.document_version (
    doc_ver_id BIGSERIAL PRIMARY KEY,
    doc_id BIGINT REFERENCES privacy.document(doc_id) ON DELETE CASCADE,
    version_no INTEGER NOT NULL,
    file_uri TEXT NOT NULL,
    checksum VARCHAR(255),
    created_at TIMESTAMP DEFAULT now(),
    active_flag BOOLEAN DEFAULT TRUE
);

-- =========================================================
-- SISTEMAS Y DATA STORE
-- =========================================================
CREATE TABLE privacy.system (
    system_id BIGSERIAL PRIMARY KEY,
    org_id BIGINT REFERENCES core.org(org_id),
    name VARCHAR(255) NOT NULL,
    type VARCHAR(100),
    hosting VARCHAR(100),
    owner_user_id BIGINT REFERENCES iam.app_user(user_id),
    criticality VARCHAR(50),
    description TEXT
);

CREATE TABLE privacy.data_store (
    store_id BIGSERIAL PRIMARY KEY,
    system_id BIGINT REFERENCES privacy.system(system_id),
    name VARCHAR(255),
    store_type VARCHAR(100),
    location TEXT,
    encryption_flag BOOLEAN,
    backup_flag BOOLEAN
);

-- =========================================================
-- PROVEEDORES Y TRANSFERENCIAS
-- =========================================================
CREATE TABLE privacy.country (
    country_id BIGSERIAL PRIMARY KEY,
    iso_code VARCHAR(10) UNIQUE,
    name VARCHAR(255)
);

CREATE TABLE privacy.recipient (
    recipient_id BIGSERIAL PRIMARY KEY,
    org_id BIGINT REFERENCES core.org(org_id),
    name VARCHAR(255),
    recipient_type VARCHAR(50),
    contact_email VARCHAR(255),
    is_third_party BOOLEAN
);

CREATE TABLE privacy.transfer (
    transfer_id BIGSERIAL PRIMARY KEY,
    pa_id BIGINT REFERENCES privacy.processing_activity(pa_id),
    recipient_id BIGINT REFERENCES privacy.recipient(recipient_id),
    country_id BIGINT REFERENCES privacy.country(country_id),
    transfer_type VARCHAR(50),
    safeguard TEXT,
    legal_basis_text TEXT,
    created_at TIMESTAMP DEFAULT now()
);

-- =========================================================
-- RETENCIÓN Y DSAR
-- =========================================================
CREATE TABLE privacy.retention_rule (
    retention_id BIGSERIAL PRIMARY KEY,
    pa_id BIGINT REFERENCES privacy.processing_activity(pa_id),
    retention_period_days INTEGER,
    trigger_event VARCHAR(100),
    disposal_method VARCHAR(100),
    legal_hold_flag BOOLEAN
);

CREATE TABLE privacy.dsar_request (
    dsar_id BIGSERIAL PRIMARY KEY,
    org_id BIGINT REFERENCES core.org(org_id),
    subject_id BIGINT REFERENCES privacy.data_subject(subject_id),
    request_type VARCHAR(50),
    channel VARCHAR(50),
    received_at TIMESTAMP,
    due_at TIMESTAMP,
    status VARCHAR(50),
    assigned_to_user_id BIGINT REFERENCES iam.app_user(user_id),
    resolution_summary TEXT,
    closed_at TIMESTAMP
);

CREATE TABLE privacy.dsar_evidence (
    dsar_ev_id BIGSERIAL PRIMARY KEY,
    dsar_id BIGINT REFERENCES privacy.dsar_request(dsar_id),
    doc_ver_id BIGINT REFERENCES privacy.document_version(doc_ver_id),
    description TEXT,
    attached_at TIMESTAMP DEFAULT now()
);

-- =========================================================
-- RIESGOS Y DPIA
-- =========================================================
CREATE TABLE risk.dpia (
    dpia_id BIGSERIAL PRIMARY KEY,
    pa_id BIGINT NOT NULL REFERENCES privacy.processing_activity(pa_id),
    initiated_at TIMESTAMP DEFAULT now(),
    status VARCHAR(50) NOT NULL,
    summary TEXT
);

CREATE TABLE risk.risk (
    risk_id BIGSERIAL PRIMARY KEY,
    org_id BIGINT REFERENCES core.org(org_id),
    name VARCHAR(255),
    description TEXT,
    risk_type VARCHAR(50),
    status VARCHAR(50)
);

CREATE TABLE risk.dpia_risk (
    dpia_id BIGINT REFERENCES risk.dpia(dpia_id),
    risk_id BIGINT REFERENCES risk.risk(risk_id),
    rationale TEXT,
    PRIMARY KEY (dpia_id, risk_id)
);

-- =========================================================
-- AUDITORÍA
-- =========================================================
CREATE TABLE audit.audit (
    audit_id BIGSERIAL PRIMARY KEY,
    org_id BIGINT REFERENCES core.org(org_id),
    audit_type VARCHAR(100),
    scope TEXT,
    planned_at TIMESTAMP,
    executed_at TIMESTAMP,
    auditor_user_id BIGINT REFERENCES iam.app_user(user_id),
    status VARCHAR(50)
);

CREATE TABLE audit.control (
    control_id BIGSERIAL PRIMARY KEY,
    org_id BIGINT REFERENCES core.org(org_id),
    code VARCHAR(50),
    name VARCHAR(255),
    control_type VARCHAR(50),
    description TEXT,
    owner_user_id BIGINT REFERENCES iam.app_user(user_id)
);

CREATE TABLE audit.audit_finding (
    finding_id BIGSERIAL PRIMARY KEY,
    audit_id BIGINT REFERENCES audit.audit(audit_id),
    severity VARCHAR(50),
    description TEXT,
    control_id BIGINT REFERENCES audit.control(control_id),
    status VARCHAR(50)
);

CREATE TABLE audit.corrective_action (
    ca_id BIGSERIAL PRIMARY KEY,
    finding_id BIGINT REFERENCES audit.audit_finding(finding_id),
    owner_user_id BIGINT REFERENCES iam.app_user(user_id),
    due_at TIMESTAMP,
    status VARCHAR(50),
    closed_at TIMESTAMP,
    outcome TEXT
);

-- =========================================================
-- FORMACIÓN
-- =========================================================
CREATE TABLE privacy.training_course (
    course_id BIGSERIAL PRIMARY KEY,
    org_id BIGINT REFERENCES core.org(org_id),
    name VARCHAR(255),
    mandatory_flag BOOLEAN,
    renewal_days INTEGER
);

CREATE TABLE privacy.training_assignment (
    assign_id BIGSERIAL PRIMARY KEY,
    course_id BIGINT REFERENCES privacy.training_course(course_id),
    user_id BIGINT REFERENCES iam.app_user(user_id),
    assigned_at TIMESTAMP,
    due_at TIMESTAMP,
    status VARCHAR(50)
);

CREATE TABLE privacy.training_result (
    result_id BIGSERIAL PRIMARY KEY,
    assign_id BIGINT REFERENCES privacy.training_assignment(assign_id),
    completed_at TIMESTAMP,
    score INTEGER,
    certificate_doc_ver_id BIGINT REFERENCES privacy.document_version(doc_ver_id)
);

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
VALUES (1, 'Org de Pruebas', '9999999999', 'Tecnología')
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
