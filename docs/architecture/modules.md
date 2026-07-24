# Modules

> Estado: Activo
> Última actualización: 2026-07-24

Los módulos son fronteras organizativas dentro de un solo despliegue Laravel. No son
paquetes Composer ni microservicios.

Dependencias permitidas:

- Dashboard puede consultar todos los dominios.
- Portal consume proyecciones autorizadas de Projects, ActivityLog y Files.
- Automations escucha Events y no modifica directamente componentes Livewire.
- Actions depende de Stages y Projects.
- Stages depende de Projects.
- Projects puede originarse en Quotes y relacionarse con People.
- Quotes depende de People para identificar su contraparte.
- People depende únicamente del contexto de Organizations y no de Quotes o Projects.
- Todos los dominios de negocio dependen del contexto de Organizations.

Se evitan dependencias circulares. Cuando dos módulos deban reaccionar, se utilizará un
evento o una Action orquestadora ubicada en el dominio dueño del caso de uso.
