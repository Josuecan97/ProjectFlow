# Sprint 2 — Bloque 02: Cálculos y edición Draft

## Estado

Completado y validado el 24 de julio de 2026.

## Objetivo

Implementar aritmética monetaria exacta, folios transaccionales por Organización y
las operaciones autorizadas para crear y editar la misma versión mientras una
Cotización permanezca en Draft.

## Rama

`feature/s2-quotation-calculations`

## Implementación

- Cálculo decimal con Brick Math, sin conversiones a `float`.
- Escala interna de seis decimales y presentación de dos decimales con half-up.
- Validación de cantidad, precio, descuento e impuesto.
- Folio `COT-000001` mediante secuencia por Organización y `lockForUpdate`.
- Creación atómica de Cotización, primera versión y conceptos.
- Edición atómica de la misma versión Draft, reemplazando conceptos y recalculando
  todos los importes en servidor.
- Validación obligatoria de Persona activa, mismo tenant y rol Cliente.
- Resolución del OrganizationMember activo que crea la versión.
- Policy de Cotizaciones y protección del modo de solo lectura.
- Form Requests basados en reglas reutilizables del dominio.

## Archivos creados

- `app/Domain/Quotes/Actions/CreateDraftQuote.php`
- `app/Domain/Quotes/Actions/UpdateDraftQuote.php`
- `app/Domain/Quotes/Policies/QuotePolicy.php`
- `app/Domain/Quotes/Services/QuoteCalculator.php`
- `app/Domain/Quotes/Services/QuoteDraftValidator.php`
- `app/Domain/Quotes/Services/QuoteMemberResolver.php`
- `app/Domain/Quotes/Services/QuoteNumberGenerator.php`
- `app/Domain/Quotes/Services/SaveQuoteDraft.php`
- `app/Domain/Quotes/Support/QuoteDraftRules.php`
- `app/Domain/Quotes/ValueObjects/QuoteItemAmounts.php`
- `app/Domain/Quotes/ValueObjects/QuoteTotals.php`
- `app/Http/Requests/Quotes/StoreQuoteRequest.php`
- `app/Http/Requests/Quotes/UpdateQuoteDraftRequest.php`
- `tests/Feature/Quotes/QuoteDraftActionsTest.php`

## Archivos modificados

- `app/Providers/AppServiceProvider.php`
- `composer.json`
- `composer.lock`
- `docs/ARCHITECTURE.MD`

## Migraciones, componentes y eventos

No se agregaron migraciones, componentes de interfaz ni eventos en este bloque.

## Pruebas agregadas

Siete pruebas cubren:

- precisión y redondeo monetario;
- rechazo de descuentos superiores al importe base;
- creación integral y totales derivados;
- secuencias independientes entre Organizaciones;
- exigencia del rol Cliente sin asignación silenciosa;
- edición de la misma versión Draft;
- autorización multitenant y membresía comercial en solo lectura.

## Validaciones

- `composer validate --strict`: correcto.
- `composer install`: correcto y reproducible desde lockfile.
- Laravel Pint: correcto.
- PHPStan/Larastan: cero errores.
- Pest: 75 pruebas, 233 aserciones, todas correctas.
- `npm run build`: correcto.
- Prueba funcional en MariaDB de migración limpia, folios y totales: correcta.
- `git diff --check`: correcto.

## Riesgos

- El límite del folio actual es `COT-999999`, conforme al formato aprobado. La
  secuencia falla explícitamente al agotarse en vez de producir un folio inválido.
- La sustitución completa de conceptos durante una edición Draft es segura porque
  ocurre dentro de la misma transacción. La futura interfaz no debe persistir
  conceptos individualmente fuera de la Action.

## Próximo bloque

Implementar estados, aprobación, versionado comercial, correcciones administrativas,
expiración idempotente, auditoría y eventos de dominio.
