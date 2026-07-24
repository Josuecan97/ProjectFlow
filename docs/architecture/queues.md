# Queues

> Estado: Propuesta para aprobación
> Última actualización: 2026-07-23

Se enviarán a cola correos, notificaciones, PDF, procesamiento de imágenes y efectos
externos de automatizaciones. Las mutaciones necesarias para confirmar el caso de uso
permanecen en la transacción síncrona.

Todo Job incluirá `organization_id`, datos serializables mínimos, timeout, intentos
limitados y una estrategia de idempotencia. Se despachará después del commit.

El entorno local podrá usar driver `sync` o `database`. Redis/Horizon se habilitará
cuando el despliegue lo requiera sin cambiar contratos de Jobs.
