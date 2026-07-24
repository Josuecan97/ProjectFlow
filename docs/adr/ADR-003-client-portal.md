# ADR-003: Portal del Cliente mediante enlace privado

> Estado: Propuesta para aprobación
> Fecha: 2026-07-23

## Contexto

El MVP debe informar al cliente sin obligarlo a crear una cuenta y sin exponer
información interna.

## Decisión

Cada Portal utilizará un token aleatorio de alta entropía. Solo se guardará su hash.
El enlace podrá expirar, rotarse y revocarse. Únicamente se proyectarán Activities y
Files con visibilidad `shared`.

Se aplicará rate limiting, `noindex`, auditoría de accesos y respuestas que no confirmen
la existencia de un Proyecto ante tokens inválidos.

## Alternativas descartadas

- Login obligatorio del cliente: aumenta fricción para el MVP.
- ID incremental o slug como secreto: enumerable.
- Guardar el token en texto plano: amplifica una fuga de base de datos.
- Portal que filtre solo en frontend: inseguro.

## Consecuencias

- Perder el enlace requiere rotarlo.
- El token completo solo puede mostrarse al crearlo.
- Toda consulta del Portal tiene un filtro server-side de visibilidad.
- Una futura cuenta de cliente podrá añadirse sin reemplazar esta estrategia.
