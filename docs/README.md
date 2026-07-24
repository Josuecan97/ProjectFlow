# ProjectFlow Documentation

> Estado: Activo
> Última actualización: 2026-07-24

## Objetivo

Servir como índice principal de la documentación oficial de ProjectFlow.

## Propósito

Este documento ayuda a navegar la carpeta `docs/` y orienta a las IA y al equipo sobre qué documentos consultar antes de proponer o implementar cambios.

## Documentos base

- [Project Bible](PROJECT_BIBLE.md)
- [MVP Technical Specification](MVP_TECHNICAL_SPEC.md)
- [AI Context](AI_CONTEXT.MD)
- [Architecture](ARCHITECTURE.MD)
- [Database](DATABASE.MD)
- [Decisions](DECISIONS.MD)
- [Roadmap](ROADMAP.MD)

## Secciones

- [Modules](modules/)
- [Architecture](architecture/)
- [Database](database/)
- [API](api/)
- [UI](ui/)
- [Sprints](sprints/)
- [ADR](adr/)

## Uso recomendado

Antes de implementar cualquier funcionalidad:

1. Leer [AI Context](AI_CONTEXT.MD).
2. Leer [Project Bible](PROJECT_BIBLE.md).
3. Leer [MVP Technical Specification](MVP_TECHNICAL_SPEC.md).
4. Leer los documentos relacionados con el módulo o área afectada.
5. Revisar [Decisions](DECISIONS.MD) y los ADR relacionados.
6. Confirmar que el cambio esté alineado con [Roadmap](ROADMAP.MD).
7. Mantener la documentación sincronizada con el código.

## Autoridad documental

`PROJECT_BIBLE.md` contiene las decisiones funcionales. `MVP_TECHNICAL_SPEC.md`
contiene la especificación aprobada para los Sprints 0 y 1; las secciones posteriores
siguen sujetas a sus ADR. Los ADR registran decisiones individuales y sus consecuencias.
