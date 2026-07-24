# Naming Conventions

> Estado: Propuesta para aprobación
> Última actualización: 2026-07-23

- Tablas y columnas en inglés, `snake_case`.
- Tablas plurales; modelos singulares.
- Foreign key `{model}_id`.
- Pivotes en singular y orden alfabético cuando Laravel lo permita.
- Timestamps estándar `created_at`, `updated_at`, `deleted_at`.
- Fechas de negocio terminan en `_on`; instantes en `_at`.
- Booleanos comienzan con `is_`, `has_` o `can_`.
- Estados y códigos almacenan strings estables en inglés.
- Índices: `{table}_{columns}_{type}` siguiendo el nombre generado por Laravel salvo
  que exceda el límite del motor.

La interfaz podrá presentar todos los términos en español mediante traducciones.
