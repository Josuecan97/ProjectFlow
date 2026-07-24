# Sprint 2 — Auditoría técnica final

## Estado

Auditoría completada el 24 de julio de 2026. El Sprint está implementado y validado,
pero no está cerrado oficialmente. No se creó tag ni release.

## Alcance auditado

- documentación oficial y ADR-008;
- modelo, migración, relaciones y restricciones multitenant;
- aritmética monetaria y folios;
- Actions, Policies, Form Requests y modo de solo lectura;
- workflow, aprobación, versionado, auditoría y expiración;
- eventos y scheduler;
- consultas, rutas y componentes Volt/Flux;
- generación y autorización del PDF;
- dependencias, análisis estático, pruebas y build.

## Resultado funcional

El módulo implementa:

- alta de Cotización para una Persona activa con rol Cliente;
- folio secuencial y transaccional por Organización;
- edición libre de la misma versión Draft;
- estados Draft, Sent, Approved, Rejected, Expired y Archived;
- aprobación trazada mediante OrganizationMember;
- acuerdo aprobado preservado y nueva versión ante cambio comercial;
- correcciones administrativas permitidas y auditadas;
- expiración idempotente;
- listado, formulario, expediente e historial;
- PDF por versión generado bajo demanda.

## Seguridad y multitenancy

Se verificó:

- autorización en Actions y rutas;
- Policies basadas en membresía activa y permisos;
- contexto de Organización en consultas y componentes;
- respuestas 404 para recursos de otro tenant;
- restricciones de base de datos para Persona, Cotización, versión, concepto, revisión
  y miembro;
- totales, folio, estado, versión y tenant resueltos exclusivamente en servidor;
- bloqueo de escrituras con membresía comercial vencida;
- PDF protegido contra mezcla de Cotización, versión y Organización;
- recursos remotos y ejecución PHP deshabilitados en Dompdf;
- mass assignment limitado mediante `#[Fillable]`.

No se detectó acceso cruzado entre Organizaciones.

## Integridad y rendimiento

- No se usa `float` en cálculos ni presentación monetaria.
- Brick Math conserva seis decimales internos y half-up para dos visibles.
- La secuencia usa fila por tenant y `lockForUpdate`, nunca `MAX(number)`.
- Las mutaciones del agregado se ejecutan en transacciones.
- La migración soporta instalación limpia, rollback y reaplicación en MariaDB.
- Las consultas del listado y expediente usan eager loading.
- Se retiraron relaciones no consumidas del expediente durante la auditoría.
- La expiración procesa Organizaciones y Cotizaciones en lotes.

## Problemas menores corregidos durante la auditoría

1. Se añadieron límites y validación anidada a la dirección en correcciones
   administrativas.
2. La pantalla de corrección ahora permite editar la dirección, como exige la regla
   aprobada.
3. El renderer PDF dejó de resolver el calculador mediante service locator y ahora
   usa inyección de dependencias.
4. Se eliminaron eager loads innecesarios del historial de versiones.
5. ROADMAP, AI_CONTEXT, PROJECT_BIBLE, módulo y documento del Sprint se sincronizaron
   con el estado real: implementado y auditado, pendiente de aprobación.

## Validaciones finales

- `composer validate --strict`
- `composer install --no-interaction --prefer-dist`
- `composer audit --locked`
- `./vendor/bin/pint --test`
- `composer analyse`
- limpieza de caches Laravel
- `php artisan route:list`
- `php artisan about`
- `php artisan schedule:list`
- `php artisan quotes:expire`
- `php artisan test --compact`
- `npm run build`
- migración limpia, seeding, rollback y reaplicación en MariaDB temporal
- pruebas funcionales de modelo, cálculos y workflow en MariaDB
- `pdfinfo`, `pdftotext` y renderizado PNG del PDF
- `git diff --check`

Resultado final: 90 pruebas, 301 aserciones, cero errores de PHPStan y cero advisories
de Composer.

## Riesgos pendientes

### Requiere decisión del Product Owner

El PDF usa el snapshot histórico del Cliente almacenado en QuoteVersion, pero muestra
los datos actuales de la Organización. La documentación aprobada exige incluir datos
de la Organización, pero no ordena congelarlos.

Agregar un snapshot de la Organización mejoraría la reproducción histórica, pero
modificaría el modelo de datos y la regla de versionado. No se implementó
silenciosamente. Debe decidirse si:

- se conserva el comportamiento actual para el MVP; o
- se agregan nombre legal, RFC, contacto y dirección de la Organización al snapshot
  de QuoteVersion antes del cierre.

### Operativos

- El despliegue debe ejecutar `php artisan schedule:run` cada minuto.
- La verificación visual automatizada de las páginas web quedó pendiente porque no
  había un navegador conectado. Las vistas sí pasaron pruebas HTTP/Volt.
- PDFs de muchas páginas deberán incluirse en regresiones futuras.
- Poppler se instaló únicamente como herramienta local de inspección; no es una
  dependencia de producción.

## Archivos y trazabilidad

El detalle por bloque está disponible en:

- `SPRINT-02-BLOCK-01-MODEL.md`
- `SPRINT-02-BLOCK-02-CALCULATIONS.md`
- `SPRINT-02-BLOCK-03-WORKFLOW.md`
- `SPRINT-02-BLOCK-04-INTERFACE.md`
- `SPRINT-02-BLOCK-05-PDF.md`

## Recomendaciones para Sprint 3

- La conversión a Proyecto debe partir exclusivamente de `approved_version_id`.
- Project debe conservar `quote_id` y `quote_version_id` para trazabilidad.
- La conversión debe ser transaccional, idempotente y autorizada por tenant.
- No reinterpretar totales o alcance desde la Persona actual.
- Definir antes de programar si una versión aprobada puede originar uno o varios
  Proyectos y cómo evitar conversiones duplicadas.
- Mantener Cotizaciones congelado salvo defectos críticos después del cierre aprobado.

## Conclusión

La implementación cumple el alcance aprobado del Sprint 2 y no presenta errores
técnicos conocidos que impidan su uso. El cierre oficial, tag y release permanecen
pendientes de aprobación explícita.
