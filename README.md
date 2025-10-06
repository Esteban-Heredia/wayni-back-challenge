# 💼 Sistema de Gestión de Deudores y Entidades (Laravel)

![Laravel](https://img.shields.io/badge/Laravel-10-red)
![PHP Version](https://img.shields.io/badge/PHP-8.2-blue)
![Tests](https://img.shields.io/badge/tests-passing-brightgreen)
![License](https://img.shields.io/badge/license-MIT-lightgrey)

Sistema Laravel para manejar información de **deudores, entidades bancarias y clientes**, con procesamiento de archivos masivos y APIs RESTful.

---

## 📝 Contenido

- [Instalación](#instalación)
- [Configuración](#configuración)
- [Migraciones y Seeders](#migraciones-y-seeders)
- [Endpoints](#endpoints)
- [Subida de archivos](#subida-de-archivos)
- [Tests](#tests)
- [Estructura del proyecto](#estructura-del-proyecto)
- [Contribución](#contribución)
- [Licencia](#licencia)

---

## ⚙️ Instalación

1. Clonar el repositorio:

```bash
git clone https://github.com/tuusuario/tu-repo.git
cd tu-repo

```
## Instalar composer

```bash
composer install
```

## Copiar el .env que se envia por email
Se envia por email una carpeta con .env de back y docker-compose.yml
( ejecutar al ultimo )

## Correr migraciones
```bash
php artisan migrate
```

## 🔧 Migraciones y Seeders
El proyecto incluye migraciones para las tablas:

* deudores_bcra
* deudores
* entidades_code
* entidades
* clientes
* upload_progress
* personal_access_tokens

| Método | Endpoint                              | Descripción                               |
|--------|---------------------------------------|-------------------------------------------|
| GET    | `/api/deudores/{cuil}`                | Obtener deudor por CUIL                   |
| GET    | `/api/deudores/situaciones/{codigo}`  | Deudores por situación                    |
| GET    | `/api/deudores/top/{cantidad}`        | Top deudores por monto                    |
| GET    | `/api/entidades/{codigo}`             | Obtener entidad por código                |
| GET    | `/api/entidadesFull`                  | Fusiona entidades desde tablas fuente     |
| GET    | `/api/deudoresFull`                   | Genera tabla deudores desde tablas fuente |
| POST   | `/api/uploadClientes`                 | Subir archivo de clientes                 |
| POST   | `/api/uploadEntidades`                | Subir archivo de entidades                |
| POST   | `/api/uploadFileDeudoresBCRA`         | Subir archivo de deudores BCRA            |

## Estructura de carpetas relevante
app/
 ├─ Models/
 │   ├─ Deudores.php
 │   ├─ Entidades.php
 │   └─ ...
 ├─ Jobs/
 │   └─ ProcessDeudoresBCRAFiles.php
 └─ Http/
     ├─ Controllers/
     │   └─ UploadController.php
     └─ ...
database/
 ├─ migrations/
 ├─ factories/
 └─ seeders/
storage/
 └─ app/private/
     ├─ clientes/uploads
     └─ entidades/uploads
tests/
 ├─ Feature/
 │   ├─ DeudoresCuilTest.php
 │   ├─ DeudoresSituacionTest.php
 │   ├─ UploadClientesTest.php
 │   └─ ...
