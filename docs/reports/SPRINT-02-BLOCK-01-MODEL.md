# Sprint 2 — Bloque 01: Modelo de Cotizaciones

## Estado

Completado y validado el 24 de julio de 2026.

## Objetivo

Construir la base de datos y el modelo de dominio mínimo del agregado Cotización,
sin implementar todavía cálculos, transiciones de estado, interfaz ni generación
de PDF.

## Rama

`feature/s2-quotation-model`

## Archivos creados

- `app/Domain/Quotes/Enums/QuoteStatus.php`
- `app/Domain/Quotes/Enums/QuoteVersionStatus.php`
- `app/Domain/Quotes/Enums/QuoteVersionRevisionType.php`
- `app/Domain/Quotes/Models/Quote.php`
- `app/Domain/Quotes/Models/QuoteVersion.php`
- `app/Domain/Quotes/Models/QuoteItem.php`
- `app/Domain/Quotes/Models/QuoteVersionRevision.php`
- `app/Domain/Quotes/Models/QuoteSequence.php`
- `database/factories/QuoteFactory.php`
- `database/factories/QuoteVersionFactory.php`
- `database/factories/QuoteItemFactory.php`
- `database/migrations/2026_07_24_000003_create_quote_tables.php`
- `tests/Feature/Quotes/QuoteModelTest.php`

## Archivos modificados

- `app/Domain/Organizations/Models/Organization.php`
- `app/Domain/People/Models/Person.php`
- `database/seeders/AccessControlSeeder.php`

## Migración

La migración incorpora:

- secuencias de folios independientes por Organización;
- cotizaciones y sus punteros de versión vigente y aprobada;
- versiones comerciales inmutables;
- conceptos con precisión decimal;
- revisiones para cambios administrativos auditables;
- índices, restricciones, claves foráneas y borrado en cascada del agregado;
- restricciones multitenant verificadas por MariaDB y SQLite;
- validaciones de integridad monetaria a nivel de base de datos.

Las referencias históricas a miembros se vuelven nulas si un miembro es eliminado
físicamente. La Organización, la Cotización y el contenido de la versión se
conservan mientras exista la Organización.

## Componentes, servicios y eventos

No aplican en este bloque. Se crearán únicamente cuando exista comportamiento de
negocio que los justifique.

## Pruebas agregadas

Nueve pruebas de integración cubren:

- relaciones y casts del agregado;
- unicidad del folio por Organización;
- rechazo de Persona perteneciente a otra Organización;
- rechazo de versiones, conceptos y revisiones entre Organizaciones;
- rechazo de punteros a versiones de otra Cotización;
- límites monetarios de conceptos;
- eliminación consistente de una Organización;
- conservación del historial al eliminar físicamente un miembro;
- sustitución del permiso obsoleto `quotes.cancel` por `quotes.archive`.

## Validaciones

- `composer validate --strict`: correcto.
- Laravel Pint: correcto.
- PHPStan/Larastan: cero errores.
- Pest: 68 pruebas, 204 aserciones, todas correctas.
- `npm run build`: correcto.
- Migración limpia y seeding en MariaDB: correcto.
- Rollback y reaplicación de la migración en MariaDB: correcto.
- `git diff --check`: correcto.

## Riesgos y decisiones

- Las relaciones circulares entre Cotización y Versión no usan claves compuestas
  cíclicas porque impedirían el borrado consistente de una Organización. La
  pertenencia exacta de los punteros se protege con claves foráneas simples y
  triggers multitenant.
- La nulabilidad de referencias históricas a miembros existe únicamente para
  permitir su eliminación física. Las Actions de aprobación exigirán siempre un
  miembro válido.
- Los triggers se implementaron de forma compatible con MariaDB y SQLite y están
  cubiertos por pruebas. Deben conservarse sincronizados si el modelo cambia.

## Próximo bloque

Implementar el motor monetario y las operaciones de creación y edición de una
cotización Draft, incluyendo folio transaccional, validaciones y pruebas de
concurrencia e integridad.
