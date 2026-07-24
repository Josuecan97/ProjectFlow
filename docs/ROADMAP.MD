# ProjectFlow — Roadmap

> Estado: Aprobado
> Última actualización: 2026-07-24

## MVP

1. Sprint 0 — completado: plataforma, autenticación, Organizaciones, equipo,
   invitaciones, roles, permisos, membresía comercial, configuración base y Dashboard
   inicial de infraestructura.
2. Sprint 1 — aprobado y congelado: Personas, roles comerciales, contactos,
   restricciones multitenant en base de datos y análisis estático.
3. Sprint 2: Cotizaciones.
4. Sprint 3: Proyectos y participantes.
5. Sprint 4: Etapas y Acciones.
6. Sprint 5: Bitácora y Archivos.
7. Sprint 6: Portal y Automatizaciones básicas.
8. Sprint 7: Dashboard de negocio y estabilización del MVP.

El antiguo Sprint 8 fue absorbido por el Sprint 7 después de adelantar Personas al
Sprint 1. Su documento se conserva únicamente como registro histórico.

El MVP se considera terminado únicamente cuando cumple los criterios de salida de
`MVP_TECHNICAL_SPEC.md`.

Todos los Sprints posteriores al Sprint 1 se desarrollan mediante Trunk-Based
Development, ramas pequeñas y Pull Requests. Cada cierre requiere auditoría, reporte,
aprobación del Product Owner y tag posterior sobre `main`. El proceso completo se
define en `architecture/development-workflow.md`.

## Versión 1

- Reportes avanzados.
- Calendario.
- Notificaciones y automatizaciones configurables más avanzadas.
- Encuestas completas y métricas de satisfacción.

## Versión 2

- Inteligencia artificial.
- API pública.
- Integraciones.
- Marketplace.
- Aplicación móvil.

## Backlog

- Firma electrónica.
- OCR.
- WhatsApp.
- Google Drive y Microsoft 365.
- Integraciones ERP.
- Chat interno.

## Deuda técnica planificada posterior al MVP

- Múltiples teléfonos por Persona.
- Múltiples correos electrónicos por Persona.
- Redes sociales por Persona.
- Selector de contactos con autocompletado paginado.
- Búsqueda Full-Text o motor especializado cuando el volumen lo justifique.
- Formularios compartidos mediante Livewire Form Objects.

Todo elemento nuevo debe justificar el problema, respetar la Biblia y no ampliar el MVP
sin decisión expresa del Product Owner.
