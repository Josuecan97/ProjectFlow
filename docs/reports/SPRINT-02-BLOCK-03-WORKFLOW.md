# Sprint 2 — Bloque 03: Workflow, versiones y auditoría

## Estado

Completado y validado el 24 de julio de 2026.

## Objetivo

Implementar el ciclo de vida aprobado de Cotizaciones, preservar acuerdos comerciales,
auditar correcciones administrativas y expirar propuestas vencidas de manera
idempotente.

## Rama

`feature/s2-quotation-workflow`

## Implementación

- Transiciones explícitas entre Draft, Sent, Approved, Rejected, Expired y Archived.
- Aprobación vinculada al OrganizationMember activo.
- Nueva versión Draft para cambios comerciales sobre una Cotización aprobada.
- Conservación del puntero y contenido de la última versión aprobada.
- Correcciones administrativas limitadas por lista permitida y registradas con antes,
  después, miembro y fecha.
- Rechazo y archivo sin eliminación de historial.
- Expiración idempotente por Organización.
- Comando `quotes:expire` programado diariamente a las 00:10.
- Eventos `QuoteApproved` y `QuoteExpired` despachados después del commit.
- Form Requests para revisión comercial y corrección administrativa.

## Archivos creados

- `app/Domain/Quotes/Actions/ApproveQuote.php`
- `app/Domain/Quotes/Actions/ArchiveQuote.php`
- `app/Domain/Quotes/Actions/CorrectApprovedQuoteVersion.php`
- `app/Domain/Quotes/Actions/CreateCommercialQuoteVersion.php`
- `app/Domain/Quotes/Actions/ExpireQuotes.php`
- `app/Domain/Quotes/Actions/RejectQuote.php`
- `app/Domain/Quotes/Actions/SendQuote.php`
- `app/Domain/Quotes/Events/QuoteApproved.php`
- `app/Domain/Quotes/Events/QuoteExpired.php`
- `app/Domain/Quotes/Support/AdministrativeCorrectionRules.php`
- `app/Http/Requests/Quotes/CorrectApprovedQuoteVersionRequest.php`
- `app/Http/Requests/Quotes/StoreCommercialQuoteVersionRequest.php`
- `tests/Feature/Quotes/QuoteWorkflowTest.php`

## Archivos modificados

- `app/Domain/Quotes/Enums/QuoteStatus.php`
- `app/Domain/Quotes/Models/QuoteVersion.php`
- `docs/architecture/events.md`
- `routes/console.php`

## Migraciones y componentes

No se agregaron migraciones ni componentes visuales en este bloque.

## Pruebas agregadas

Seis pruebas cubren:

- envío y aprobación trazable;
- rechazo de aprobación desde un estado inválido;
- revisión comercial sin alterar el acuerdo aprobado;
- corrección administrativa y rechazo de campos comerciales;
- expiración idempotente y evento único;
- rechazo, archivo y conservación de historial.

## Validaciones

- `composer validate --strict`: correcto.
- `composer install`: correcto.
- Laravel Pint: correcto.
- PHPStan/Larastan: cero errores.
- Pest: 81 pruebas, 265 aserciones, todas correctas.
- `npm run build`: correcto.
- Registro del scheduler y ejecución de `quotes:expire`: correctos.
- Workflow integral en MariaDB temporal: correcto.
- `git diff --check`: correcto.

## Riesgos

- El scheduler requiere que el despliegue ejecute `php artisan schedule:run` cada
  minuto, como cualquier scheduler nativo de Laravel.
- La clasificación administrativa se protege mediante una lista explícita. Los campos
  comerciales no pueden atravesar esa Action.
- Los eventos no tienen listeners todavía; constituyen puntos de integración para
  Bitácora y automatizaciones de Sprints futuros.

## Próximo bloque

Implementar consultas, rutas y componentes Volt/Flux para listado, alta, edición,
expediente, historial y ejecución autorizada del workflow.
