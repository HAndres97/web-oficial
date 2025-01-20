// Creaciones Tabla y referencias de la base de datos.
CREATE TABLE IF NOT EXISTS prestamos (
  `id` SMALLINT PRIMARY KEY AUTO_INCREMENT,
  `id_cliente` CHAR(9) NOT NULL,
  `id_patrimonio` TINYINT,
  `cantidad_solicitada` INT,
  `amortizacion` INT,
  `tipo_interes` TINYINT,
  `fecha_inicio` DATE,
  `fecha_interes` TINYINT,
  `fecha_final` DATE,
  `estado` ENUM('activo', 'pendiente', 'desactivado')
);

CREATE TABLE IF NOT EXISTS registros_intereses (
  `id` SMALLINT PRIMARY KEY AUTO_INCREMENT,
  `id_interes` SMALLINT,
  `cantidad` DECIMAL,
  `fecha_amortizado` DATE,
  `fecha_pago` DATE

);

CREATE TABLE IF NOT EXISTS clientes (
  `id` CHAR(9) NOT NULL PRIMARY KEY UNIQUE,
  `nombre` VARCHAR(500),
  `apellidos` VARCHAR(500),
  `movil` VARCHAR(500),
  `email` VARCHAR(500),
  `comentario` TINYTEXT DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS intereses (
  `id` SMALLINT PRIMARY KEY AUTO_INCREMENT,
  `id_prestamo` SMALLINT,
  `cantidad_interes` DECIMAL,
  `amortizado` boolean,
  `fecha_pago` DATE
);

CREATE TABLE IF NOT EXISTS patrimonios (
  `id` TINYINT PRIMARY KEY AUTO_INCREMENT,
  `nombre` VARCHAR(500),
  `cantidad` FLOAT
);

CREATE TABLE IF NOT EXISTS gastos (
  `id` SMALLINT PRIMARY KEY AUTO_INCREMENT,
  `id_patrimonio` TINYINT,
  `nombre` VARCHAR(500),
  `cantidad` FLOAT,
  `fecha` DATE
);

CREATE TABLE IF NOT EXISTS registro_morosidad (
  `id` SMALLINT PRIMARY KEY AUTO_INCREMENT,
  `id_interes` SMALLINT,
  `fecha_pago` DATE,
  `registro_pago` DATE DEFAULT NULL,
  `cantidad` FLOAT,
  `amortizado` boolean
);

CREATE TABLE IF NOT EXISTS inversiones (
  `id` TINYINT PRIMARY KEY AUTO_INCREMENT,
  `id_patrimonio` TINYINT,
  `nombre` VARCHAR(500),
  `cantidad` FLOAT,
  `fecha_registro` DATE,
  `estado` ENUM('activo', 'pendiente', 'desactivado'),
  `comentario` TINYTEXT DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS usuarios (
  `id` TINYINT PRIMARY KEY AUTO_INCREMENT,
  `correo` VARCHAR(500),
  `password` VARCHAR(500),
  `nombre` VARCHAR(500)
);
ALTER TABLE prestamos ADD CONSTRAINT prestamos_id_cliente_fk FOREIGN KEY (id_cliente) REFERENCES clientes (id);
ALTER TABLE prestamos ADD CONSTRAINT prestamos_id_patrimonio_fk FOREIGN KEY (id_patrimonio) REFERENCES patrimonios (id);
ALTER TABLE intereses ADD CONSTRAINT intereses_id_prestamo_fk FOREIGN KEY (id_prestamo) REFERENCES prestamos (id);
ALTER TABLE registro_morosidad ADD CONSTRAINT registro_morosidad_id_interes_fk FOREIGN KEY (id_interes) REFERENCES intereses (id);
ALTER TABLE registros_intereses ADD CONSTRAINT registros_intereses_id_interes_fk FOREIGN KEY (id_interes) REFERENCES intereses (id);
ALTER TABLE gastos ADD CONSTRAINT gastos_id_patrimonio_fk FOREIGN KEY (id_patrimonio) REFERENCES patrimonios (id);
ALTER TABLE inversiones ADD CONSTRAINT inversiones_id_patrimonio_fk FOREIGN KEY (id_patrimonio) REFERENCES patrimonios (id);