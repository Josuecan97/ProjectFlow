# Events

> Estado: Aprobado
> Última actualización: 2026-07-24

## Principios

Los eventos describen hechos consumados y se emiten después de confirmar la transacción.
No sustituyen validación ni autorización. Deben transportar identificadores y contexto
de Organización, evitando grafos completos de modelos serializados.

## Catálogo inicial

- `QuoteApproved`
- `QuoteExpired`
- `ProjectCreated`
- `ProjectStatusChanged`
- `StageCompleted`
- `ActionAssigned`
- `ActionCompleted`
- `FileUploaded`
- `ActivityEntryCreated`
- `PortalEnabled`

## Reglas

- Nombre en pasado.
- Listener pequeño; trabajo externo en Job.
- Ningún listener debe producir ciclos.
- Los efectos repetibles usan clave de idempotencia.
- Crear Bitácora automáticamente es un efecto de eventos de dominio, no de observers
  genéricos difíciles de controlar.
