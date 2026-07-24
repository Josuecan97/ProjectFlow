# ADR-007: Membresía comercial de la Organización

> Estado: Aprobado
> Fecha: 2026-07-23

## Contexto

ProjectFlow necesita controlar el acceso comercial de cada Organización sin confundirlo
con los usuarios internos que pertenecen a ella.

## Decisión

`OrganizationMember` representa a un usuario interno. `OrganizationSubscription`
representa la membresía comercial.

Al crear una Organización, el sistema crea automáticamente una prueba de 14 días con
acceso completo. Al vencer, la Organización conserva acceso de solo lectura.

Inicialmente podrán asignarse suscripciones manuales. Una futura plataforma de pagos
podrá activarlas o renovarlas automáticamente mediante eventos verificados.

## Consecuencias

- Roles y permisos no dependen del plan comercial.
- Toda mutación verifica además que la suscripción permita escritura.
- Los cambios de suscripción conservan historial.
- Planes, precios y proveedor de pago se definirán posteriormente.
