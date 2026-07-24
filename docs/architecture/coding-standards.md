# Coding Standards

> Estado: Aprobado
> Última actualización: 2026-07-24

## PHP y Laravel

- `declare(strict_types=1);` en código nuevo de dominio.
- PSR-12 y Laravel Pint.
- Tipos de parámetros y retornos.
- Enums de PHP para estados.
- Nombres técnicos en inglés; textos de interfaz traducibles al español.
- Clases singulares, tablas plurales y foreign keys `{model}_id`.
- Form Requests o validación Livewire equivalente.
- Policies para autorización y Actions para operaciones de negocio.

## Calidad

- No usar `env()` fuera de configuración.
- No consultar globalmente sin contexto de Organización.
- No usar `float` para dinero.
- No incluir reglas de negocio en Blade.
- Evitar N+1 y seleccionar únicamente datos necesarios.
- Una migración no se modifica después de producción; se crea una nueva.

## Commits y revisión

- Cambios pequeños y coherentes.
- Pruebas del comportamiento nuevo o corregido.
- Documentación actualizada cuando cambie una regla.
- Antes de integrar: Pint, pruebas y migración desde cero.
- Larastan/PHPStan nivel 5 debe terminar sin errores mediante `composer analyse`.
- No se aceptan baselines masivos ni supresiones para ocultar errores corregibles.

## Cierre obligatorio de cada Sprint

1. Implementación.
2. Validación técnica, incluyendo Composer, Laravel, Pint, Larastan, pruebas y build.
3. Auditoría completa de código y documentación.
4. Corrección automática de problemas menores.
5. Reporte técnico.
6. Aprobación explícita antes de iniciar el siguiente Sprint.
