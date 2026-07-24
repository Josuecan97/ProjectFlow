# ProjectFlow — Especificación Técnica del MVP

> Estado: Implementado para Sprints 0 y 1; Cotizaciones aprobadas para Sprint 2
> Última actualización: 2026-07-23
> Autoridad funcional: `PROJECT_BIBLE.md`

## 1. Propósito y alcance

Este documento convierte las reglas funcionales de ProjectFlow en una especificación
técnica ejecutable. No sustituye a `PROJECT_BIBLE.md`: ante una contradicción,
prevalece la Biblia y se registra un ADR antes de programar.

El MVP incluye autenticación, organizaciones, usuarios, roles y permisos, personas,
cotizaciones, proyectos, participantes, etapas, acciones, bitácora, archivos, portal
del cliente, automatizaciones básicas y dashboard.

Quedan fuera del MVP la inteligencia artificial, API pública, marketplace, aplicación
móvil, integraciones externas, facturación, contabilidad, inventarios y un constructor
avanzado de automatizaciones.

## 2. Stack oficial propuesto

- PHP 8.4 o superior compatible.
- Laravel 13.
- Livewire 4, Volt 1 y Flux UI 2, compatibles con Laravel 13.
- Tailwind CSS 4 y Vite 8.
- MariaDB como motor oficial.
- Pest para pruebas.

Redis, Horizon, Reverb y S3 se prepararán mediante configuración e interfaces, pero no
serán requisitos para ejecutar localmente el primer MVP. Las versiones exactas quedarán
fijadas en los archivos lock al iniciar la implementación.

## 3. Arquitectura

Se utilizará un monolito modular basado en convenciones de Laravel:

```text
app/
  Domain/
    Organizations/
    Users/
    People/
    Quotes/
    Projects/
    Stages/
    Actions/
    ActivityLog/
    Files/
    ClientPortal/
    Automations/
    Dashboard/
  Http/
  Livewire/
  Policies/
  Providers/
```

Cada dominio podrá contener únicamente lo necesario: `Actions`, `Enums`, `Events`,
`Jobs`, `Listeners`, `Models`, `Policies`, `Queries`, `Rules` y `Services`.

Reglas arquitectónicas:

- Los modelos representan persistencia y relaciones.
- Las operaciones de negocio con varios cambios se ejecutan mediante clases Action.
- Livewire coordina la interfaz, pero no contiene reglas de negocio.
- Las Policies autorizan toda operación sobre recursos.
- Los Events describen hechos confirmados.
- Listeners y Jobs ejecutan efectos secundarios después del commit.
- No se crearán repositorios genéricos encima de Eloquent.
- No se crearán abstracciones sin una frontera o segunda implementación real.

## 4. Multitenancy

El MVP utilizará una aplicación y una base de datos compartida. Toda tabla de negocio
tendrá `organization_id`.

El aislamiento se aplicará mediante:

1. Relación activa del usuario como miembro interno de la organización.
2. Contexto de organización resuelto por middleware.
3. Scopes de Eloquent para consultas normales.
4. Policies como segunda barrera.
5. Foreign keys e índices que incluyan `organization_id` cuando sea necesario.
6. Pruebas obligatorias de fuga cruzada entre organizaciones.

Un usuario podrá pertenecer a varias organizaciones. La organización activa se guardará
en sesión y nunca se aceptará desde un formulario como fuente confiable. Jobs, comandos
y listeners recibirán explícitamente el `organization_id`; no dependerán de la sesión.

No habrá bypass global del tenant en pantallas normales. Cualquier herramienta futura
de soporte global tendrá rutas, permisos y auditoría independientes.

## 5. Usuarios, roles y permisos

`User` representa una identidad global. `OrganizationMember` representa su relación
interna con una organización. `OrganizationSubscription` representa, de forma separada,
la membresía comercial de la Organización dentro de ProjectFlow.

Roles iniciales:

- Propietario: control total y gestión de la organización.
- Administrador: operación completa excepto transferencia de propiedad.
- Gerente de proyectos: administra proyectos y operación relacionada.
- Colaborador: trabaja en proyectos y acciones asignadas.
- Consulta: acceso interno de solo lectura.

Estos cinco roles pertenecen exclusivamente al equipo interno de la Organización.
El Cliente no es un OrganizationMember ni recibe un rol interno durante el MVP: es una Persona
con rol comercial `client`, participa en uno o varios Proyectos y consulta únicamente
la información compartida mediante el Portal privado. Una futura cuenta autenticada de
cliente se modelará como acceso de Portal, no como Colaborador o Consulta interno.

