# ADR-006: Automatizaciones limitadas del MVP

> Estado: Propuesta para aprobación
> Fecha: 2026-07-23

## Contexto

La Biblia incluye Automatizaciones en el MVP, pero un constructor libre de flujos
añadiría complejidad, validación y riesgos difíciles de estabilizar.

## Decisión

El MVP ofrecerá un catálogo predefinido de combinaciones evento–acción que cada
Organización podrá activar y configurar. Los efectos externos se ejecutarán mediante
Jobs idempotentes después del commit.

## Consecuencias

- Se cumplen recordatorios y registros automáticos del MVP.
- No se admite código, condiciones arbitrarias ni flujos encadenados.
- El modelo puede evolucionar hacia reglas más flexibles después del MVP.
