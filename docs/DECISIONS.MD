# ProjectFlow - Registro de Decisiones

> Este documento registra todas las decisiones importantes del proyecto.

---

## DA-001

ProjectFlow será un SaaS.

Estado:

✅ Aprobado

---

## DA-002

El sistema NO será un ERP.

Estado:

✅ Aprobado

---

## DA-003

Las Personas representan cualquier actor del sistema.

No existen tablas separadas para clientes o proveedores.

Estado:

✅ Aprobado

---

## DA-004

Las Actividades representan hechos.

Las Acciones representan pendientes.

Estado:

✅ Aprobado

---

## DA-005

Las Etapas representan planificación.

No representan tareas.

Estado:

✅ Aprobado

---

## DA-006

El Portal del Cliente será mediante enlace privado en el MVP.

Estado:

✅ Aprobado

---

## DA-007

Laravel será el framework oficial.

Estado:

✅ Aprobado

---

## DA-008

Livewire será la tecnología principal del frontend.

Estado:

✅ Aprobado

---

## DA-009

El MVP prioriza simplicidad sobre cantidad de funciones.

Estado:

✅ Aprobado

---

## DA-010

Toda funcionalidad futura irá al Backlog.

No modifica el MVP.

Estado:

✅ Aprobado

---

# Cómo registrar nuevas decisiones

Cada decisión debe incluir:

- Código
- Fecha
- Descripción
- Justificación
- Estado

---

# Propuestas técnicas pendientes de aprobación

Estas decisiones no se consideran congeladas hasta la aprobación del Product Owner.

## DT-001

Laravel 13, PHP 8.4+, Livewire/Volt/Flux compatibles y MariaDB serán el stack del MVP.

Estado: ✅ Aprobado e implementado en Sprint 0.

## DT-002

El multitenancy utilizará una base compartida con `organization_id`, contexto de tenant,
scopes, Policies y pruebas de aislamiento.

Estado: ✅ Aprobado e implementado en Sprint 0.

## DT-003

El usuario será global y su acceso se modelará mediante OrganizationMember, Roles y
Permissions.

Estado: ✅ Aprobado e implementado en Sprint 0.

## DT-004

Los contactos de Personas Morales también serán Personas.

Estado: ✅ Aprobado para Sprint 1.

## DT-005

Las Cotizaciones tendrán versiones. Después de aprobar, la Cotización seguirá editable
mediante una nueva versión y se conservará la copia exacta de lo aprobado.

Estado: 🟡 Propuesta.

## DT-006

El avance del Proyecto será la suma ponderada del avance de sus Etapas; el avance de
una Etapa dependerá de sus Acciones no canceladas.

Estado: 🟡 Propuesta.

## DT-007

El Portal almacenará únicamente el hash de un token revocable, rotable y opcionalmente
expirable.

Estado: 🟡 Propuesta.

## DT-008

Las Automatizaciones del MVP estarán limitadas a un catálogo predefinido.

Estado: 🟡 Propuesta.

## DT-009

Miembros internos y membresías comerciales son conceptos diferentes:
`OrganizationMember` y `OrganizationSubscription`.

Toda Organización recibe una prueba automática de 14 días con acceso completo. Al
vencer, queda en modo de solo lectura.

Estado: ✅ Aprobado.

## DT-010

Personas se adelanta oficialmente al Sprint 1. Los Sprints posteriores se recorren una
posición y el Dashboard de negocio con estabilización queda en Sprint 7.

Estado: ✅ Aprobado el 2026-07-24.

## DT-011

ProjectFlow utiliza Trunk-Based Development con `main` como única rama estable,
ramas de trabajo de corta duración y Pull Requests obligatorios. No se utilizará
GitFlow clásico.

La auditoría y los checks técnicos son obligatorios antes del merge. Los tags se crean
únicamente sobre `main` después del cierre aprobado de cada Sprint y la autorización
del Product Owner.

Estado: ✅ Aprobado el 2026-07-24.
