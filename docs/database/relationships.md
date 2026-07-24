# Relationships

> Estado: Aprobado para Sprints 0 y 1
> Última actualización: 2026-07-24

```text
Organization 1─* OrganizationMember *─1 User
OrganizationMember *─* Role *─* Permission
Organization 1─* OrganizationSubscription 1─* OrganizationSubscriptionEvent
Organization 1─* Person *─* PersonRole
Person 1─* PersonRelationship *─1 Person
Person 1─* Quote 1─* QuoteVersion 1─* QuoteItem
Quote 1─* Project
Project *─* Person (ProjectParticipant)
Project 1─* Stage 1─* Action
Action *─* Action (Dependency)
Project 1─* ActivityEntry
File *─* attachable (FileAttachment)
Project 1─* ProjectPortal 1─* PortalAccessLog
Organization 1─* AutomationSetting
```

Reglas esenciales:

- Toda relación de negocio ocurre dentro de la misma Organización.
- PersonRelationship conecta `parent_person_id` con `related_person_id`, rechaza
  autorrelaciones y no cruza Organizaciones. Laravel lo valida antes de escribir y
  MariaDB lo refuerza con claves foráneas compuestas y una restricción CHECK.
- Una Persona moral puede tener varios contactos y solo uno principal.
- Toda Acción requiere Etapa y Proyecto coherentes.
- Cotización aprobada puede originar varios Proyectos.
- Participantes externos son Personas; responsables internos son OrganizationMembers.
- FileAttachment solo acepta tipos permitidos por una lista explícita.
- Solo puede existir un Portal activo por Proyecto.
