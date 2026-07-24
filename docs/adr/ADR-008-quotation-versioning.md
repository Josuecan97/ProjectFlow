# ADR-008: Versionado e inmutabilidad de Cotizaciones

> Estado: Aprobado para Sprint 2
> Fecha: 2026-07-24

## Contexto

Una Cotización es un acuerdo comercial versionado, no solamente un PDF. Después de la
aprobación se necesita corregir datos administrativos sin perder trazabilidad y
negociar cambios comerciales sin alterar lo aceptado.

## Decisión

Quote es la raíz del agregado. QuoteVersion contiene una propuesta reproducible y
QuoteItem sus conceptos.

- Draft modifica la misma versión.
- Aprobar congela campos comerciales.
- Un cambio comercial crea una nueva versión Draft copiada de la aprobada.
- Una corrección administrativa usa una lista explícita de campos y genera
  QuoteVersionRevision con antes, después, OrganizationMember y fecha.
- Quote conserva `current_version_id` y `approved_version_id`.
- Vigencia, moneda, alcance, condiciones, instantánea del cliente y totales viven en
  QuoteVersion porque forman parte del acuerdo reproducible.
- El folio utiliza QuoteSequence por Organización con bloqueo transaccional.

## Alternativas descartadas

- Sobrescribir la versión aprobada: destruye el acuerdo aceptado.
- Crear versión en cada tecla o guardado de Draft: genera ruido sin valor comercial.
- Guardar solo los datos actuales de Persona: impide reproducir el documento histórico.
- Usar `MAX(number)`: no es seguro ante concurrencia.
- Auditoría genérica sin campos permitidos: facilita cambios comerciales encubiertos.

## Consecuencias

- Las Actions separan edición draft, corrección administrativa y revisión comercial.
- La base debe reforzar tenant, secuencia y relaciones de versión.
- El PDF se genera desde QuoteVersion.
- Sprint 3 puede convertir la versión aprobada sin reinterpretar datos actuales.
- QuoteVersionRevision no sustituye la Bitácora funcional de futuros Sprints.
