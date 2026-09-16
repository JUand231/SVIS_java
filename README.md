# SVIS - Sistema de Votaciones SENA CIMM

Proyecto del SENA para manejar las votaciones de representante de aprendices.
Lo hicimos porque antes eso se hacía en papel y era un enredo contar los votos.

## Qué hace

- El admin crea encuestas con candidatos (nombre + documento obligatorios, foto opcional).
- Cada votante entra con documento y ficha, vota con un token OTP y solo puede votar una vez.
- En el inicio se ven todos los candidatos con filtros por jornada, y cuando el aprendiz
  inicia sesión solo le salen los de su jornada.
- Cada candidato puede tener hasta 3 propuestas que se muestran en su tarjeta.
- Resultados con conteo y porcentaje.

## Tecnologías

- Backend: lenguaje Java (Servlets sin Spring), Maven, driver MySQL 8.0.33, war para Tomcat 9.
- Frontend: lenguaje PHP puro (sin framework), con cURL para llamar al API, más HTML/CSS/JS y SweetAlert2 para los avisos.
  Ojo: PHP es el lenguaje, Laravel es un framework hecho en PHP. Nosotros no usamos Laravel.
- Base de datos: MariaDB (la que trae XAMPP).

## Arquitectura

Por capas, sin framework pesado:

- `servlet` recibe la petición (hace de controlador).
- `service` tiene la lógica (crear encuesta, validar voto único, quemar token).
- `repository` habla con la base de datos (interfaces + clases `Jdbc...`).
- `dto` y `model` llevan los datos de un lado a otro.

El patrón que más usamos es Repository (cada tabla tiene su interfaz y su
implementación JDBC). No usamos Strategy: ese es para intercambiar algoritmos
en caliente y aquí no lo necesitamos, solo se elige la implementación JDBC
desde el `AppContext`.

## Estructura

```
svis-backend-java/   -> API Java (/api/login-admin, /api/login-votante, /api/encuestas, /api/tokens/generar, /api/votar, /api/resultados, /api/perfil)
frontend/            -> login.php, index.php, estudiante.php, admin.php, resultados.php, config.example.php
```

## Requisitos

- XAMPP con PHP 8.2 y MariaDB 10.4
- JDK 8+ y Maven
- Apache Tomcat 9

## Instalación

### 1. Base de datos

Importar en phpMyAdmin el archivo:

```
svis-backend-java/src/main/resources/votaciones.sql
```

Si ya tenían la base creada antes, agregar la columna de propuestas:

```sql
ALTER TABLE opcion ADD COLUMN propuestas TEXT DEFAULT NULL;
```

### 2. Backend

Compilar desde la carpeta del backend:

```
mvn clean package
```

Sale el war en `target/svis-api-1.0-SNAPSHOT.war`. Ese war se monta en Tomcat
con el contexto `/svis-api` (en nuestro caso con un `svis-api.xml` que apunta al target).

Configurar la conexión en `src/main/resources/db.properties`
(hay un `db.properties.example` de guía).

### 3. Frontend

Copiar la carpeta `frontend` a `htdocs/svis/frontend`, duplicar
`config.example.php` como `config.php` y ajustar la URL del API:

```php
API_BASE_URL = http://localhost:8080/svis-api
```

Después abrir `http://localhost/svis/frontend/login.php`.

## Cómo se usa

1. El admin entra y crea la encuesta. Cada línea de opciones va así:
   `Nombre | documento | foto | propuesta1 ; propuesta2`
   El nombre tiene que ser igual al registrado, si no lo rechaza.
2. El admin genera los tokens para la encuesta.
3. El aprendiz entra con documento + ficha, elige su candidato de su jornada,
   pone su token y vota. Si intenta votar otra vez le sale 409 (ya votó).
4. Los resultados se ven en `resultados.php`.

## Endpoints

| Método | Ruta | Para qué |
|---|---|---|
| POST | /api/login-admin | entra el admin |
| POST | /api/login-votante | entra el aprendiz |
| GET/POST/PUT | /api/encuestas | listar, crear, cerrar |
| POST/GET | /api/tokens/generar | generar y listar tokens |
| POST | /api/votar | votar con OTP |
| GET | /api/resultados?encuestaId= | conteo y porcentaje |
| GET | /api/perfil?usuarioId= | jornada del votante |

## Usuarios de prueba

- Admin: `juanda / 123`
- Aprendiz mañana: `1058274558 / 3232460`
- Aprendiz tarde: `1000000003 / 3232462`
- Aprendiz noche: `1000000005 / 3232464`

## Notas

- Las jornadas se normalizan (mañana/tarde/noche) porque en la BD a veces
  llegaba con tilde y otras sin tilde y eso duplicaba los filtros.
- La foto del candidato es opcional, si no hay sale la inicial.
- El voto es único por encuesta: tabla `token_otp` con UNIQUE por
  (encuesta, código) y (encuesta, usuario).
