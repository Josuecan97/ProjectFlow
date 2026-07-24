# Sprint 2 — Reporte del bloque documental

> Fecha: 2026-07-24
> Rama: `docs/s2-quotation-spec`
> Estado: Completado; pendiente de aprobación

## Objetivo

Convertir las reglas aprobadas de Cotizaciones en una especificación funcional y
técnica consistente antes de iniciar implementación.

## Documentos creados

- `docs/adr/ADR-008-quotation-versioning.md`
- `docs/reports/SPRINT-02-DOCUMENTATION-BLOCK.md`

## Documentos modificados

- PROJECT_BIBLE, ROADMAP, AI_CONTEXT, ARCHITECTURE y DECISIONS.
- MVP_TECHNICAL_SPEC y DATABASE.
- Sprint 2, módulo Quotes y contrato interno de aplicación.
- Entidades, migraciones, relaciones, módulos, permisos, eventos y colas.

## Decisiones formalizadas

- Draft edita la misma versión.
- Correcciones administrativas posteriores a aprobación conservan revisión técnica.
- Cambios comerciales crean una nueva versión Draft.
- Quote diferencia versión actual y versión aprobada.
- Las versiones conservan snapshot de Cliente, moneda, vigencia, alcance y condiciones.
- Folio secuencial transaccional por Organización.
- Aprobación vinculada a OrganizationMember.
- Estados draft, sent, approved, rejected, expired y archived.
- Expiración idempotente.
- PDF básico bajo demanda y sin persistencia histórica.
- Precisión decimal interna y presentación monetaria a dos decimales.

## Migraciones

No se agregaron migraciones durante este bloque documental.

## Validaciones

No se modificó código ejecutable. El bloque terminó con:

- Composer Validate correcto.
- Laravel Pint correcto.
- Larastan/PHPStan sin errores.
- 59 pruebas y 178 aserciones correctas.
- Build de Vite correcto.
- `git diff --check` correcto.

Las pruebas funcionales de Cotizaciones se crearán en las ramas de implementación.

## Riesgos

- La distinción semántica entre una corrección de redacción y un cambio de alcance
  requiere una operación de interfaz explícita y auditoría; no puede inferirse
  automáticamente a partir del texto.
- La generación PDF necesitará seleccionar una herramienta compatible antes de su
  bloque de implementación.
- Expirar automáticamente requiere comando programado idempotente, además de
  verificación al consultar o mutar.

## Pendientes

- Aprobación del bloque documental.
- Implementar en ramas feature separadas: modelo, cálculos, workflow, interfaz, PDF y
  pruebas/auditoría.
- No se inició código del Sprint 2.
