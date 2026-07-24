# Sprint 02 — Cotizaciones

> Estado: Aprobado y cerrado — versión v0.2.0
> Inicio documental: 2026-07-24
> Implementación: 2026-07-24
> Cierre: 2026-07-24

## Objetivo

Implementar Cotizaciones como entidades de negocio versionadas que representan una
propuesta comercial para una Persona con rol Cliente. Una Cotización aprobada conserva
el acuerdo aceptado y queda preparada para originar uno o varios Proyectos en Sprint 3.

## Alcance

- Folio secuencial por Organización con formato `COT-000001`.
- Persona asociada obligatoriamente con rol comercial Cliente.
- Estados: draft, sent, approved, rejected, expired y archived.
- Versiones, conceptos y cálculos monetarios en servidor.
- Fecha de emisión y vencimiento por versión.
- Aprobación trazable por OrganizationMember.
- Correcciones administrativas auditadas sobre versiones aprobadas.
- Nueva versión automática ante cambios comerciales posteriores a una aprobación.
- PDF básico generado bajo demanda para una versión.
- Policies, Actions, Enums, validaciones, factories, seeders y pruebas.
- Aislamiento por Organización y modo de solo lectura por membresía vencida.

## Versionado

- Mientras la versión actual esté en draft se edita directamente.
- Aprobar congela sus campos comerciales.
- Una corrección administrativa autorizada puede actualizar exclusivamente datos de
  presentación, contacto u observaciones. Debe registrar valores anteriores, nuevos,
  autor y fecha.
- Modificar conceptos, cantidades, precios, descuentos, impuestos, moneda, condiciones,
  alcance o fechas de una versión aprobada crea automáticamente una nueva versión
  draft.
- La nueva versión parte de una copia de la versión aprobada.
- `approved_version_id` conserva la última versión aprobada mientras
  `current_version_id` identifica la versión de trabajo.
- Aprobar una nueva versión la convierte en vigente sin borrar las anteriores.

## Cálculos

Por concepto:

```text
base = cantidad × precio unitario
subtotal = base − descuento
impuesto = subtotal × tasa de impuesto
total = subtotal + impuesto
```

- `discount_amount` es un importe fijo.
- `tax_rate` es un porcentaje.
- El descuento nunca supera la base.
- Los cálculos internos conservan la precisión decimal configurada.
- Cantidades y tasas admiten cuatro decimales; importes internos conservan seis.
- Los importes visibles se presentan con dos decimales y redondeo half-up.
- No se utiliza `float` para operaciones monetarias.
- Los totales de la versión se calculan en servidor y nunca se confían a la UI.

## Vigencia

- Cada versión tiene fecha de emisión y vencimiento.
- Una Cotización activa draft o sent cuya versión vigente supera `expires_on` sin
  aprobación cambia a expired mediante un proceso idempotente.
- La expiración conserva versiones, conceptos, totales y auditoría.

## PDF básico

Incluye Organización, Cliente, folio, versión, vigencia, conceptos, totales y
condiciones. Se genera bajo demanda y no se conserva como archivo histórico.

Quedan fuera: firma electrónica, envío por correo, plantillas personalizadas, anexos y
almacenamiento del PDF.

## Fuera de alcance

- Conversión efectiva a Proyecto.
- Facturación, contratos y pagos.
- Envío de Cotizaciones por correo.
- Portal del Cliente.
- Firma electrónica.
- API pública.
- Adjuntos y Bitácora funcional.

## Criterios de aceptación

- No existe acceso ni relación cruzada entre Organizaciones.
- El folio es único y seguro ante concurrencia dentro de la Organización.
- Solo una Persona con rol Cliente puede asociarse.
- Totales y límites de descuento están probados.
- Las transiciones requieren permisos y respetan solo lectura.
- Una versión aprobada conserva su acuerdo comercial.
- Toda corrección administrativa queda auditada.
- Un cambio comercial crea una nueva versión draft.
- La expiración es idempotente y conserva el historial.
- El PDF reproduce la versión solicitada.
- Migraciones, rollback, Pint, Larastan, pruebas y build terminan correctamente.
