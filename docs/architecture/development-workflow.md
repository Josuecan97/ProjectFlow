# Flujo de desarrollo

> Estado: Aprobado  
> Vigencia: 2026-07-24  
> Alcance: Todos los Sprints posteriores al Sprint 1

## Objetivo

Mantener `main` estable y liberable mientras ProjectFlow evoluciona mediante cambios
pequeños, revisables y trazables.

ProjectFlow utiliza Trunk-Based Development con ramas de corta duración y Pull
Requests. No utiliza GitFlow clásico ni mantiene una rama `develop`.

## Ramas

### Rama estable

`main` es la única rama estable. Debe permanecer:

- compilable;
- migrable;
- probada;
- documentada;
- lista para liberar.

No recibe desarrollos incompletos ni pushes directos.

### Ramas de trabajo

Cada rama parte de la versión más reciente de `main`, tiene un único objetivo y debe
vivir horas o pocos días. No se crea una rama que contenga un Sprint completo.

Convenciones:

| Prefijo | Uso | Ejemplo |
|---|---|---|
| `feature/` | Bloque funcional aprobado | `feature/s2-quotation-model` |
| `fix/` | Corrección ordinaria | `fix/quotation-rounding` |
| `hotfix/` | Incidente crítico sobre la versión estable | `hotfix/tenant-leak` |
| `audit/` | Auditoría y cierre técnico de Sprint | `audit/sprint-02` |
| `docs/` | Cambio exclusivamente documental | `docs/development-workflow` |

Otros ejemplos válidos para Sprint 2:

- `feature/s2-quotation-ui`
- `feature/s2-quotation-workflow`
- `feature/s2-quotation-validation`
- `feature/s2-quotation-tests`

Una rama no debe mezclar funcionalidades, correcciones o documentación sin relación
directa con su objetivo.

## Ciclo de una rama feature

1. Confirmar que el bloque pertenece al Sprint aprobado.
2. Actualizar `main` y crear la rama desde ella.
3. Implementar un bloque pequeño y completamente funcional.
4. Mantener commits coherentes y comprensibles.
5. Actualizar pruebas y documentación afectada.
6. Ejecutar la validación técnica.
7. Realizar una autoauditoría de código, seguridad, multitenancy y documentación.
8. Publicar la rama y abrir un Pull Request.
9. Resolver comentarios y checks fallidos.
10. Integrar únicamente cuando el Pull Request esté aprobado.
11. Eliminar la rama después del merge.

Se recomienda `squash merge` para que cada Pull Request se convierta en un commit
coherente en `main`. Los commits intermedios continúan disponibles en el Pull Request.

## Pull Requests

Todo cambio se integra mediante Pull Request, incluidos fixes, hotfixes, auditorías y
cambios documentales.

Cada Pull Request debe incluir:

- objetivo y alcance;
- decisión o requisito oficial que implementa;
- archivos o áreas principales afectadas;
- migraciones y estrategia de rollback, cuando aplique;
- riesgos de seguridad y multitenancy;
- pruebas añadidas o modificadas;
- validaciones ejecutadas;
- capturas o evidencia visual cuando cambie la interfaz;
- deuda o riesgos pendientes;
- confirmación de que no incorpora trabajo fuera del Sprint.

El tamaño debe permitir una revisión completa. Si un Pull Request contiene varios
objetivos independientes, debe dividirse.

## Checklist obligatorio de integración

Antes del merge deben terminar correctamente:

- `composer validate --strict`;
- instalación reproducible desde `composer.lock`;
- `./vendor/bin/pint --test`;
- `composer analyse`;
- `php artisan test`;
- `npm run build`;
- comandos de diagnóstico Laravel aplicables;
- migración desde una base limpia y rollback cuando el cambio incluya migraciones;
- revisión de Policies, validación, mass assignment, CSRF y autenticación cuando
  corresponda;
- pruebas de aislamiento entre Organizaciones para cualquier dato tenant-aware;
- revisión de sincronización entre código y documentación;
- auditoría técnica del cambio.

Un check fallido bloquea el merge. No se aceptan baselines o supresiones masivas para
evitar corregir errores analizables.

## Protección recomendada de `main` en GitHub

