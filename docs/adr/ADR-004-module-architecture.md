# ADR-004: Monolito modular y multitenancy compartido

> Estado: Propuesta para aprobación
> Fecha: 2026-07-23

## Contexto

El MVP necesita velocidad de entrega, transacciones entre módulos y aislamiento seguro
de organizaciones, sin el costo operativo de microservicios o bases separadas.

## Decisión

Se utilizará un monolito Laravel organizado por dominios bajo `app/Domain`. Todas las
organizaciones compartirán base de datos y las tablas de negocio incluirán
`organization_id`.

Middleware, scopes, Policies, restricciones e integración tests actuarán como capas
complementarias de aislamiento.

## Alternativas descartadas

- Microservicios: complejidad prematura y transacciones distribuidas.
- Una base por organización: operación, migraciones y soporte más costosos para MVP.
- Laravel sin fronteras de dominio: facilita acoplamiento conforme crecen módulos.
- Paquete modular externo: dependencia innecesaria para simples límites de carpetas.

## Consecuencias

- Despliegue y transacciones sencillos.
- Toda consulta de negocio debe tener contexto de Organización.
- Jobs y comandos deben recibir el tenant explícitamente.
- Las pruebas de aislamiento son obligatorias para cada módulo.
- Los dominios pueden extraerse en el futuro solo si existe una necesidad demostrada.