Permisos mínimos:

- `organization.manage`
- `members.view`, `members.invite`, `members.update`, `members.remove`
- `roles.view`, `roles.manage`
- `people.view`, `people.create`, `people.update`, `people.archive`
- `quotes.view`, `quotes.create`, `quotes.update`, `quotes.approve`, `quotes.archive`
- `projects.view`, `projects.create`, `projects.update`, `projects.change_status`
- `stages.manage`
- `actions.view`, `actions.create`, `actions.update`, `actions.complete`
- `activity.view_internal`, `activity.create`, `activity.update`, `activity.delete`
- `activity.restore`, `activity.share`
- `files.view`, `files.upload`, `files.archive`, `files.share`
- `portal.manage`, `automations.manage`, `dashboard.view`

Los permisos se asignan a roles, nunca directamente a usuarios. Un miembro interno podrá
tener varios roles. Debe existir exactamente un Propietario activo por organización.

## 6. Convenciones de datos

- Claves primarias BIGINT.
- Importes con `decimal`, nunca `float`.
- Fechas de negocio como `date`.
- Instantes almacenados en UTC y presentados en la zona horaria de la organización.
- Estados mediante Enums respaldados por strings.
- Foreign keys e índices explícitos.
- Soft deletes únicamente donde exista recuperación funcional.
- Los borrados recuperables utilizan soft delete cuando la regla lo requiera.

## 7. Modelo de datos

### 7.1 Identidad y organizaciones

`organizations`

- `id`, `name`, `legal_name`, `tax_id`, `email`, `phone`
- `timezone`, `locale`, `currency`, `logo_path`, `settings`
- timestamps

`users`

- `id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`
- email único global y timestamps

`organization_members`

- `id`, `organization_id`, `user_id`, `status`
- `invited_by`, `joined_at`, timestamps
- unique (`organization_id`, `user_id`)

`roles`

- `id`, `organization_id` nullable para roles del sistema
- `name`, `code`, `is_system`, timestamps

`permissions`, `organization_member_role` y `permission_role`

- permisos con `code` único
- pivotes con claves únicas compuestas

`organization_subscriptions`

- `id`, `organization_id`
- `status`: trial, active, past_due, suspended, cancelled, expired
- `source`: system, manual, payment
- `starts_at`, `ends_at`, `auto_renew`
- `granted_by_user_id` nullable, `provider` nullable
- `provider_customer_id` nullable, `provider_subscription_id` nullable
- `last_payment_at` nullable, `notes` nullable, timestamps

`organization_subscription_events`

- `id`, `organization_subscription_id`
- `type`: created, activated, renewed, suspended, cancelled, expired
- `actor_user_id` nullable, `metadata` JSON nullable, `occurred_at`

Al crear una Organización se genera automáticamente una suscripción `trial` de 14 días,
con acceso completo a las funciones disponibles. Al expirar, la Organización entra en
modo de solo lectura. Una suscripción manual activa o un pago confirmado en el futuro
pueden reactivar la escritura. Los planes, precios y proveedor de pagos se definirán
posteriormente sin cambiar este ciclo de vida.

### 7.2 Personas

`people`

- `id`, `organization_id`
- `type`: individual, organization
- `display_name`, `legal_name`, `first_name`, `last_name`
- `tax_id`, `curp`, `primary_email`, `primary_phone`, `website`
- `address` JSON, `notes`, `status`: active/archived
- timestamps y `deleted_at`

Persona física requiere nombre; Persona moral requiere razón social. `tax_id` será
único por organización cuando exista.

`person_roles` y `person_role`

- Catálogo: prospect, client, supplier, partner, contact, external_collaborator.
- Una Persona puede tener varios roles.

`person_relationships`

- `organization_id`, `parent_person_id`, `related_person_id`
- `type`: contact, `job_title`, `is_primary`, `notes`

Un contacto también es una Persona; no se crea otra entidad.

### 7.3 Cotizaciones

`quotes`

- `id`, `organization_id`, `person_id`
- `number`, `status`
- `current_version_id`, `approved_version_id`
- `approved_at`, `approved_by_organization_member_id`
- timestamps y unique (`organization_id`, `number`)

Estados: draft, sent, approved, rejected, expired, archived.

`quote_versions`

- `id`, `organization_id`, `quote_id`, `version_number`, `status`
- `title`, `description`, `scope`, `terms`, `notes`
- `issued_on`, `expires_on`, `currency`
- instantánea administrativa: `client_name`, `contact_name`, `contact_email`,
  `contact_phone`, `client_address`