La configuración debe aplicarse manualmente en GitHub mediante Rulesets o Branch
Protection Rules. Esta documentación no autoriza su aplicación automática.

Reglas recomendadas:

- exigir Pull Request antes del merge;
- bloquear pushes directos;
- exigir los checks oficiales;
- exigir que las conversaciones estén resueltas;
- bloquear force push;
- bloquear eliminación de `main`;
- exigir que la rama esté actualizada cuando resulte necesario;
- exigir al menos una aprobación cuando exista más de un desarrollador;
- limitar excepciones administrativas a incidentes documentados.

Procedimiento orientativo:

1. Abrir la configuración del repositorio en GitHub.
2. Crear un Ruleset para la rama `main`.
3. Activar protección contra eliminación y actualizaciones no lineales.
4. Exigir Pull Request y los checks de CI.
5. Definir aprobaciones según el tamaño actual del equipo.
6. Guardar la regla y verificarla con un Pull Request de prueba no funcional.

Mientras exista un solo desarrollador, los checks y el Pull Request siguen siendo
obligatorios; la revisión humana adicional puede activarse cuando se incorpore otro
colaborador.

## Auditoría y cierre de Sprint

Las ramas feature se integran de forma incremental. Al terminar el alcance:

1. Crear `audit/sprint-XX` desde `main`.
2. Auditar implementación, seguridad, arquitectura, rendimiento, pruebas y
   documentación.
3. Corregir únicamente problemas menores dentro de la rama de auditoría.
4. Informar decisiones que requieran aprobación antes de aplicarlas.
5. Ejecutar nuevamente toda la validación técnica.
6. Actualizar el reporte técnico de cierre.
7. Abrir el Pull Request final de auditoría.
8. Integrarlo únicamente con checks correctos y revisión completada.
9. Solicitar la aprobación formal del Product Owner.
10. Crear el tag oficial solo después del cierre aprobado.
11. No iniciar el Sprint siguiente hasta recibir aprobación explícita.

## Versionado y releases

Los tags representan únicamente versiones aprobadas y reproducibles de `main`.

Reglas:

- usar tags anotados;
- crear el tag sobre el commit aprobado de `main`;
- no crear tags sobre ramas feature;
- no mover, borrar ni reutilizar tags publicados;
- no crear un tag antes de la auditoría y el cierre oficial;
- conservar notas de release vinculadas al reporte del Sprint.

ProjectFlow utiliza versionado semántico durante el MVP. El tag existente `v0.1.0`
representa la base aprobada de los Sprints 0 y 1. La siguiente versión se determina al
cierre del Sprint correspondiente y nunca se reserva mediante un tag anticipado.

Una release debe incluir:

- resumen funcional;
- migraciones y consideraciones de despliegue;
- validaciones ejecutadas;
- incompatibilidades conocidas;
- riesgos pendientes;
- enlace al reporte técnico del Sprint.

## Hotfixes

Un hotfix parte de `main`, contiene exclusivamente la corrección crítica y se integra
por Pull Request con las mismas validaciones. Si una emergencia exige una excepción,
debe documentarse, revisarse inmediatamente después y no desactivar permanentemente
las protecciones.

## Definition of Done de un bloque

Un bloque está terminado cuando:

- cumple el requisito aprobado;
- no contiene funcionalidad incompleta ni código muerto;
- respeta arquitectura, reglas de negocio y multitenancy;
- tiene pruebas proporcionales al riesgo;
- toda validación obligatoria pasa;
- documentación y código están sincronizados;
- la autoauditoría está completada;
- el Pull Request fue revisado e integrado;
- no deja una deuda silenciosa.

## Definition of Done de un Sprint

Un Sprint está terminado únicamente cuando:

- todo su alcance aprobado está integrado en `main`;
- `main` está compilable, migrable, probada y lista para liberar;
- la auditoría completa fue integrada mediante Pull Request;
- los problemas menores fueron corregidos;
- los asuntos que requieren decisión están resueltos o documentados;
- el reporte técnico final está completo;
- código y documentación permanecen sincronizados;
- el Product Owner aprobó formalmente el cierre;
- se creó el tag oficial después de la aprobación;
- no se inició el Sprint siguiente.
