# Automations

> Estado: Propuesta para aprobación
> Última actualización: 2026-07-23

## Alcance del MVP

Automatizaciones basadas en eventos mediante un catálogo predefinido. Cada Organización
puede activar y configurar combinaciones permitidas, pero no crear código, condiciones
arbitrarias o flujos encadenados.

## Catálogo inicial

- Acción asignada, próxima a vencer, vencida o completada.
- Etapa completada.
- Proyecto con cambio de estado o entregado.

Acciones permitidas: crear Bitácora y enviar notificaciones internas o correo cuando
esté configurado.

## Reglas

- Se ejecutan después del commit.
- Jobs idempotentes y reintentables.
- Conservan contexto de Organización.
- Nunca evaden Policies ni exponen contenido interno en el Portal.
- Fallar una notificación no revierte la operación de negocio ya confirmada.

## Permiso

Solo `automations.manage` puede activar o modificar configuraciones.

## Fuera del MVP

Constructor visual, condiciones libres, webhooks configurables y acciones con IA.
