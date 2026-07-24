# Personas

> Estado: Implementado en Sprint 1
> Última actualización: 2026-07-24

## Objetivo

Concentrar en un expediente único a todo individuo o entidad con quien una Organización
mantiene una relación.

## Alcance

- Personas físicas y morales.
- Roles comerciales múltiples.
- Contactos como Personas relacionadas.
- Listado, búsqueda, filtros, creación, edición, expediente, archivo y restauración.
- Detección de posibles duplicados.

## Reglas del dominio

- No existen Client, Prospect, Supplier, Partner ni Contact como modelos separados.
- Toda Persona lleva `organization_id`.
- Persona física requiere `first_name`; Persona moral requiere `legal_name`.
- `display_name` se calcula y persiste de forma coherente desde sus datos de identidad.
- Una Persona puede tener varios PersonRoles.
- `tax_id` se normaliza en mayúsculas y es único por Organización cuando existe.
- Correo y teléfono se normalizan para búsqueda y advertencias de duplicado.
- Archivar establece estado archived y soft delete; restaurar vuelve a estado active.
- Una relación de contacto conecta Personas del mismo tenant, no admite autorrelación y
  solo una relación principal por Persona moral.

## Roles comerciales

- prospect
- client
- supplier
- partner
- contact
- external_collaborator

Son un catálogo del sistema y se asignan mediante la tabla pivote `person_role`.

## Interfaz

- Listado con búsqueda y filtros por tipo, rol y estado.
- Formularios diferenciados para física y moral.
- Expediente con información general, roles y contactos.
- Advertencias no bloqueantes por coincidencia de correo o teléfono.
- Confirmación para archivar, restaurar y retirar relaciones.

## Permisos

- `people.view`
- `people.create`
- `people.update`
- `people.archive`

Cada operación requiere OrganizationMember activo, permiso, tenant coincidente y
membresía comercial con escritura cuando la operación modifica datos.

## Fuera de alcance

API pública, archivos, Bitácora, Cotizaciones, Proyectos e historial de cambios de
campos. Esas integraciones se incorporan en sus respectivos Sprints.
