# Entities

> Estado: Aprobado para Sprints 0, 1 y 2
> Última actualización: 2026-07-24

Entidades del MVP:

- Organization, User, OrganizationMember, Role, Permission.
- OrganizationSubscription y OrganizationSubscriptionEvent.
- Person, PersonRole, PersonRelationship.
- Quote, QuoteVersion, QuoteItem, QuoteVersionRevision y QuoteSequence.
- Project, ProjectParticipant.
- Stage, Action, ActionDependency.
- ActivityEntry y ActivityEntryRevision.
- File, FileAttachment.
- ProjectPortal, PortalAccessLog.
- AutomationSetting.

`MVP_TECHNICAL_SPEC.md` define atributos, estados y reglas. Los catálogos estables se
representan mediante Enums o seeders; no se crearán tablas para valores sin metadatos.
