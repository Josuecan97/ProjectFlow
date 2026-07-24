# Sprint 00 — Fundaciones

> Estado: Completado
> Inicio: 2026-07-23
> Finalización: 2026-07-23

## Objetivo

Dejar una aplicación navegable, segura y verificable sobre la que se construirán los
módulos de negocio.

## Alcance

- Laravel, PHP, MariaDB, Livewire, Volt, Flux UI, Tailwind y Vite.
- Registro, login, recuperación, verificación de correo y logout.
- Layout, navegación, dashboard inicial y perfil.
- Organización, configuración y selector.
- Aislamiento completo entre Organizaciones.
- OrganizationMembers, invitaciones, activación, suspensión y soft delete.
- Roles, permisos y Policies.
- OrganizationSubscription, prueba automática de 14 días e historial.
- Acceso completo durante prueba y solo lectura al vencer.
- Pantallas de Organización, miembros, roles y suscripción.
- Migraciones, modelos, relaciones, Enums, validaciones, factories y seeders.

## Criterios de aceptación

- Autenticación completa.
- Un usuario puede pertenecer a varias Organizaciones.
- No existe acceso cruzado.
- Roles y permisos controlan cada operación.
- Crear una Organización genera su prueba de 14 días.
- La expiración bloquea mutaciones y conserva consultas.
- Migraciones ejecutan desde cero.
- Composer, Artisan, pruebas, Pint y build frontend pasan.

## Fuera de alcance

Personas, Cotizaciones, Proyectos, Etapas, Acciones, Bitácora y Portal del Cliente.
