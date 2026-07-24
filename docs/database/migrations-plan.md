# Migrations Plan

> Estado: Aprobado para Sprints 0 y 1
> Última actualización: 2026-07-24

Orden de migraciones:

1. organizations y adaptación de users.
2. organization_members, roles, permissions y pivotes.
3. organization_subscriptions y organization_subscription_events.
4. people, person_roles, person_role, person_relationships — Sprint 1.
5. quotes.
6. quote_versions y quote_items; después FK de approved_version.
7. projects y project_participants.
8. stages.
9. actions y action_dependencies.
10. activity_entries y activity_entry_revisions.
11. files y file_attachments.
12. project_portals y portal_access_logs.
13. automation_settings.

Cada migración tendrá `down()`, foreign keys, índices y restricciones únicas. Las
dependencias circulares se resuelven agregando la FK en una migración posterior.

Verificación obligatoria:

- Migrar desde base vacía.
- Ejecutar seeders mínimos.
- Revertir durante desarrollo.
- Volver a migrar y ejecutar pruebas.

Después del primer despliegue productivo no se editarán migraciones publicadas.
