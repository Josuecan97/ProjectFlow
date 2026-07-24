# ADR-001: Modelo único de Persona

> Estado: Implementado en Sprint 1
> Fecha: 2026-07-24

## Contexto

Una misma contraparte puede actuar como prospecto, cliente, proveedor, socio o
contacto. Separarla en tablas produciría duplicados y rompería su historial.

## Decisión

Existirá una tabla `people` con tipo física o moral y roles muchos-a-muchos. Los
contactos de una Persona moral también serán Personas, vinculadas mediante
`person_relationships`.

## Alternativas descartadas

- Tablas `clients`, `suppliers` y `contacts`: duplican identidad e historial.
- Un campo único `role` en Persona: impide roles simultáneos.
- Contactos como JSON: impide relaciones, búsqueda e historial.

## Consecuencias

- Una Persona mantiene identidad e historial únicos.
- La UI deberá permitir múltiples roles.
- Se requieren controles de duplicados dentro de cada Organización.
- El contacto puede participar directamente en proyectos sin copiar datos.

## Referencias

- `PROJECT_BIBLE.md`
- `MVP_TECHNICAL_SPEC.md`
