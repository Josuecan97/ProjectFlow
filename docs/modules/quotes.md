# Cotizaciones

> Estado: Aprobado y estable — v0.2.0
> Última actualización: 2026-07-24

## Responsabilidad

Administrar propuestas comerciales entre una Organización y una Persona con rol
Cliente. El dominio Quotes depende de Organizations para tenant, membresía y permisos,
y de People para la contraparte. No crea Proyectos durante Sprint 2.

## Agregado

Quote es la raíz del agregado y conserva identidad, folio, Persona, estado y punteros a
la versión actual y aprobada.

QuoteVersion representa una propuesta comercial concreta y reproducible. Contiene
vigencia, moneda, datos de presentación del cliente, alcance, condiciones, conceptos y
totales.

QuoteItem pertenece a una sola versión. QuoteVersionRevision registra exclusivamente
correcciones administrativas sobre una versión aprobada.

## Reglas

- Toda entidad lleva o hereda un `organization_id` verificable.
- La Persona debe pertenecer al tenant y tener rol `client`.
- Agregar el rol Cliente requiere confirmación explícita y usa la Action de Personas.
- El rol nunca se asigna silenciosamente.
- La Cotización no se elimina; puede archivarse.
- Una versión draft es editable.
- Los campos comerciales de una versión aprobada son inmutables.
- Una corrección administrativa no crea versión y siempre genera revisión.
- Un cambio comercial sobre una aprobada crea una versión draft.
- Los totales son derivados y se recalculan en servidor.
- Las operaciones monetarias usan aritmética decimal: cuatro decimales para cantidad y
  tasa, seis para importes internos y dos visibles con redondeo half-up.
- El PDF se genera desde la versión seleccionada, nunca desde valores de formulario.

## Campos administrativos editables después de aprobación

- datos de presentación del contacto;
- correo;
- teléfono;
- dirección;
- observaciones;
- correcciones ortográficas o de redacción que no alteren alcance ni condiciones.

La Action de corrección utiliza una lista permitida explícita. Cualquier campo fuera de
esa lista se trata como cambio comercial y no puede mutar la versión aprobada.

## Campos comerciales versionados

- título y alcance;
- fechas de emisión y vencimiento;
- moneda;
- condiciones comerciales;
- conceptos, cantidades y unidades;
- precios, descuentos e impuestos.

## Estados y transiciones

- draft → sent, expired o archived;
- sent → approved, rejected, expired o archived;
- approved → archived, o crea una nueva versión draft ante cambio comercial;
- rejected → archived;
- expired → archived;
- archived es de solo consulta.

Las correcciones administrativas no cambian el estado approved.

## Autorización

- `quotes.view`
- `quotes.create`
- `quotes.update`
- `quotes.approve`
- `quotes.archive`

Toda mutación también exige membresía comercial con escritura.

## Interfaz del Sprint

- listado con búsqueda y filtros;
- alta de Cotización;
- editor de versión draft y conceptos;
- expediente con historial de versiones y auditoría administrativa;
- acciones de enviar, aprobar, rechazar y archivar;
- generación de PDF por versión.

## Integraciones futuras

Sprint 3 podrá convertir una versión aprobada en uno o varios Proyectos. Files,
Bitácora, Portal, contratos, facturación y pagos quedan fuera de este módulo.