- `subtotal`, `discount_total`, `tax_total`, `total`
- `created_by_organization_member_id`, `approved_at`
- timestamps y unique (`quote_id`, `version_number`)

Estados de versión: draft, sent, approved, rejected y expired. `archived` pertenece a
la Cotización agregada.

`quote_items`

- `id`, `organization_id`, `quote_version_id`, `position`, `name`, `description`
- `quantity`, `unit`, `unit_price`, `discount_amount`, `tax_rate`
- `subtotal`, `tax_amount`, `total`, timestamps

`quote_version_revisions`

- `id`, `organization_id`, `quote_version_id`
- `changed_by_organization_member_id`, `type`: administrative_correction
- `before_values`, `after_values` JSON, `created_at`

`quote_sequences`

- `organization_id` como clave única
- `last_number`, timestamps

El folio usa `COT-000001` y se obtiene bloqueando transaccionalmente la secuencia de la
Organización. No se calcula mediante `MAX`.

Los totales se calculan en servidor con decimal:

```text
base = quantity × unit_price
subtotal = base − discount_amount
tax_amount = subtotal × tax_rate / 100
total = subtotal + tax_amount
```

El descuento no puede superar la base. Cantidades usan hasta cuatro decimales, tasas
hasta cuatro y los importes calculados conservan seis decimales. Las operaciones no
usan `float`; el redondeo visible a dos decimales utiliza half-up y no modifica el
valor interno.

La misma versión se edita mientras esté draft. Aprobarla congela sus campos
comerciales. Una corrección administrativa permitida actualiza únicamente datos de
presentación y siempre crea QuoteVersionRevision. Cambiar conceptos, cantidades,
precios, descuentos, impuestos, moneda, condiciones, alcance o vigencia crea una nueva
versión draft a partir de la aprobada. `approved_version_id` conserva la última versión
aceptada y `current_version_id` la versión de trabajo.

Toda Cotización pertenece a una Persona activa del mismo tenant con rol `client`. El
rol puede agregarse solo mediante confirmación explícita. La aprobación registra al
OrganizationMember. La Cotización no se elimina y puede originar varios Proyectos.

Una Cotización activa en draft o sent cambia idempotentemente a expired cuando su
versión vigente supera `expires_on` sin aprobación.

### 7.4 Proyectos y participantes

`projects`

- `id`, `organization_id`, `quote_id`, `primary_person_id`
- `owner_organization_member_id`, `code`, `name`, `description`
- `status`, `priority`, `health`
- `starts_on`, `expected_end_on`, `completed_on`, `delivered_on`
- `budget`, `pause_reason`, timestamps y `deleted_at`
- unique (`organization_id`, `code`)

Estados: draft, pending, in_progress, paused, completed, delivered, cancelled.
Prioridades: low, normal, high, critical. Salud: excellent, stable, at_risk, critical.

`project_participants`

- `organization_id`, `project_id`, `person_id`
- `role`: client, sponsor, supplier, external_contact, other
- `is_primary`, `notes`, timestamps

Responsables internos se relacionan mediante OrganizationMember; participantes de negocio
mediante Persona.

### 7.5 Etapas

`stages`

- `organization_id`, `project_id`, `responsible_organization_member_id`
- `name`, `description`, `position`, `weight`, `status`
- fechas estimadas y reales, timestamps

Estados: pending, in_progress, paused, completed, cancelled.

Reglas:

- Peso mayor a 0 y menor o igual a 100.
- Para iniciar el Proyecto, sus Etapas activas deben sumar 100.
- Una Etapa cancelada no participa; antes se redistribuyen sus pesos.
- Avance de Etapa = acciones no canceladas completadas / acciones no canceladas.
- Sin acciones: 0 % si no está completada y 100 % al completarla.
- Avance del Proyecto = suma de `peso × avance_etapa / 100`.

### 7.6 Acciones

`actions`

- `organization_id`, `project_id`, `stage_id`, `responsible_organization_member_id`
- `title`, `description`, `status`, `priority`
- fechas estimadas y reales
- `estimated_minutes`, `spent_minutes`, timestamps y `deleted_at`

Estados: pending, in_progress, waiting, completed, cancelled. `overdue` se calcula
cuando la fecha límite pasó y la Acción no está completada ni cancelada; no se almacena
como estado.

`action_dependencies`

- `action_id`, `depends_on_action_id`, unique compuesto

