# Sprint 2 — Bloque 04: Interfaz de Cotizaciones

## Estado

Completado y validado técnicamente el 24 de julio de 2026.

## Objetivo

Exponer el módulo de Cotizaciones mediante Volt y Flux UI sin trasladar reglas de
negocio desde las Actions hacia los componentes.

## Rama

`feature/s2-quotation-ui`

## Implementación

- Navegación principal hacia Cotizaciones condicionada por Policy.
- Listado tenant-aware con búsqueda, filtro de estado y paginación.
- Formulario unificado para creación, edición Draft y revisión comercial.
- Selección limitada a Personas activas con rol Cliente del tenant.
- Editor dinámico de conceptos.
- Expediente con datos comerciales, conceptos, totales, estado e historial.
- Acciones autorizadas para enviar, aprobar, rechazar, archivar y crear versión.
- Formulario de corrección administrativa y consulta de su auditoría.
- Modo de solo lectura cuando la membresía comercial no permite escrituras.
- Presentación monetaria mediante el calculador decimal oficial.

## Archivos creados

- `app/Domain/Quotes/Queries/QuoteIndexQuery.php`
- `resources/views/pages/quotes/form.blade.php`
- `resources/views/pages/quotes/index.blade.php`
- `resources/views/pages/quotes/show.blade.php`
- `tests/Feature/Quotes/QuoteInterfaceTest.php`

## Archivos modificados

- `resources/views/components/layouts/app.blade.php`
- `routes/web.php`

## Migraciones, servicios y eventos

No se agregaron migraciones, servicios de negocio ni eventos en este bloque.

## Pruebas agregadas

Seis pruebas cubren:

- renderizado de listado, alta, edición y expediente;
- creación y edición mediante Volt;
- transición a Sent desde el expediente;
- renderizado de revisión comercial y corrección administrativa;
- respuesta 404 ante acceso cruzado entre Organizaciones;
- lectura sin controles de mutación cuando la membresía está vencida.

## Validaciones

- `composer validate --strict`: correcto.
- `composer install`: correcto.
- Laravel Pint: correcto.
- PHPStan/Larastan: cero errores.
- Pest: 87 pruebas, 292 aserciones, todas correctas.
- `php artisan optimize:clear`: correcto.
- `php artisan route:list`: 45 rutas válidas.
- `php artisan about`: correcto.
- `npm run build`: correcto.
- `git diff --check`: correcto.

## Riesgos

- No había un navegador conectado a la sesión para realizar inspección visual
  automatizada. El renderizado y las interacciones se validaron mediante pruebas
  HTTP/Volt; queda recomendada una comprobación visual manual antes del cierre.
- El selector de Cliente carga la lista del tenant. El autocompletado paginado está
  oficialmente pospuesto y no se implementó.
- El formulario presenta totales confirmados después de guardar. Un cálculo reactivo
  en cliente no es necesario para integridad y puede evaluarse en una mejora futura.

## Próximo bloque

Implementar la generación de PDF bajo demanda desde una QuoteVersion autorizada,
incluyendo únicamente la información aprobada para Sprint 2.
