# ProjectFlow — Reporte de entrega del Sprint 1

> Fecha: 2026-07-24
> Estado: Completado

## 1. Resumen de implementación

Sprint 1 implementa el módulo Personas como expediente único para individuos y
entidades relacionados con una Organización. No existen modelos separados para
clientes, prospectos, proveedores, socios ni contactos.

El módulo permite:

- registrar Personas físicas y morales;
- asignar múltiples roles comerciales;
- listar, buscar y filtrar por tipo, rol y estado;
- consultar y editar el expediente;
- archivar y restaurar conservando relaciones;
- relacionar contactos como otras Personas;
- definir un único contacto principal;
- advertir coincidencias por correo o teléfono;
- impedir RFC duplicado dentro de una Organización;
- respetar permisos, tenant y modo de solo lectura.

## 2. Archivos creados y modificados

Principales áreas creadas:

- `app/Domain/People/Actions`: creación, actualización, archivo, restauración y
  administración de contactos.
- `app/Domain/People/Enums`: tipos, estados, roles y relaciones.
- `app/Domain/People/Models`: Person, PersonRole y PersonRelationship.
- `app/Domain/People/Policies`: autorización del módulo.
- `app/Domain/People/Queries`: listado filtrado y paginado.
- `app/Domain/People/Services`: normalización y detección de duplicados.
- `app/Http/Requests/People`: validaciones reutilizables.
- `resources/views/pages/people`: listado, alta, edición y expediente.
- `resources/views/components/people`: campos compartidos de formularios.
- `tests/Feature/People`: pruebas de modelo, Actions, validaciones, contactos e interfaz.

También se actualizaron rutas, navegación, Dashboard, Organization, AppServiceProvider,
seeders y documentación oficial.

## 3. Migraciones

Se creó `2026_07_24_000001_create_people_tables.php` con:

- `people`;
- `person_roles`;
- `person_role`;
- `person_relationships`.

Incluye foreign keys, índices por tenant, soft delete, RFC único por Organización,
pivote de roles y restricción de base de datos para un solo contacto principal.

La migración se verificó desde una base vacía sobre MariaDB y SQLite de pruebas.

## 4. Relaciones del modelo

- Organization `hasMany` Person.
- Person `belongsTo` Organization.
- Person `belongsToMany` PersonRole.
- Person `hasMany` PersonRelationship como organización relacionada.
- Person `hasMany` PersonRelationship como contacto de otra Persona.
- PersonRelationship `belongsTo` Persona principal y Persona relacionada.

Las relaciones archivadas continúan disponibles mediante `withTrashed`.

## 5. Componentes creados

- `people.index`: listado, búsqueda, filtros, archivo y restauración.
- `people.create`: alta de Persona física o moral.
- `people.edit`: edición y sincronización de roles.
- `people.show`: expediente, roles, información, dirección y contactos.
- `components.people.form-fields`: formulario compartido con validación visible.

## 6. Validaciones ejecutadas

- Composer validate, install y dump-autoload.
- Limpieza completa de cachés de Laravel.
- `php artisan route:list`.
- `php artisan about`.
- `php artisan migrate:fresh --seed --force` en MariaDB.
- Laravel Pint.
- Suite Pest/Laravel.
- Build de Vite.
- Auditoría de Composer: cero vulnerabilidades.
- Auditoría de npm: cero vulnerabilidades.

PHPStan no está instalado en el proyecto y por ello no se ejecutó.

## 7. Pruebas realizadas

La suite contiene 59 pruebas y terminó correctamente. Sprint 1 cubre:

- Personas físicas y morales;
- múltiples roles comerciales;
- normalización de RFC, correo y teléfono;
- RFC único por tenant;
- alta, edición, archivo y restauración;
- búsqueda y filtros;
- advertencias de duplicado;
- contactos, actualización y contacto principal único;
- rechazo de autorrelaciones y relaciones cruzadas;
- permisos por rol;
- intentos de lectura y escritura entre Organizaciones;
- modo de solo lectura por membresía vencida;
- renderizado y operaciones de componentes Volt.
- reconstrucción persistente del contexto de Organización en peticiones Livewire.

## 8. Riesgos detectados

- El navegador integrado no estuvo disponible para la revisión visual final. Las
  pantallas quedaron cubiertas por pruebas HTTP y Volt, pero conviene realizar una
  inspección visual breve cuando vuelva a estar disponible.
- Sprint 1 almacena correo y teléfono principales. Múltiples medios de contacto podrán
  añadirse posteriormente sin dividir la entidad Persona.
- Archivos, Bitácora e historial comercial aparecerán en el expediente durante sus
  respectivos Sprints.
- No existe API pública de Personas en el MVP actual.

## 9. Recomendaciones para Sprint 2

- Iniciar Cotizaciones únicamente después de aprobación del Product Owner.
- Mantener Person como contraparte obligatoria de cada Cotización.
- Congelar versiones aprobadas y conservar trazabilidad monetaria.
- Diseñar numeración única por Organización.
- Repetir desde el primer bloque pruebas de permisos, tenant y solo lectura.
- Definir el alcance exacto del PDF básico antes de implementar su presentación.

No se inició trabajo de Sprint 2.

La revisión senior posterior al Sprint se encuentra en
`docs/reports/PEOPLE-MODULE-AUDIT-2026-07-24.md`.