Acción, Etapa y Proyecto deben pertenecer a la misma organización; la Etapa debe
pertenecer al Proyecto. No se permiten ciclos. Completar una Acción genera Bitácora.

### 7.7 Bitácora

`activity_entries`

- `organization_id`, `project_id`, `type`, `title`, `description`
- `visibility`: internal/shared
- `source_type`, `source_id`, `author_organization_member_id`
- `occurred_at`, `metadata`, timestamps
- `deleted_at`

`activity_entry_revisions`

- `activity_entry_id`, `changed_by_organization_member_id`
- `action`: updated, deleted, restored
- `before_values` JSON, `after_values` JSON
- `created_at`

“Actividad”, “registro de actividad” y “entrada de Bitácora” se implementan como
`ActivityEntry`. La Bitácora funcional no sustituye logs técnicos. Las entradas pueden
crearse, editarse y borrarse según permisos. El borrado será recuperable y cada edición
o borrado conservará auditoría de autor, fecha y valores anteriores.

### 7.8 Archivos

`files`

- `organization_id`, `project_id` nullable, `uploaded_by`
- `disk`, `path`, `original_name`, `mime_type`, `size`, `checksum`
- `category`, `visibility`, `status`, timestamps y `deleted_at`

`file_attachments`

- `file_id`, `attachable_type`, `attachable_id`, timestamps

El archivo físico usa un nombre generado, se valida extensión/MIME/tamaño y las
descargas siempre pasan por autorización. Se admitirán adjuntos a Persona, Cotización,
Proyecto, Acción y Bitácora.

### 7.9 Portal del Cliente

`project_portals`

- `organization_id`, `project_id`, `token_hash`
- `enabled_at`, `expires_at`, `revoked_at`, `last_accessed_at`
- `created_by`, timestamps

`portal_access_logs`

- `project_portal_id`, `accessed_at`, `ip_hash`, `user_agent`, `result`

Seguridad:

- Token con entropía criptográfica; solo se almacena su hash.
- Se muestra una vez, puede rotarse y revocarse.
- El portal consulta exclusivamente recursos `shared`.
- No expone usuarios internos, notas, costos o archivos internos.
- Rate limiting, cabecera `noindex` y respuesta indistinguible ante token inválido.

### 7.10 Automatizaciones

`automation_settings`

- `organization_id`, `event`, `action`, `enabled`, `configuration`
- unique (`organization_id`, `event`, `action`)

El MVP ofrece un catálogo seguro, no un constructor libre:

- Acción asignada → notificar responsable.
- Acción próxima a vencer → notificar responsable.
- Acción vencida → notificar responsable y gerente.
- Acción completada → crear Bitácora.
- Etapa completada → crear Bitácora.
- Cambio de estado del Proyecto → crear Bitácora y notificación opcional.
- Proyecto entregado → preparar envío de encuesta.

Los efectos externos serán Jobs idempotentes y reintentables.

## 8. Estados, transacciones y eventos

Los estados se implementarán con Enums y transiciones explícitas:

- Cotización: draft → sent → approved/rejected; draft o sent → expired. Puede
  archivarse; un cambio comercial posterior a aprobación crea una versión draft sin
  modificar la aprobada.
- Proyecto: draft → pending → in_progress → completed → delivered.
- Proyecto activo puede pausarse y regresar a in_progress.
- Etapa: pending → in_progress → paused/completed/cancelled.
- Acción: pending → in_progress/waiting → completed/cancelled.

Aprobar cotizaciones, convertir proyectos, completar acciones y cambios relevantes de
estado serán transaccionales. Los eventos se despacharán después del commit.

Eventos iniciales:

- `QuoteApproved`
- `QuoteExpired`
- `ProjectCreated`
- `ProjectStatusChanged`
- `StageCompleted`
- `ActionAssigned`
- `ActionCompleted`
- `FileUploaded`
- `ActivityEntryCreated`
- `PortalEnabled`

## 9. Validación y autorización

- Toda entrada se valida en Form Requests o formularios Livewire equivalentes.
- Toda mutación se autoriza mediante Policy.
- `organization_id`, estados e importes calculados no se confían a la UI.
- Se recalculan totales y porcentajes en servidor.
- Los errores no deben revelar existencia de recursos de otra organización.

## 10. Estrategia de pruebas

Unitarias:

- Totales de Cotización.
- Avance de Etapas y Proyectos.
- Transiciones de estados.
- Vencimientos y ciclos de dependencias.

Feature:

