# ADR-002: Etapas, Acciones y cálculo de avance

> Estado: Propuesta para aprobación
> Fecha: 2026-07-23

## Contexto

ProjectFlow necesita separar planeación de alto nivel y trabajo pendiente, y calcular
el avance sin captura manual.

## Decisión

Todo Proyecto tendrá Etapas ponderadas y toda Acción pertenecerá obligatoriamente a una
Etapa. Las Etapas activas sumarán 100 % antes de iniciar el Proyecto.

Avance de Etapa = acciones no canceladas completadas / acciones no canceladas.

Avance de Proyecto = suma de `peso × avance_etapa / 100`.

Una Etapa sin acciones vale 0 % hasta que se complete explícitamente, momento en el que
vale 100 %. `overdue` se calcula y no se almacena como estado.

## Alternativas descartadas

- Avance manual: subjetivo y fácil de desactualizar.
- Hitos: no representan fases completas.
- Acciones independientes: rompen la estructura definida en la Biblia.
- Guardar `overdue`: genera inconsistencias con el paso del tiempo.

## Consecuencias

- Deben validarse pesos antes de iniciar.
- Cancelar una Etapa exige redistribuir pesos.
- Completar/cancelar Acciones recalcula el avance.
- Las acciones canceladas no penalizan el cálculo.
