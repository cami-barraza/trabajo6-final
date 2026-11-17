-- Base de datos de CinePlus --

CREATE DATABASE IF NOT EXISTS cineplus;--
USE cineplus;

CREATE TABLE genero (
    gen_ID INT AUTO_INCREMENT,
    gen_Nombre VARCHAR(50) NOT NULL,
    CONSTRAINT pk_genero PRIMARY KEY (gen_ID),
    CONSTRAINT uq_genero_nombre UNIQUE (gen_Nombre)
);

CREATE TABLE contenido (
    cont_ID INT AUTO_INCREMENT,
    cont_Titulo VARCHAR(150) NOT NULL,
    cont_Anio INT NOT NULL,
    cont_Sinopsis VARCHAR(500),
    cont_Tipo ENUM('Pelicula', 'Serie') NOT NULL,
    CONSTRAINT pk_contenido PRIMARY KEY (cont_ID)
);

CREATE TABLE pelicula (
    peli_ID INT,
    peli_Duracion INT NOT NULL,
    CONSTRAINT pk_pelicula PRIMARY KEY (peli_ID),
    CONSTRAINT fk_pelicula_contenido FOREIGN KEY (peli_ID)
        REFERENCES contenido(cont_ID)
);

CREATE TABLE serie (
    serie_ID INT,
    serie_Temporadas INT NOT NULL,
    serie_EpisodiosTot INT NOT NULL,
    serie_DuracionProm INT NOT NULL,
    CONSTRAINT pk_serie PRIMARY KEY (serie_ID),
    CONSTRAINT fk_serie_contenido FOREIGN KEY (serie_ID)
        REFERENCES contenido(cont_ID)
);

CREATE TABLE contenido_genero (
    cont_ID INT NOT NULL,
    gen_ID INT NOT NULL,
    CONSTRAINT pk_contenido_genero PRIMARY KEY (cont_ID, gen_ID),
    CONSTRAINT fk_cg_contenido FOREIGN KEY (cont_ID) REFERENCES contenido(cont_ID),
    CONSTRAINT fk_cg_genero FOREIGN KEY (gen_ID) REFERENCES genero(gen_ID)
);

CREATE TABLE persona (
    per_ID INT AUTO_INCREMENT,
    per_NombreCompleto VARCHAR(80) NOT NULL,
    per_FechaNac DATE NOT NULL,
    CONSTRAINT pk_persona PRIMARY KEY (per_ID)
);

CREATE TABLE participacion (
    part_ID INT AUTO_INCREMENT,
    per_ID INT NOT NULL,
    cont_ID INT NOT NULL,
    part_Rol ENUM('Actor', 'Director', 'Guionista') NOT NULL,
    CONSTRAINT pk_participacion PRIMARY KEY (part_ID),
    CONSTRAINT fk_part_persona FOREIGN KEY (per_ID) REFERENCES persona(per_ID),
    CONSTRAINT fk_part_contenido FOREIGN KEY (cont_ID) REFERENCES contenido(cont_ID)
);

INSERT INTO genero (gen_Nombre) VALUES
('Drama'), ('Suspenso'), ('Ciencia Ficción'), ('Crimen'), ('Comedia');

INSERT INTO contenido (cont_Titulo, cont_Anio, cont_Sinopsis, cont_Tipo) VALUES
('Inception', 2010, 'Un ladrón que roba secretos corporativos mediante la tecnología de compartir sueños recibe la tarea de plantar una idea en la mente de un CEO.', 'Pelicula'),
('El Padrino', 1972, 'La historia del patriarca de una familia de la mafia italoamericana y la transferencia de su imperio criminal a su hijo reacio.', 'Pelicula'),
('Breaking Bad', 2008, 'Un profesor de química de secundaria se convierte en fabricante de metanfetamina tras ser diagnosticado con cáncer.', 'Serie'),
('Friends', 1994, 'Seis amigos veinteañeros atraviesan la vida y el amor en Manhattan.', 'Serie');

INSERT INTO pelicula (peli_ID, peli_Duracion) VALUES
(1, 148),
(2, 175);

INSERT INTO serie (serie_ID, serie_Temporadas, serie_EpisodiosTot, serie_DuracionProm) VALUES
(3, 5, 62, 47),
(4, 10, 236, 22);

INSERT INTO contenido_genero (cont_ID, gen_ID) VALUES
(1, 3), (1, 2),
(2, 1), (2, 4),
(3, 1), (3, 4), (3, 2),
(4, 5);

INSERT INTO persona (per_NombreCompleto, per_FechaNac) VALUES
('Christopher Nolan', '1970-07-30'),
('Leonardo DiCaprio', '1974-11-11'),
('Francis Ford Coppola', '1939-04-07'),
('Marlon Brando', '1924-04-03'),
('Vince Gilligan', '1967-02-10'),
('Bryan Cranston', '1956-03-07'),
('Jennifer Aniston', '1969-02-11');

INSERT INTO participacion (per_ID, cont_ID, part_Rol) VALUES
(1, 1, 'Director'),
(2, 1, 'Actor'),
(3, 2, 'Director'),
(4, 2, 'Actor'),
(5, 3, 'Guionista'),
(6, 3, 'Actor'),
(7, 4, 'Actor');


-- ---------- usuarios ----------
CREATE TABLE usuario (
    usu_ID INT AUTO_INCREMENT,
    usu_Nombre VARCHAR(50) NOT NULL,
    usu_Apellido VARCHAR(50) NOT NULL,
    usu_Correo VARCHAR(100) NOT NULL,
    usu_Password VARCHAR(255) NOT NULL,
    usu_Rol ENUM('admin','usuario','editor') NOT NULL DEFAULT 'usuario',
    CONSTRAINT pk_usuario PRIMARY KEY (usu_ID),
    CONSTRAINT uq_usuario_correo UNIQUE (usu_Correo)
);

-- Usuarios de prueba
-- admin@cineplus.com  -> admin123
-- usuario@cineplus.com -> usuario123
INSERT INTO usuario (usu_Nombre, usu_Apellido, usu_Correo, usu_Password, usu_Rol) VALUES
('Admin', 'CinePlus', 'admin@cineplus.com', '$2y$10$LelBrckuB513YBW4LpMQreBIG91zz6MIQSF0fqnw8rrnZJVCvVX.O', 'admin'),
('Usuario', 'Prueba', 'usuario@cineplus.com', '$2y$10$ckRaH/H4atotyvKtn9cZaeDsobY4a8VjMtYtHvrf/VKrii/edo/fK', 'usuario');