- Autenticación y selección de organización.
- Policies por rol.
- Flujos principales de cada módulo.
- Edición versionada de Cotización aprobada y CRUD autorizado de Bitácora.
- Correcciones administrativas auditadas y expiración de Cotizaciones.
- Conversión en uno o varios Proyectos.
- Portal, expiración, rotación y revocación.
- Automatizaciones e idempotencia.

Cada módulo deberá incluir al menos una prueba que intente consultar y modificar un
recurso de otra organización. Estas pruebas son condición de aceptación.

Antes de integrar:

- `php artisan test` pasa sin skips críticos.
- Pint pasa.
- Las migraciones ejecutan desde cero.
- Jobs, almacenamiento y notificaciones se prueban con fakes cuando corresponda.

## 11. Criterio de terminado

Un módulo está terminado cuando cumple reglas y permisos, tiene restricciones de base
de datos, interfaz usable, eventos relevantes, pruebas de negocio y aislamiento,
documentación actualizada y todas las verificaciones pasan.

## 12. Orden de implementación

1. Sprint 0: plataforma, MariaDB, autenticación, tenancy, equipo, RBAC, membresía
   comercial y Dashboard inicial de infraestructura.
2. Sprint 1: Personas, roles comerciales y contactos.
3. Sprint 2: Cotizaciones, versiones, conceptos, aprobación, auditoría administrativa,
   expiración y PDF básico.
4. Sprint 3: Proyectos, conversión, participantes, estados y salud.
5. Sprint 4: Etapas, pesos, Acciones, dependencias y avance.
6. Sprint 5: Bitácora, adjuntos, visibilidad y archivos.
7. Sprint 6: Portal, automatizaciones predefinidas y notificaciones.
8. Sprint 7: Dashboard de negocio, pruebas integrales y estabilización.

## 13. Criterios de salida del MVP

Una organización debe poder administrar su equipo; registrar Personas; crear, versionar
y aprobar Cotizaciones; convertirlas en Proyectos; planificar Etapas; ejecutar Acciones;
consultar Bitácora; cargar y compartir archivos; habilitar un Portal revocable; usar
automatizaciones básicas; decidir desde el Dashboard y demostrar que no existen fugas
de datos entre organizaciones.

## 14. Decisiones que requieren aprobación

1. Laravel 13 y MariaDB.
2. Base compartida con aislamiento por `organization_id`.
3. Usuario global como miembro interno de varias organizaciones.
4. Múltiples roles por miembro interno y roles iniciales propuestos.
5. Contactos modelados también como Personas.
6. Reemplazado por las decisiones aprobadas para Sprint 2.
7. Fórmula de avance definida aquí.
8. Vencimiento calculado, no almacenado como estado.
9. Bitácora editable y borrable con permisos, soft delete e historial de cambios.
10. Portal con token hasheado, rotación, revocación y expiración opcional.
11. Automatizaciones limitadas a un catálogo durante el MVP.
12. Orden de sprints y criterios de salida.

## 15. Decisiones aprobadas para Sprint 0

- MariaDB es el motor oficial.
- Toda Organización recibe automáticamente una prueba de 14 días.
- La prueba ofrece acceso completo a las funciones disponibles.
- Al vencer la prueba, la Organización conserva acceso de solo lectura.
- `OrganizationMember` y `OrganizationSubscription` son conceptos separados.

## 16. Decisiones aprobadas para Sprint 1

- Sprint 1 implementa oficialmente Personas, roles comerciales y contactos.
- Clientes, prospectos, proveedores, contactos y socios no son entidades separadas.
- Una Persona puede tener varios roles simultáneamente.
- Toda Persona y relación pertenece a una Organización.
- El RFC es único por Organización cuando exista.
- Coincidencias de correo o teléfono generan advertencias de duplicado, no bloqueo.
- Archivar usa soft delete y conserva las relaciones.

## 17. Decisiones aprobadas para Sprint 2

- Estados: draft, sent, approved, rejected, expired y archived.
- Una versión draft se edita sin generar versiones adicionales.
- Los campos comerciales de una versión aprobada son inmutables.
- Una corrección administrativa permitida no crea versión y conserva revisión técnica.
- Un cambio comercial posterior a aprobación crea una nueva versión draft.
- Folio `COT-000001` secuencial, transaccional y único por Organización.
- La Persona asociada debe tener rol Cliente; agregarlo exige confirmación.
- La aprobación pertenece a un OrganizationMember.
- La expiración conserva íntegramente el historial.
- El PDF básico se genera bajo demanda y no se almacena.
