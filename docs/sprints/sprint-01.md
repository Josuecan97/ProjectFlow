# Sprint 01 — Personas

> Estado: Aprobado y congelado
> Inicio: 2026-07-24
> Finalización: 2026-07-24

## Objetivo

Construir el expediente central de cualquier individuo o entidad con la que una
Organización mantiene una relación, sin duplicar clientes, prospectos, proveedores,
contactos ni socios.

## Alcance

- Personas físicas y morales.
- Roles comerciales múltiples: prospecto, cliente, proveedor, socio, contacto y
  colaborador externo.
- Contactos modelados como Personas relacionadas.
- Listado, búsqueda y filtros por tipo, rol y estado.
- Creación, expediente, edición, archivado y restauración.
- Prevención exacta de duplicados por RFC y advertencias por correo o teléfono.
- Policies, Actions, consultas, validadores, Enums, factories y seeders.
- Aislamiento completo por Organización.
- Bloqueo de mutaciones cuando la membresía comercial esté en solo lectura.

## Modelo funcional

- Una Persona pertenece obligatoriamente a una Organización.
- Una Persona conserva su identidad aunque cambien sus roles.
- Una Persona puede tener varios roles simultáneamente.
- Un contacto es otra Persona; no existe una tabla independiente de contactos.
- Una Persona moral puede relacionar varios contactos y marcar uno como principal.
- El RFC, cuando exista, es único dentro de la Organización.
- Correo y teléfono coincidentes generan advertencia, no un bloqueo automático.
- Archivar conserva identidad, relaciones e historial mediante soft delete.

## Criterios de aceptación

- Una Persona puede ser cliente y proveedor simultáneamente.
- Persona física requiere nombre; Persona moral requiere razón social.
- Un contacto relacionado continúa siendo reutilizable sin duplicarse.
- Búsqueda y filtros operan exclusivamente dentro de la Organización actual.
- Crear, consultar, editar, archivar y restaurar requieren permisos.
- No existe lectura, escritura ni relación cruzada entre Organizaciones.
- Una Organización con membresía vencida conserva consulta, pero no mutaciones.
- Migraciones ejecutan desde cero y todas las pruebas, Pint y build pasan.
- La base de datos impide relaciones entre Personas de distintas Organizaciones
  mediante foreign keys compuestas.
- Larastan/PHPStan nivel 5 termina sin errores.

## Fuera de alcance

Cotizaciones, Proyectos, Etapas, Acciones, Bitácora, archivos del expediente, Portal del
Cliente y API pública.

## Estado posterior al cierre

El módulo Personas se considera estable. Solo admite correcciones críticas, mejoras de
seguridad, problemas importantes de rendimiento o decisiones oficiales del producto.
No se agregan nuevas funcionalidades durante el MVP sin aprobación expresa.
