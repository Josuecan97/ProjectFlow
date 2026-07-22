# ProjectFlow - Arquitectura

> Este documento define la arquitectura técnica oficial del proyecto.

---

# Objetivos

La arquitectura debe ser:

- Escalable
- Modular
- Fácil de mantener
- Basada en convenciones de Laravel
- Preparada para crecer sin reescribir módulos

---

# Stack

## Backend

- Laravel 12
- PHP 8.4+

## Frontend

- Livewire 3
- Volt
- Flux UI
- Tailwind CSS

## Base de Datos

- MariaDB

## Infraestructura

- Redis
- Horizon
- Reverb
- AWS S3

---

# Arquitectura

Se seguirá una arquitectura modular.

Cada módulo será independiente.

Ejemplo:

app/

Modules/

    Personas/

    Proyectos/

    Cotizaciones/

    Bitacora/

    Acciones/

    Portal/

---

Cada módulo contendrá únicamente lo necesario para funcionar.

Ejemplo:

Modules/

    Personas/

        Actions/

        Models/

        Policies/

        Requests/

        Resources/

        Livewire/

        Events/

        Listeners/

---

# Capas

Presentación

↓

Livewire

↓

Actions

↓

Models

↓

Database

---

# Reglas

Siempre utilizar:

- Form Requests
- Policies
- Route Model Binding
- Eloquent
- Soft Deletes cuando aplique

---

# Eventos

Los eventos forman parte del núcleo del sistema.

Ejemplos:

ProjectCreated

ProjectCompleted

ActivityCreated

ActionCompleted

QuoteApproved

---

# Jobs

Los procesos pesados deben ejecutarse mediante Queue.

Ejemplos:

Enviar correos

Generar PDF

Procesar imágenes

Notificaciones

---

# Livewire

Cada pantalla tendrá componentes pequeños.

Evitar componentes gigantes.

Separar:

UI

Lógica

Validación

---

# Base de datos

Las relaciones deben ser explícitas.

No duplicar información.

Preferir integridad referencial.

---

# Convenciones

Seguir PSR.

Tipado estricto.

Nombres descriptivos.

Código autodocumentado.

No usar abreviaturas.

---

# Objetivo

Toda decisión técnica debe favorecer la simplicidad y el mantenimiento a largo plazo.