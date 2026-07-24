# ProjectFlow — Cierre final del Sprint 1

> Fecha: 2026-07-24  
> Estado: Aprobado, estable y congelado

## Mejoras posteriores a la auditoría

### Integridad multitenant en MariaDB

La migración
`2026_07_24_000002_harden_person_relationship_tenant_integrity.php` agrega:

- clave única de referencia (`organization_id`, `id`) en `people`;
- foreign key compuesta para la Persona principal de una relación;
- foreign key compuesta para la Persona relacionada;
- restricción que impide autorrelaciones;
- implementación equivalente mediante triggers durante pruebas SQLite.

La migración se aplicó, revirtió y reaplicó correctamente sobre MariaDB. Las pruebas
automatizadas también verifican que la base rechace escrituras directas cruzadas y
autorrelaciones.

### Larastan/PHPStan

- Larastan 3.10 y PHPStan 2.2 incorporados como dependencias de desarrollo.
- Configuración oficial en `phpstan.neon`.
- Nivel inicial 5 sobre app, bootstrap, config, database y routes.
- Comando normalizado: `composer analyse`.
- Cero errores y sin baseline o supresiones masivas.
- Se corrigieron tipos de relaciones Eloquent, enums y fechas detectados por el
  análisis.

## Deuda técnica pospuesta

Queda fuera del MVP actual:

- múltiples teléfonos;
- múltiples correos;
- redes sociales;
- selector paginado de contactos;
- búsqueda Full-Text;
- Livewire Form Objects compartidos.

Estas tareas están registradas en ROADMAP.md y no amplían el Sprint 1.

## Validación

- Composer validate e instalación desde lock: correctos.
- Migración MariaDB y rollback: correctos.
- Laravel Pint: correcto.
- Larastan/PHPStan nivel 5: cero errores.
- Pest/Laravel: 59 pruebas, 59 correctas, 178 aserciones.
- Build Vite: correcto.

## Estado final

El módulo Personas queda estable y congelado. No se inició el Sprint 2. Cualquier
nueva funcionalidad requiere una decisión oficial y la aprobación para comenzar el
siguiente Sprint.
