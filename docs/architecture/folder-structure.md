# Folder Structure

> Estado: Activo
> Última actualización: 2026-07-24

```text
app/
  Domain/{Domain}/
    Actions/
    Enums/
    Events/
    Jobs/
    Listeners/
    Models/
    Policies/
    Queries/
    Rules/
    Services/
  Http/
  Providers/
database/
  factories/
  migrations/
  seeders/
docs/
resources/
  css/
  js/
  views/
    pages/              # componentes funcionales Volt
routes/
tests/
  Feature/
  Unit/
```

Solo se crean carpetas cuando tengan contenido. Las pantallas Volt permanecen en
`resources/views/pages` y llaman Actions del dominio. Los modelos se ubican dentro del
dominio, no se duplican en `app/Models`. Los componentes no contienen reglas de negocio.
