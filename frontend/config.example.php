<?php
// Copia este archivo como config.php y ajusta la URL según tu Tomcat.
// Ejemplo: si NetBeans te desplegó como svis-api-1.0-SNAPSHOT, usa esa.
define('SITE_NAME', 'Votaciones SENA');
// Backend Java (Tomcat). Aquí apuntan los cURL de login.php
define('API_BASE_URL', 'http://localhost:8080/svis-api');
define('API_TIMEOUT', 10);
