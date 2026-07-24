# Cotizaciones — Contrato de aplicación

> Estado: Aprobado para interfaz web del Sprint 2
> Última actualización: 2026-07-24

## Alcance

Sprint 2 no expone una API pública. Las páginas Volt llaman Actions del dominio y las
rutas web permanecen protegidas por autenticación, verificación de correo, contexto de
Organización, Policies y CSRF.

## Operaciones internas

- listar y consultar Cotizaciones del tenant;
- crear Cotización y primera versión draft;
- editar versión draft;
- confirmar el rol Cliente de la Persona cuando sea necesario;
- enviar, aprobar, rechazar y archivar;
- crear una revisión comercial desde una aprobada;
- aplicar corrección administrativa auditada;
- expirar Cotizaciones vencidas mediante proceso idempotente;
- generar PDF de una versión.

## Autoridad de datos

La aplicación nunca acepta como autoridad:

- `organization_id`;
- folio;
- estado;
- número de versión;
- totales calculados;
- versión aprobada;
- miembro aprobador.

Estos valores se resuelven o calculan en servidor.

## Errores

Los recursos de otra Organización responden como no encontrados. Los errores de
validación se asocian a sus campos y no revelan información de otros tenants.

## API pública

Queda fuera del MVP. Si se aprueba en una versión futura, reutilizará las Actions y
Policies existentes sin duplicar reglas de negocio.
