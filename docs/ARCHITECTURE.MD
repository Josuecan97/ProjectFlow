# ProjectFlow — Arquitectura

> Estado: Aprobado
> Última actualización: 2026-07-24

## Objetivo

Definir una arquitectura suficiente para entregar el MVP rápidamente, manteniendo
aislamiento entre organizaciones, claridad y capacidad de evolución.

## Stack

- PHP 8.4+
- Laravel 13
- Livewire 4, Volt 1 y Flux UI 2
- Tailwind CSS 4 y Vite 8
- MariaDB
- Pest
- Brick Math para aritmética decimal monetaria sin `float`

Redis, Horizon, Reverb y S3 quedan preparados, pero no son dependencias operativas
obligatorias del primer despliegue.

## Estilo arquitectónico

ProjectFlow será un monolito modular. Los dominios vivirán bajo `app/Domain` y usarán
las convenciones nativas de Laravel. No se crearán microservicios ni capas genéricas.

Flujo habitual:

```text
Livewire/HTTP
  → Validación y Policy
  → Action de negocio
  → Eloquent/Transacción
  → Evento después del commit
  → Listener o Job
```

## Fronteras

- Organización y Usuarios administran identidad, miembros internos y autorización.
- Suscripciones administra la membresía comercial y el modo de acceso de la Organización.
- Personas administra actores comerciales.
- Cotizaciones administra propuestas y versiones.
- Proyectos concentra la ejecución.
- Etapas y Acciones administran planeación y trabajo pendiente.
- Bitácora conserva hechos funcionales.
- Archivos administra metadatos, autorización y almacenamiento.
- Portal proyecta únicamente información compartida.
- Automatizaciones reacciona a eventos mediante un catálogo limitado.
- Dashboard consulta información de los demás módulos sin apropiarse de sus reglas.

## Multitenancy

Una base compartida con `organization_id` en toda tabla de negocio. Middleware resuelve
el contexto, scopes filtran consultas, Policies autorizan y pruebas intentan cruces de
tenant. Jobs y comandos reciben la organización explícitamente.

## Reglas de implementación

- Actions para operaciones de negocio con varias mutaciones.
- Enums para estados y transiciones explícitas.
- Transacciones para aprobaciones, conversiones y cierres.
- Events para hechos confirmados; Jobs idempotentes para efectos externos.
- Queries dedicadas únicamente cuando una consulta sea compleja o reutilizada.
- Ninguna regla de negocio importante dentro de componentes Livewire.
- Form Requests o validación equivalente y Policies en toda mutación.
- Tipado estricto, nombres descriptivos y sin abreviaturas innecesarias.
- Las versiones aprobadas son snapshots comerciales; las correcciones administrativas
  usan una Action y una revisión técnica separadas.

## Seguridad

- No confiar en `organization_id`, estados, totales o porcentajes recibidos del cliente.
- No exponer rutas físicas de archivos ni tokens completos.
- No registrar secretos o información sensible en logs.
- Portal con tokens hasheados, revocables, rotables y rate limiting.
- Respuestas que no revelen recursos pertenecientes a otra organización.

## Rendimiento

Primero se diseñarán índices por organización y relaciones principales. Se evitarán
N+1 mediante eager loading. Cache, colas y optimizaciones se añadirán solo cuando exista
una necesidad observable.

## Referencias

- `PROJECT_BIBLE.md`
- `MVP_TECHNICAL_SPEC.md`
- `DATABASE.MD`
- `architecture/permissions.md`
- ADR-001 a ADR-008 según el módulo afectado
