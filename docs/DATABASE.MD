# ProjectFlow — Base de datos

> Estado: Aprobado para Sprints 0 y 1
> Última actualización: 2026-07-24
> Motor oficial: MariaDB

## Principios

- La base representa el negocio y protege el aislamiento por Organización.
- Todas las relaciones usan foreign keys cuando la tecnología lo permite.
- No se duplican entidades Cliente y Proveedor: ambas son roles de Persona.
- Los datos históricos se conservan.
- Estados, importes y porcentajes se validan en aplicación y base de datos cuando sea
  práctico.

## Mapa de entidades

```text
Organization
├── OrganizationMember ── User
├── OrganizationSubscription ── OrganizationSubscriptionEvent
├── Role ── Permission
├── Person ── PersonRole
│   └── Quote ── QuoteVersion ── QuoteItem
└── Project
    ├── ProjectParticipant ── Person
    ├── Stage
    │   └── Action ── ActionDependency
    ├── ActivityEntry
    ├── FileAttachment ── File
    ├── ProjectPortal ── PortalAccessLog
    └── AutomationSetting
```

## Relaciones congeladas

- Usuario ↔ Organización: muchos-a-muchos mediante OrganizationMember.
- OrganizationMember ↔ Role: muchos-a-muchos.
- Organización → OrganizationSubscription: uno-a-muchos histórico; una vigente.
- Role ↔ Permission: muchos-a-muchos.
- Organización → Persona: uno-a-muchos.
- Persona ↔ PersonRole: muchos-a-muchos.
- Persona → Cotización: uno-a-muchos.
- Cotización → QuoteVersion: uno-a-muchos.
- Cotización aprobada → Proyecto: uno-a-muchos.
- Proyecto ↔ Persona: muchos-a-muchos mediante ProjectParticipant.
- Proyecto → Etapa: uno-a-muchos.
- Etapa → Acción: uno-a-muchos obligatorio.
- Proyecto → ActivityEntry: uno-a-muchos.
- File se asocia a entidades permitidas mediante FileAttachment.
- Proyecto → ProjectPortal: uno-a-uno activo; los tokens anteriores se conservan
  revocados para auditoría.

## Tablas

La definición de columnas, estados, índices únicos y reglas se encuentra en
`MVP_TECHNICAL_SPEC.md`, sección “Modelo de datos”. El plan de creación se encuentra en
`database/migrations-plan.md`.

## Integridad de tenant

- Toda tabla de negocio lleva `organization_id`.
- Las claves únicas de negocio incluyen `organization_id`.
- Action conserva `project_id` y `stage_id`; el servicio valida que la Etapa pertenezca
  al Proyecto y a la misma Organización.
- Las relaciones entre Personas, Cotizaciones y Proyectos se validan dentro del mismo
  tenant.
- `person_relationships` referencia a cada Persona mediante
  (`organization_id`, `person_id`), por lo que MariaDB rechaza relaciones cruzadas
  incluso si una escritura evita la capa de Laravel.
- `person_relationships` impide que una Persona se relacione consigo misma.
- No se aceptan IDs de tenant desde formularios como autoridad.

## Eliminación e historial

- Cotizaciones no se eliminan desde la UI; la copia de cada versión aprobada se conserva.
- ActivityEntry admite edición y soft delete con auditoría de cambios.
- Persona, Proyecto, Acción y File pueden archivarse con soft delete cuando la regla
  funcional lo permita.
- Revocar un portal o cancelar un elemento conserva el historial.
- Ediciones y borrados de Bitácora conservan actor, fecha y valores anteriores.

## Cálculos

- Valores monetarios: decimal y cálculo en servidor.
- `overdue` es derivado, no estado persistido.
- Avance de Etapa: acciones completadas entre acciones no canceladas.
- Avance de Proyecto: suma ponderada por el peso de cada Etapa.
- Antes de iniciar un Proyecto, los pesos activos deben sumar 100.

## Índices mínimos

- Todas las foreign keys.
- (`organization_id`, `status`) en tablas operativas.
- (`organization_id`, `created_at`) para listados recientes.
- (`organization_id`, `tax_id`) único en Personas cuando RFC exista.
- (`organization_id`, `primary_email`) y (`organization_id`, `primary_phone`) para
  búsqueda y advertencias de duplicado.
- (`organization_id`, `number`) en Cotizaciones.
- (`organization_id`, `code`) en Proyectos.
- (`project_id`, `position`) en Etapas.
- (`responsible_organization_member_id`, `due_on`, `status`) en Acciones.
- (`project_id`, `occurred_at`) en Bitácora.

## Datos y tiempo

- BIGINT para IDs.
- UTC para timestamps.
- Zona horaria, locale y moneda predeterminada en Organización.
- Fechas sin hora como `date`.
- JSON únicamente para configuración y metadatos variables, no para relaciones.
