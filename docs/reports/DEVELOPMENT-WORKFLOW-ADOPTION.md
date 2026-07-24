# Adopción del flujo oficial de desarrollo

> Fecha: 2026-07-24  
> Estado: Aprobado  
> Tipo de cambio: Proceso de ingeniería y documentación

## Documentos creados

- `docs/architecture/development-workflow.md`: especificación completa de estrategia
  Git, ramas, Pull Requests, checks, auditorías, releases y Definition of Done.
- `docs/reports/DEVELOPMENT-WORKFLOW-ADOPTION.md`: reporte de adopción.

## Documentos modificados

- `docs/PROJECT_BIBLE.md`: incorpora el flujo oficial y retira una marca accidental.
- `docs/ROADMAP.MD`: establece el proceso aplicable a los siguientes Sprints.
- `docs/DECISIONS.MD`: registra la decisión técnica DT-011.
- `docs/AI_CONTEXT.MD`: comunica el flujo vigente a futuras sesiones.
- `docs/README.md`: enlaza la nueva guía desde el índice oficial.
- `docs/architecture/coding-standards.md`: integra ramas, Pull Requests y tags en los
  estándares obligatorios.

## Flujo adoptado

- Trunk-Based Development.
- `main` como única rama estable.
- Ramas pequeñas y de corta duración.
- Pull Request obligatorio para toda integración.
- Validaciones y auditoría antes del merge.
- Rama `audit/sprint-XX` para el cierre técnico.
- Tags anotados sobre `main` únicamente después del cierre aprobado.
- Sin GitFlow clásico ni rama `develop`.

## Recomendaciones operativas

- Configurar manualmente un Ruleset de GitHub para proteger `main`.
- Incorporar los checks obligatorios a CI antes de iniciar trabajo paralelo.
- Crear una plantilla de Pull Request basada en el checklist oficial cuando se
  autorice modificar la estructura de colaboración de GitHub.
- Activar una aprobación humana obligatoria al incorporar un segundo desarrollador.
- Medir el tamaño y duración de los Pull Requests para evitar ramas extensas.

## Confirmación de alcance

No se crearon ramas ni tags, no se modificó la configuración de GitHub, no se alteró
código funcional y no se inició el Sprint 2.
