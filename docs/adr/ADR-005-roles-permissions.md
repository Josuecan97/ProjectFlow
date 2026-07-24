# ADR-005: Roles por miembro interno

> Estado: Propuesta para aprobación
> Fecha: 2026-07-23

## Contexto

Un usuario puede pertenecer a distintas organizaciones con responsabilidades diferentes.
Los permisos directos al usuario dificultan auditoría y mantenimiento.

## Decisión

El usuario será global y se vinculará mediante OrganizationMember. Los permisos se
asignarán a Roles y los Roles a OrganizationMembers. Un miembro podrá tener varios roles.

Roles iniciales: Propietario, Administrador, Gerente de proyectos, Colaborador y
Consulta. Existirá exactamente un Propietario activo por Organización.

Estos son roles internos. El Cliente es una Persona con rol comercial `client` y en el
MVP consulta sus Proyectos mediante el Portal privado, sin OrganizationMember interno.

## Consecuencias

- La autorización siempre conoce OrganizationMember y Organización.
- No habrá permisos directos por usuario.
- Cambiar roles no modifica la identidad global.
- Transferir propiedad requerirá una operación transaccional y auditada.
