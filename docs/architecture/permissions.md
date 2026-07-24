# Permissions

> Estado: Activo
> Última actualización: 2026-07-24

## Modelo

User → OrganizationMember → Role → Permission.

Los permisos nunca se asignan directamente a User. La autorización valida en este orden:

1. Usuario autenticado.
2. OrganizationMember activo.
3. Recurso de la misma Organización.
4. Permiso requerido.
5. Restricción contextual, por ejemplo responsable de la Acción.

## Roles iniciales

- Propietario.
- Administrador.
- Gerente de proyectos.
- Colaborador.
- Consulta.

La matriz detallada se configurará con los permisos definidos en
`MVP_TECHNICAL_SPEC.md`. Propietario es el único que transfiere propiedad. Consulta no
crea ni modifica. Colaborador modifica únicamente recursos permitidos o asignados.

El Cliente no aparece en esta matriz porque no es un rol interno: es una Persona con
rol comercial y acceso limitado al Portal privado.

OrganizationSubscription no concede roles. Únicamente determina si la Organización
puede escribir o se encuentra en modo de solo lectura.

Personas utiliza `people.view`, `people.create`, `people.update` y `people.archive`.
Asignar roles comerciales o contactos requiere `people.update`.

Toda Policy debe incluir pruebas de permitido, denegado por permiso y denegado por
Organización diferente.
