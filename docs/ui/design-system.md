# Design System

> Estado: Propuesta para aprobación
> Última actualización: 2026-07-23

## Base

Flux UI será la biblioteca principal, Tailwind CSS se utilizará para composición y
ajustes. Se evitarán componentes propios cuando Flux ya resuelva el patrón.

## Principios

- Claridad antes que densidad.
- Estado y siguiente acción siempre visibles.
- Formularios con etiquetas persistentes y errores junto al campo.
- Confirmación explícita para cancelar, archivar, revocar o cambiar estados críticos.
- Diseño responsive y navegación completa por teclado.
- Contraste WCAG AA y foco visible.
- Estados loading, vacío, error, sin permiso y éxito en toda pantalla.

## Convenciones

- Colores semánticos consistentes para estados y salud.
- No depender únicamente del color: incluir texto o icono.
- Fechas, moneda y zona horaria de la Organización.
- Tablas para explorar; paneles/resúmenes para decidir.
- El Dashboard evita gráficas decorativas y prioriza atención inmediata.

Los componentes específicos se definirán durante cada sprint sin cambiar estos
principios.
