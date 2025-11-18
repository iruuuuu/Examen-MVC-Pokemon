# TAREAS-PROYECTOS
🔴 Vulnerabilidades Críticas de Seguridad

1. Inyección SQL en TODOS los Repositorios

CRÍTICO: Todas las consultas SQL usan concatenación directa sin preparación de sentencias. ProyectRepository.php:9

// ❌ VULNERABLE - NO USAR

$q = "INSERT INTO projects (name, description, datetime) VALUES ('" . $proyect->getName() . "', '" . $proyect->getDescription() . "', '" . $proyect->getDatetime() . "')";

Solución: Existe un archivo ProyectRepositoy.php (con typo) que muestra la implementación correcta con prepared statements. ProyectRepositoy.php:8-10

1. Hash MD5 para Contraseñas

CRÍTICO: El sistema usa MD5 para hashear contraseñas, lo cual es completamente inseguro.

Solución Requerida: Implementar password\_hash() con bcrypt o argon2.

1. Path Traversal en Enrutamiento

El controlador principal carga archivos dinámicamente sin validación: mainController.php:16-18

Un atacante podría usar ?c=../../malicious para incluir archivos arbitrarios.

1. Sin Protección CSRF

Ningún formulario tiene tokens CSRF, permitiendo ataques de falsificación de peticiones.

🏗️ Arquitectura del Sistema

Patrón Front Controller

El flujo de peticiones es: mainController.php:10

index.php carga mainController.php

mainController.php inicia sesión con session\_start() mainController.php:16-18

Enruta según parámetro ?c (ej: ?c=proyect carga proyectController.php)

Si no hay usuario en sesión, muestra login mainController.php:23-25

Controladores de Dominio

Cada controlador usa bloques condicionales secuenciales que terminan con die() o exit():

proyectController.php: Crear/eliminar/ver proyectos proyectController.php:4-12

taskController.php: Crear/eliminar tareas taskController.php:16-30

userController.php: Login, registro, logout, avatar userController.php:67-79

Autorización Distribuida

Cada operación administrativa verifica manualmente el rol: proyectController.php:6

if ($\_SESSION['user']->getRoles() == 'admin') {

// operación

}

Problema: No hay centralización, lo que causa inconsistencias.

📋 Requisitos del Sistema

Base de Datos MySQL

Necesitas tres tablas principales:

projects (id, name, description, datetime)

tasks (id, name, description, datetime, project\_id, user\_id)

users (id, username, password, avatar, roles)

Estructura de Archivos

/controllers/

- mainController.php
- proyectController.php
- taskController.php
- userController.php

/models/

- Proyect.php, ProyectRepository.php
- Task.php, TaskRepository.php
- User.php, UserRepository.php
- Connection.php

/views/

- loginView.phtml
- mainView.phtml
- showProyect.phtml
- registerView.phtml
- editUserView.phtml

/helpers/

- FileHelper.php

/public/img/

⚠️ Problemas de Seguridad Adicionales

Sin Regeneración de Sesión

Después del login no se llama a session\_regenerate\_id(), permitiendo ataques de fijación de sesión.

Subida de Archivos Sin Validación

El sistema permite subir avatares sin validar extensión, tipo MIME o contenido. userController.php:71

Patrón POST-Redirect-GET

Todas las operaciones que modifican estado redirigen correctamente para evitar reenvíos: proyectController.php:11-12

🔧 Mejoras Prioritarias

URGENTE: Reemplazar todas las consultas SQL con prepared statements

URGENTE: Cambiar MD5 por password\_hash()

URGENTE: Validar parámetro ?c con whitelist

Implementar tokens CSRF en todos los formularios

Agregar session\_regenerate\_id() después del login

Centralizar lógica de autorización

Validar archivos subidos (extensión, MIME, tamaño)

Implementar rate limiting en login

Agregar logging de eventos de seguridad

Notes

El proyecto tiene una arquitectura simple tipo MVC sin framework, lo que facilita entenderlo pero introduce múltiples vulnerabilidades de seguridad críticas. Existe un archivo ProyectRepositoy.php (con typo en el nombre) que muestra la implementación correcta con prepared statements, sugiriendo que el desarrollador conoce las mejores prácticas pero no las aplicó consistentemente. ProyectRepositoy.php:8-10 NO DESPLEGAR EN PRODUCCIÓN sin corregir las vulnerabilidades de inyección SQL y hash de contraseñas.
# Examen-MVC-Pokemon
