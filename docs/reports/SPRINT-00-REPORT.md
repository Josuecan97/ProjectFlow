# ProjectFlow — Reporte de entrega del Sprint 0

> Fecha: 2026-07-23
> Estado: Completado

## 1. Resumen de implementación

Se construyó la base técnica del SaaS con Laravel 13, PHP 8.5, MariaDB, Livewire 4,
Volt, Flux UI, Tailwind CSS 4 y Vite 8.

El Sprint incluye:

- registro, login, recuperación de contraseña, verificación de correo y logout;
- perfil, layout, navegación y Dashboard;
- creación, selección y configuración de Organizaciones;
- aislamiento multitenant mediante contexto validado por cada petición;
- miembros, invitaciones con token cifrado, activación, suspensión y soft delete;
- cinco roles protegidos, catálogo de permisos y Policies;
- prueba automática de 14 días con acceso completo;
- modo de solo lectura al vencer;
- estado, días restantes e historial de membresía;
- soporte interno para asignaciones manuales y proveedores de pago futuros;
- factories, seeders y cuenta demo.

## 2. Archivos creados y modificados

Las áreas principales son:

- `app/Domain/Organizations`: acciones, Enums, modelos, Policies y servicios.
- `app/Http/Controllers/Auth`: acciones HTTP de autenticación.
- `app/Http/Middleware`: resolución segura de la Organización.
- `resources/views/pages`: pantallas Volt de acceso, perfil, Dashboard y Organización.
- `database/migrations`: seis migraciones del dominio de Organizaciones.
- `database/factories` y `database/seeders`: datos de prueba y demo.
- `tests/Feature`: autenticación, multitenancy, permisos, invitaciones y membresía.
- `docs`: Biblia, especificación técnica, arquitectura, datos, ADR y Sprints alineados.

## 3. Validaciones ejecutadas

- `composer validate --strict`
- `composer install`
- `composer dump-autoload --optimize`
- limpieza completa de cachés de Laravel
- `php artisan route:list`
- `php artisan about`
- `php artisan migrate:fresh --seed --force` sobre MariaDB
- `php artisan test --compact`: 27 pruebas, 95 aserciones
- `./vendor/bin/pint --test`
- `npm run build`
- auditoría de Composer: sin vulnerabilidades
- auditoría de npm: sin vulnerabilidades

PHPStan no está instalado en el proyecto, por lo que no se ejecutó.

## 4. Problemas encontrados

- El repositorio inicial utilizaba SQLite aunque la documentación define MariaDB.
- Faltaban Livewire, Volt y Flux UI.
- “Membresía” se usaba tanto para miembros internos como para suscripción comercial.
- Faltaban definiciones cerradas para duración y comportamiento de la prueba.
- MySQL y MariaDB instalados localmente entraban en conflicto por puerto y directorio.
- El navegador integrado no estuvo disponible durante la revisión visual final.

## 5. Soluciones aplicadas

- Se configuró una instancia aislada de MariaDB para ProjectFlow en el puerto 3307.
- Se separaron explícitamente `OrganizationMember` y `OrganizationSubscription`.
- Se fijó la prueba en 14 días, acceso completo y solo lectura al vencer.
- Se instaló y configuró el stack oficial de Livewire.
- Se agregaron restricciones, índices, llaves foráneas y pruebas de aislamiento.
- Se añadieron pruebas HTTP de todas las pantallas para compensar la indisponibilidad
  del navegador en la revisión final.

## 6. Riesgos pendientes

- La integración con una pasarela de pago sigue intencionalmente pendiente.
- El envío real de correo requiere configurar un proveedor fuera del entorno local.
- Conviene repetir una revisión visual completa cuando el navegador integrado esté
  disponible.
- La transferencia de propiedad requiere un flujo dedicado cuando se incorpore a un
  Sprint de administración avanzada; el propietario actual está protegido contra
  suspensión, eliminación y reasignación accidental.

## 7. Recomendaciones para el Sprint 1

- Iniciar el módulo Personas sin mezclarlo con cuentas de acceso.
- Aplicar siempre `organization_id` desde el contexto resuelto, nunca desde entrada
  del usuario.
- Crear Policies, Form Requests y pruebas de acceso cruzado desde el primer bloque.
- Mantener el mismo ciclo de migración, pruebas, Pint, build y auditoría por bloque.
- Realizar primero una revisión visual breve del Sprint 0 y después desarrollar
  Personas en incrementos verticales pequeños.
