# Changelog

Todos los cambios relevantes de ProjectFlow se documentan en este archivo.

El proyecto utiliza versionado semántico y crea versiones oficiales únicamente
después de la aprobación del Product Owner.

## [0.2.0] - 2026-07-24

### Agregado

- Módulo multitenant de Cotizaciones.
- Folios secuenciales y transaccionales por Organización.
- Versiones comerciales, conceptos y cálculos monetarios con precisión decimal.
- Estados Draft, Sent, Approved, Rejected, Expired y Archived.
- Aprobación trazable mediante OrganizationMember.
- Revisiones comerciales sin alterar la versión aprobada.
- Correcciones administrativas con historial antes/después.
- Expiración idempotente mediante scheduler.
- Interfaz Volt/Flux para listado, alta, edición, expediente y workflow.
- PDF básico por versión generado bajo demanda.
- Eventos `QuoteApproved` y `QuoteExpired`.
- Policies, Form Requests, factories y pruebas de aislamiento multitenant.

### Cambiado

- El permiso obsoleto `quotes.cancel` fue sustituido por `quotes.archive`.
- Brick Math se incorporó como dependencia directa para operaciones monetarias.
- Dompdf se incorporó para generación de documentos PDF.

### Seguridad

- Restricciones multitenant adicionales en MariaDB para todo el agregado.
- Validación de pertenencia entre Cotización, Persona, versión, concepto y revisión.
- Protección de PDFs contra acceso cruzado entre Organizaciones.
- Modo de solo lectura aplicado a todas las mutaciones de Cotizaciones.

### Decisiones

- QuoteVersion conserva el snapshot histórico del Cliente.
- Durante el MVP, el PDF utiliza los datos vigentes de la Organización y no almacena
  un snapshot histórico de su identidad.

## [0.1.0] - 2026-07-24

### Agregado

- Plataforma base, autenticación y Organizaciones.
- Equipo, invitaciones, roles y permisos.
- Membresías con prueba automática de 14 días y modo de solo lectura.
- Módulo Personas con roles comerciales y contactos.
- Integridad multitenant y análisis estático con PHPStan/Larastan.

[0.2.0]: https://github.com/Josuecan97/ProjectFlow/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/Josuecan97/ProjectFlow/releases/tag/v0.1.0
