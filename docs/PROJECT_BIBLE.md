# PROJECTFLOW BIBLE

> **Documento Maestro del Proyecto**
>
> **Versión:** 1.0
>
> **Estado:** Activo
>
> **Tipo:** Living Document
>
> **Proyecto:** ProjectFlow

---

# IMPORTANTE

Este documento representa la especificación funcional, técnica y conceptual más importante de ProjectFlow.

Su propósito es preservar todo el conocimiento del proyecto de forma permanente, evitando que la visión del producto dependa de conversaciones, personas o herramientas específicas.

Toda persona o inteligencia artificial que participe en el proyecto deberá leer este documento antes de modificar el sistema.

Este documento tiene prioridad sobre cualquier conversación mantenida durante el desarrollo del proyecto.

La carpeta `docs/` constituye la fuente oficial de documentación del sistema.

---

# ¿Por qué existe este documento?

Durante el diseño de ProjectFlow se tomaron cientos de decisiones relacionadas con arquitectura, reglas de negocio, experiencia de usuario y filosofía del producto.

Muchas de esas decisiones nacieron después de largas discusiones, correcciones, descartes y múltiples iteraciones.

Este documento no busca resumir esas conversaciones.

Su objetivo es preservar el resultado final de todas ellas.

Cada sección representa decisiones ya tomadas.

Las decisiones aquí documentadas deben considerarse oficiales hasta que exista una nueva decisión formal que las modifique.

---

# Objetivo del proyecto

ProjectFlow nace para resolver un problema muy específico.

Miles de empresas venden servicios mediante proyectos.

Algunas desarrollan software.

Otras implementan infraestructura.

Otras venden consultoría.

Otras realizan instalaciones.

Otras administran campañas de marketing.

Otras ofrecen mantenimiento.

Todas tienen algo en común.

Una vez que el cliente acepta una propuesta comercial, comienza una etapa muy difícil de administrar.

La ejecución.

En la mayoría de empresas esa ejecución termina distribuida entre:

- WhatsApp
- Correos electrónicos
- Llamadas
- Excel
- Documentos compartidos
- Fotografías
- PDF
- Notas personales
- Grupos de trabajo
- Sistemas diferentes

Como consecuencia aparecen problemas comunes:

- El cliente no sabe cómo va su proyecto.
- El equipo pierde información.
- Nadie conoce el último avance.
- Existen múltiples versiones de un mismo documento.
- Las tareas se olvidan.
- Las evidencias se dispersan.
- No existe un historial confiable.
- Se pierde demasiado tiempo preguntando "¿cómo va esto?".

ProjectFlow existe para eliminar ese problema.

Toda la información relacionada con un proyecto debe vivir en un solo lugar.

---

# ¿Qué es ProjectFlow?

ProjectFlow es una plataforma SaaS especializada en la administración de proyectos de servicios.

Su objetivo principal consiste en centralizar todo el ciclo de vida de un proyecto, desde el primer contacto comercial hasta la entrega final, manteniendo sincronizados al equipo interno y al cliente.

ProjectFlow busca convertirse en la fuente oficial de información de cada proyecto.

No pretende reemplazar herramientas contables.

No pretende reemplazar sistemas ERP.

No pretende administrar inventarios.

No pretende sustituir un CRM tradicional.

ProjectFlow administra la ejecución del trabajo.

Ese es su propósito.

Todo lo demás es secundario.

---

# Qué NO es ProjectFlow

Una de las primeras decisiones del proyecto fue definir claramente aquello que nunca intentará convertirse.

ProjectFlow NO es:

- Un ERP.
- Un sistema contable.
- Un software de facturación.
- Un sistema administrativo general.
- Un gestor de inventarios.
- Un CRM tradicional.
- Un software Help Desk.
- Un gestor de tickets.
- Un gestor de tareas genérico.
- Un reemplazo de Trello.
- Un reemplazo de ClickUp.
- Un reemplazo de Monday.

Aunque en el futuro pueda integrarse con cualquiera de estas herramientas, ProjectFlow siempre conservará un enfoque muy específico:

Administrar proyectos de servicios.

Toda funcionalidad futura deberá respetar ese principio.

---

# Visión

ProjectFlow busca convertirse en la plataforma donde una empresa pueda responder cualquier pregunta relacionada con un proyecto en menos de treinta segundos.

Ejemplos:

¿Cómo va el proyecto?

¿Quién es el responsable?

¿Qué ocurrió ayer?

¿Qué falta por hacer?

¿Qué evidencias existen?

¿Cuándo fue la última visita?

¿Qué documentos se entregaron?

¿Qué etapa sigue?

¿Qué aprobó el cliente?

¿Cuál es el estado real del proyecto?

Si ProjectFlow no puede responder esas preguntas de forma clara, entonces el producto no está cumpliendo su propósito.

---

# Filosofía principal

Existe una regla que gobierna absolutamente todo el proyecto.

Todo gira alrededor del Proyecto.

No alrededor del Cliente.

No alrededor de las Tareas.

No alrededor de los Archivos.

No alrededor de los Usuarios.

El Proyecto representa la unidad principal del sistema.

Todo elemento debe poder relacionarse con un proyecto.

Todas las decisiones de arquitectura fueron tomadas respetando esta filosofía.

Cuando exista una duda sobre cómo implementar una funcionalidad, siempre deberá preguntarse:

"¿Esta decisión mejora la administración del proyecto?"

Si la respuesta es no, probablemente dicha funcionalidad no pertenece a ProjectFlow.

---

# El principio de simplicidad

Durante el diseño del producto se tomó una decisión permanente.

ProjectFlow nunca buscará tener la mayor cantidad de funciones.

Buscará tener las funciones correctas.

Cada nueva característica deberá justificar claramente el problema que resuelve.

Las funcionalidades creadas únicamente por "si algún día sirven" quedan prohibidas.

Se prioriza un sistema sencillo, rápido, consistente y fácil de utilizar.

La simplicidad forma parte de la arquitectura del producto.

No es una consecuencia.

Es un objetivo.

---

# Modelo del negocio

Antes de definir cualquier módulo, durante el diseño del producto se tomó una decisión que cambió completamente la arquitectura de ProjectFlow.

Inicialmente el sistema fue concebido como un gestor de proyectos tradicional.

Sin embargo, conforme evolucionó el análisis del negocio quedó claro que el proyecto no comenzaba cuando se creaba un proyecto.

Comenzaba mucho antes.

Comenzaba desde la primera relación con una persona.

A partir de ese momento toda la arquitectura fue rediseñada.

El flujo oficial del negocio quedó definido de la siguiente manera.

Persona

↓

Cotización

↓

Proyecto

↓

Etapas

↓

Acciones

↓

Bitácora

↓

Portal del Cliente

↓

Entrega

↓

Encuesta

Este flujo representa el ciclo de vida oficial de ProjectFlow.

Toda nueva funcionalidad deberá integrarse respetando este modelo.

Si una funcionalidad rompe este flujo deberá justificarse mediante una decisión arquitectónica.

---

# El Proyecto es el centro del sistema

La decisión más importante tomada durante el diseño fue que absolutamente todo debe girar alrededor del Proyecto.

No alrededor de tareas.

No alrededor de clientes.

No alrededor de archivos.

No alrededor de usuarios.

El Proyecto representa la unidad principal del negocio.

Cada módulo existe para aportar información al Proyecto.

Todas las relaciones terminan convergiendo en él.

Cuando exista una duda durante el desarrollo siempre deberá responderse primero la siguiente pregunta:

"¿Cómo aporta esta funcionalidad al proyecto?"

Si no existe una respuesta clara, probablemente esa funcionalidad no pertenece al producto.

---

# Filosofía del ciclo de vida

ProjectFlow no administra únicamente proyectos.

Administra todo el proceso que ocurre antes, durante y después de un proyecto.

Antes:

- Personas
- Cotizaciones

Durante:

- Proyecto
- Etapas
- Acciones
- Bitácora
- Archivos

Después:

- Entrega
- Encuesta
- Historial

Esta visión fue adoptada para evitar fragmentar la información entre diferentes sistemas.

Toda la información relacionada con un trabajo debe permanecer conectada durante todo su ciclo de vida.

Esa continuidad representa uno de los principios fundamentales del producto.

---

# Modelo del Negocio

Durante el diseño de ProjectFlow se tomó una de las decisiones más importantes de todo el proyecto.

Inicialmente el sistema fue concebido como un administrador tradicional de proyectos.

Sin embargo, conforme evolucionó el análisis del negocio quedó claro que un proyecto no comienza cuando alguien crea un registro llamado "Proyecto".

Comienza mucho antes.

Comienza desde el momento en que una empresa establece una relación con una persona y esa relación puede convertirse en una oportunidad de negocio.

A partir de esa conclusión toda la arquitectura del sistema fue rediseñada.

ProjectFlow dejó de ser únicamente un gestor de proyectos y pasó a convertirse en un sistema que administra el ciclo completo de un proyecto de servicios.

Todo el modelo del negocio gira alrededor del siguiente flujo.

```text
Persona
    │
    ▼
Cotización
    │
    ▼
Proyecto
    │
    ▼
Etapas
    │
    ▼
Acciones
    │
    ▼
Bitácora
    │
    ▼
Portal del Cliente
    │
    ▼
Entrega
    │
    ▼
Encuesta
```

Este flujo representa el ciclo de vida oficial de ProjectFlow.

Toda nueva funcionalidad deberá integrarse respetando este modelo.

Si una funcionalidad rompe este flujo deberá justificarse mediante una decisión arquitectónica formal.

---

# El Proyecto como núcleo del sistema

Aunque el flujo inicia con una Persona, el Proyecto representa el núcleo de toda la plataforma.

Toda la información relevante termina relacionada con un proyecto.

Las personas participan en proyectos.

Las cotizaciones generan proyectos.

Las etapas pertenecen a proyectos.

Las acciones pertenecen a proyectos.

La bitácora pertenece a proyectos.

Los archivos pertenecen a proyectos.

El portal existe para mostrar el estado de un proyecto.

Las automatizaciones reaccionan a eventos de un proyecto.

Los reportes analizan proyectos.

ProjectFlow no administra tareas.

ProjectFlow no administra clientes.

ProjectFlow administra proyectos de servicios.

Cuando exista una duda durante el desarrollo siempre deberá responderse primero la siguiente pregunta:

> **¿Esta funcionalidad mejora la administración de un proyecto de servicios?**

Si la respuesta es no, probablemente esa funcionalidad no pertenece a ProjectFlow.

---

# Ciclo de vida del proyecto

El sistema fue diseñado para acompañar un proyecto desde el primer contacto comercial hasta su cierre.

El ciclo de vida oficial es el siguiente:

## Antes del proyecto

- Registro de Personas.
- Seguimiento comercial.
- Generación de Cotizaciones.

## Inicio del proyecto

- Conversión de una Cotización aprobada en Proyecto.
- Asignación de responsables.
- Definición de participantes.
- Creación de etapas.

## Ejecución

- Registro de actividades.
- Administración de acciones.
- Gestión documental.
- Seguimiento del avance.
- Comunicación con el cliente.

## Cierre

- Entrega del proyecto.
- Validación.
- Encuesta de satisfacción.
- Conservación del historial.

Toda la arquitectura del sistema fue diseñada respetando este ciclo de vida.

---

# Módulos principales del sistema

Durante la etapa de diseño se concluyó que ProjectFlow debía dividirse en módulos independientes, pero completamente integrados entre sí.

Cada módulo resuelve un problema específico del negocio, sin perder de vista que todos forman parte del mismo flujo de trabajo.

Los módulos oficiales del MVP son los siguientes.

## Dashboard

El Dashboard no funciona como una pantalla de bienvenida.

Su objetivo es responder rápidamente tres preguntas:

- ¿Qué requiere mi atención?
- ¿Qué ocurrió mientras no estaba?
- ¿Qué debo hacer ahora?

El Dashboard debe convertirse en el centro de decisiones del usuario y no en una simple colección de indicadores.

Debe mostrar únicamente información útil para la operación diaria.

---

## Organización

Representa la empresa propietaria de la cuenta.

Toda la información del sistema pertenece a una Organización.

La organización define:

- Configuración general.
- Datos fiscales.
- Identidad visual.
- Configuración regional.
- Preferencias del sistema.

Toda la información debe quedar aislada por organización.

ProjectFlow fue diseñado desde el inicio para soportar múltiples organizaciones de forma segura.

---

## Personas

Las Personas representan cualquier actor con el que la organización mantiene una relación.

Una Persona puede convertirse en:

- Cliente.
- Prospecto.
- Proveedor.
- Contacto.
- Socio.
- Colaborador externo.

Una Persona nunca cambia de entidad.

Lo único que cambia es el rol que desempeña dentro del negocio.

Esta decisión elimina la necesidad de mantener tablas separadas para Clientes y Proveedores.

---

## Cotizaciones

Las Cotizaciones representan propuestas comerciales.

Una cotización pertenece siempre a una Persona.

Cuando una cotización es aprobada puede convertirse en un Proyecto.

Toda la información comercial debe permanecer disponible incluso después de iniciar el proyecto.

---

## Proyectos

El Proyecto representa el núcleo de todo el sistema.

Todo módulo importante termina relacionado con un Proyecto.

El Proyecto concentra:

- Personas.
- Participantes.
- Etapas.
- Acciones.
- Bitácora.
- Archivos.
- Portal del Cliente.
- Estado.
- Salud.
- Historial.

Toda la plataforma fue diseñada alrededor de esta entidad.

---

## Etapas

Las Etapas representan la planificación general del proyecto.

No representan tareas.

No representan actividades.

Cada etapa agrupa una parte importante del trabajo.

El avance del proyecto se calcula mediante el progreso de sus etapas.

---

## Acciones

Las Acciones representan trabajo pendiente.

Una Acción tiene un responsable, una fecha y un estado.

Cuando una Acción finaliza puede generar automáticamente un registro en la Bitácora.

---

## Bitácora

La Bitácora representa el historial operativo del proyecto.

Aquí se registra todo aquello que ya ocurrió.

Nunca debe utilizarse como lista de tareas.

La Bitácora constituye la memoria histórica del proyecto.

---

## Archivos

Todos los archivos relacionados con un proyecto deben permanecer organizados dentro del mismo.

Cada archivo podrá clasificarse según su propósito.

El objetivo es evitar que la información termine dispersa entre diferentes plataformas.

---

## Portal del Cliente

El Portal del Cliente representa la vista pública del proyecto.

Su propósito consiste en mantener informado al cliente sin necesidad de solicitar actualizaciones constantemente.

El portal únicamente muestra información autorizada.

Nunca expone información interna del equipo.

---

## Usuarios y Roles

Los Usuarios representan personas con acceso al sistema.

Los Roles determinan qué puede hacer cada usuario.

Los permisos nunca deben implementarse directamente sobre el usuario.

Siempre deberán administrarse mediante Roles y Permisos.

---

## Automatizaciones

Las Automatizaciones permiten ejecutar acciones automáticamente cuando ocurre un evento importante dentro del sistema.

Su objetivo es reducir trabajo repetitivo y mantener sincronizada la información.

Toda automatización debe originarse a partir de un evento claramente definido.

---

# Dashboard

El Dashboard fue el primer módulo diseñado de ProjectFlow y representa la pantalla principal del sistema.

Su objetivo no es mostrar gráficas bonitas.

Su objetivo es ayudar al usuario a tomar decisiones en menos de un minuto.

Durante el diseño del producto se decidió que el Dashboard nunca funcionará como un panel administrativo tradicional.

No será un lugar para navegar.

Será un lugar para decidir.

Cada componente del Dashboard debe responder una pregunta específica.

Si un componente no ayuda al usuario a tomar una decisión, entonces no pertenece al Dashboard.

---

# Filosofía del Dashboard

El Dashboard debe responder inmediatamente las siguientes preguntas.

## ¿Qué requiere mi atención?

Mostrar:

- Acciones vencidas.
- Acciones para hoy.
- Proyectos en riesgo.
- Solicitudes pendientes del cliente.

---

## ¿Qué ocurrió mientras no estaba?

Mostrar:

- Actividad reciente.
- Últimos movimientos.
- Cambios importantes.
- Acciones completadas.
- Comentarios nuevos.

---

## ¿Cómo se encuentra la empresa?

Mostrar:

- Proyectos activos.
- Proyectos finalizados.
- Proyectos pausados.
- Salud general de los proyectos.

---

## ¿Qué debo hacer ahora?

Mostrar accesos rápidos para:

- Crear Persona.
- Crear Cotización.
- Crear Proyecto.
- Registrar Actividad.
- Crear Acción.

---

# Diseño del Dashboard

El Dashboard se divide en bloques independientes.

Cada bloque puede evolucionar sin afectar los demás.

## Atención inmediata

Es el bloque más importante.

Siempre aparece en la parte superior.

Debe mostrar únicamente aquello que requiere atención inmediata.

Ejemplos:

- Acciones vencidas.
- Proyectos detenidos.
- Solicitudes sin responder.
- Entregas próximas.

---

## Agenda del día

Lista únicamente el trabajo correspondiente al día actual.

No muestra el historial.

No muestra tareas futuras.

Debe responder:

"¿Qué tengo que hacer hoy?"

---

## Actividad reciente

Representa la Bitácora global.

Debe mostrar cronológicamente los últimos movimientos importantes.

Ejemplos:

- Nueva evidencia.
- Acción completada.
- Archivo agregado.
- Comentario.
- Cambio de etapa.
- Proyecto creado.

---

## Resumen ejecutivo

Presenta indicadores generales.

Ejemplos:

- Proyectos activos.
- Proyectos terminados.
- Personas registradas.
- Cotizaciones pendientes.
- Acciones abiertas.

Estos indicadores son únicamente informativos.

No representan la prioridad del usuario.

---

## Acciones rápidas

Permiten ejecutar operaciones frecuentes con un solo clic.

Ejemplos:

Nueva Persona.

Nueva Cotización.

Nuevo Proyecto.

Nueva Acción.

Nueva Actividad.

---

# Lo que NO debe tener el Dashboard

Durante el diseño del producto se descartaron múltiples ideas.

No incluir:

- Tablas gigantes.
- Gráficas innecesarias.
- Información histórica excesiva.
- Configuraciones.
- Formularios complejos.

Toda información secundaria pertenece a su módulo correspondiente.

---

# Principio de claridad

El Dashboard debe entenderse en menos de treinta segundos.

Si un usuario necesita capacitación para interpretar el Dashboard, entonces el diseño es incorrecto.

La prioridad siempre será la claridad.

---

# Objetivo final

Cuando un usuario abra ProjectFlow deberá saber inmediatamente:

- Qué está ocurriendo.
- Qué requiere atención.
- Qué debe hacer.
- Cómo se encuentran sus proyectos.

Si el Dashboard logra responder esas cuatro preguntas habrá cumplido completamente su objetivo.

---

# Personas

El módulo Personas representa una de las decisiones arquitectónicas más importantes tomadas durante el diseño de ProjectFlow.

Inicialmente el sistema contemplaba entidades independientes para Clientes, Proveedores y Contactos.

Después de analizar múltiples escenarios de negocio se concluyó que este enfoque generaba duplicidad de información, relaciones complejas y un mantenimiento innecesario.

Como consecuencia se tomó una decisión definitiva.

ProjectFlow tendrá una única entidad llamada **Persona**.

Todas las relaciones comerciales y operativas del sistema partirán de esta entidad.

Esta decisión queda considerada como una regla permanente del proyecto.

---

# Filosofía

Una Persona representa cualquier individuo u organización con la que la empresa mantiene una relación.

El sistema nunca debe asumir que una Persona es exclusivamente un cliente.

Los roles cambian.

La Persona permanece.

Por ejemplo.

Una empresa puede comenzar siendo un Prospecto.

Después convertirse en Cliente.

Posteriormente también convertirse en Proveedor.

Incluso puede participar como Socio en otro proyecto.

Toda esa evolución ocurre sin cambiar de entidad.

Únicamente cambia la relación de negocio.

---

# Tipos de Persona

ProjectFlow reconoce únicamente dos tipos.

## Persona Física

Representa a un individuo.

Ejemplos.

- Juan Pérez
- María López
- Carlos Ramírez

---

## Persona Moral

Representa una empresa, institución u organización.

Ejemplos.

- CODEVIA
- Universidad Innova
- Microsoft
- Ayuntamiento

Las Personas Morales podrán tener múltiples contactos asociados.

---

# Roles

Una Persona puede desempeñar múltiples roles simultáneamente.

Ejemplos.

- Prospecto
- Cliente
- Proveedor
- Socio
- Contacto
- Colaborador externo

Estos roles no crean nuevas entidades.

Son únicamente clasificaciones de la relación comercial.

Una Persona puede tener más de un rol al mismo tiempo.

---

# Objetivo del módulo

El módulo Personas no funciona como una agenda.

No funciona como un directorio.

No funciona como un CRM tradicional.

Su propósito consiste en concentrar toda la información relacionada con cada persona u organización.

Cada Persona representa un expediente completo.

---

# Información general

Cada Persona deberá permitir almacenar información como:

- Nombre o Razón Social.
- Tipo de Persona.
- RFC.
- CURP cuando aplique.
- Correos electrónicos.
- Teléfonos.
- Dirección.
- Sitio web.
- Redes sociales.
- Notas generales.

La información deberá organizarse para facilitar futuras ampliaciones.

---

# Contactos

Las Personas Morales podrán registrar múltiples contactos.

Cada contacto podrá almacenar.

- Nombre.
- Cargo.
- Correo.
- Teléfono.
- Observaciones.

Uno de ellos podrá marcarse como contacto principal.

---

# Historial

Cada Persona mantiene un historial completo de interacción.

Ejemplos.

- Cotizaciones generadas.
- Proyectos relacionados.
- Actividades registradas.
- Documentos.
- Comentarios.
- Acciones.
- Participaciones.

El historial nunca debe perderse.

---

# Relación con Cotizaciones

Una Persona puede tener múltiples Cotizaciones.

Cada Cotización pertenece a una única Persona con rol Cliente.

Una Cotización aprobada puede generar uno o varios Proyectos.

La relación comercial nunca debe romperse.

---

# Relación con Proyectos

Una Persona puede participar en múltiples Proyectos.

La participación dependerá del contexto.

Ejemplos.

Cliente principal.

Proveedor.

Responsable externo.

Contacto.

Supervisor.

Patrocinador.

La relación se define mediante la participación y no modificando la Persona.

---

# Archivos

Cada Persona podrá tener documentos asociados.

Ejemplos.

- Contratos.
- Identificaciones.
- RFC.
- Comprobantes.
- Fotografías.
- Convenios.

Los archivos forman parte del expediente de la Persona.

---

# Bitácora

Las Personas también generan Bitácora.

Ejemplos.

- Llamadas.
- Correos.
- Reuniones.
- Visitas.
- Comentarios.
- Evidencias.

Esto permite conocer todo el historial de relación con la Persona.

---

# Principios del módulo

Durante el diseño se establecieron los siguientes principios.

- Nunca duplicar Personas.
- Nunca separar Clientes y Proveedores en tablas distintas.
- Toda relación debe partir de una Persona.
- Toda información debe permanecer centralizada.
- El historial nunca debe perderse.
- Los roles cambian.
- La Persona permanece.

Estos principios deberán respetarse durante toda la vida del proyecto.

---

# Decisiones congeladas

## DA-001

Existe una única entidad llamada Persona.

Estado:

✅ Aprobada.

---

## DA-002

Clientes y Proveedores no existen como tablas independientes.

Estado:

✅ Aprobada.

---

## DA-003

Una Persona puede desempeñar múltiples roles.

Estado:

✅ Aprobada.

---

## DA-004

Toda relación comercial comienza con una Persona.

Estado:

✅ Aprobada.

---

## Objetivo final

El módulo Personas debe convertirse en el expediente central de cualquier actor que interactúe con la organización.

Toda la información relacionada con esa Persona deberá encontrarse en un único lugar, permitiendo conocer su historial completo sin importar el rol que haya desempeñado a lo largo del tiempo.

---

# Cotizaciones

El módulo de Cotizaciones representa el punto de transición entre una oportunidad comercial y un proyecto formal.

Toda Cotización pertenece obligatoriamente a una Persona con rol Cliente y documenta la propuesta económica y técnica presentada por la organización.

Una Cotización no representa un proyecto.

Representa la intención de realizar un proyecto bajo determinadas condiciones.

---

# Objetivo

El objetivo del módulo es permitir administrar el proceso comercial antes de iniciar la ejecución del trabajo.

Debe conservar el historial completo de cada propuesta presentada al cliente, independientemente de si fue aprobada o rechazada.

---

# Principios

Toda Cotización debe pertenecer a una Persona con rol Cliente.

Una Persona puede tener múltiples Cotizaciones.

Una Cotización puede generar uno o varios Proyectos.

Las Cotizaciones nunca deben eliminarse.

Las Cotizaciones representan el historial comercial de la organización.

---

# Estados

Las Cotizaciones podrán encontrarse en alguno de los siguientes estados.

## Borrador

La propuesta aún se encuentra en elaboración.

No ha sido enviada al cliente.

---

## Enviada

La propuesta fue entregada al cliente y se encuentra pendiente de respuesta.

---

## Aprobada

La Cotización fue aceptada.

A partir de este momento podrá convertirse en un Proyecto.

---

## Rechazada

El cliente decidió no continuar.

La Cotización permanece como parte del historial comercial.

---

## Expirada

La fecha de vencimiento fue superada sin aprobación.

La Cotización conserva íntegramente su historial.

---

## Archivada

La Cotización dejó de formar parte de la operación activa, pero permanece disponible
para consulta histórica.

---

# Información general

Cada Cotización deberá almacenar como mínimo.

- Folio.
- Persona.
- Fecha de emisión.
- Fecha de vencimiento.
- Estado.
- Moneda.
- Título y alcance.
- Condiciones comerciales.
- Impuestos.
- Subtotal.
- Total.
- Observaciones.

---

# Conceptos

Cada Cotización estará integrada por uno o varios conceptos.

Cada concepto podrá contener.

- Nombre.
- Descripción.
- Cantidad.
- Unidad.
- Precio unitario.
- Descuento.
- Importe.

El sistema calculará automáticamente los importes.

---

# Versiones

Mientras una Cotización permanezca en Borrador, su versión actual podrá editarse sin
crear versiones adicionales.

Una versión aprobada representa un acuerdo comercial.

Los conceptos, cantidades, precios, descuentos, impuestos, moneda, condiciones,
alcance y fechas de una versión aprobada son inmutables. Cambiarlos crea
automáticamente una nueva versión Borrador basada en la aprobada.

Una corrección administrativa que no cambie el acuerdo comercial podrá actualizar
datos de presentación, contacto, dirección, observaciones, ortografía o redacción sin
crear una versión. Toda corrección deberá conservar qué cambió, valores anteriores y
nuevos, quién la realizó y cuándo.

La última versión aprobada permanece referenciada como vigente hasta que otra versión
sea aprobada. Ninguna versión anterior se elimina.

Esto permite reproducir exactamente la evolución de la negociación comercial.

---

# Folio

Cada Organización tendrá una secuencia independiente.

El formato oficial será `COT-000001`.

La asignación será transaccional y segura ante concurrencia.

---

# Persona Cliente

Toda Cotización pertenece a una Persona de la misma Organización con rol Cliente.

Si la Persona todavía no tiene ese rol, la interfaz podrá ofrecer agregarlo mediante
confirmación explícita. El sistema nunca lo asignará automáticamente.

---

# Cálculos monetarios

Los importes se calculan en servidor usando precisión decimal.

Por concepto:

```text
base = cantidad × precio unitario
subtotal = base − descuento
impuesto = subtotal × tasa de impuesto
total = subtotal + impuesto
```

El descuento nunca podrá superar la base. La interfaz mostrará importes redondeados a
dos decimales.

---

# Aprobación

La aprobación será autorizada y quedará vinculada al OrganizationMember que la realizó,
no al usuario global.

---

# Vigencia

Cada versión registra fecha de emisión y vencimiento.

Una Cotización activa no aprobada, ya sea Borrador o Enviada, que supere su vencimiento
cambiará de forma idempotente a Expirada, conservando toda la información.

---

# PDF básico

Durante el Sprint 2 el PDF se genera bajo demanda para una versión e incluye datos de
Organización, Cliente, conceptos, totales, condiciones y número de versión.

No incluye firma electrónica, correo, plantillas personalizadas, almacenamiento
histórico ni anexos.

---

# Conversión a Proyecto

Cuando una Cotización sea aprobada podrá convertirse en un Proyecto.

La conversión deberá reutilizar toda la información relevante.

- Persona.
- Nombre del proyecto.
- Descripción.
- Participantes iniciales.
- Presupuesto.
- Fechas.

La Cotización original permanecerá intacta.

Nunca deberá modificarse durante la conversión.

---

# Archivos

Cada Cotización podrá almacenar documentos relacionados.

Ejemplos.

- PDF enviado al cliente.
- Anexos.
- Planos.
- Especificaciones.
- Documentación técnica.

---

# Bitácora

Toda acción importante deberá registrarse automáticamente.

Ejemplos.

- Cotización creada.
- Cotización enviada.
- Cotización modificada.
- Cotización aprobada.
- Cotización rechazada.
- Proyecto generado.

---

# Relación con Personas

Una Persona puede tener múltiples Cotizaciones.

Toda Cotización pertenece a una única Persona con rol Cliente.

Esta relación nunca cambia.

---

# Relación con Proyectos

Una Cotización aprobada podrá generar uno o varios Proyectos.

Cada Proyecto conservará la referencia de la Cotización que le dio origen.

Esto permitirá mantener trazabilidad completa entre el proceso comercial y la ejecución del trabajo.

---

# Decisiones congeladas

## DA-005

Toda Cotización pertenece a una Persona con rol Cliente.

Estado:

✅ Aprobada.

---

## DA-006

Las Cotizaciones nunca se eliminan.

Estado:

✅ Aprobada.

---

## DA-007

Una Cotización aprobada puede convertirse en un Proyecto.

Estado:

✅ Aprobada.

---

## DA-008

El historial comercial debe conservarse permanentemente.

Estado:

✅ Aprobada.

---

## Q-DA-001

Una versión aprobada conserva inmutable el acuerdo comercial. Los cambios comerciales
crean una nueva versión Borrador y las correcciones administrativas conservan
auditoría.

Estado:

✅ Aprobada.

---

## Q-DA-002

Toda Cotización pertenece a una Persona con rol Cliente y su aprobación pertenece a un
OrganizationMember.

Estado:

✅ Aprobada.

---

# Objetivo final

El módulo de Cotizaciones debe convertirse en el puente entre la relación comercial con una Persona y la ejecución formal de un Proyecto, garantizando trazabilidad completa desde la primera propuesta hasta la entrega final del servicio.

---

# Proyectos

El Proyecto representa la entidad central de ProjectFlow.

Todo el sistema fue diseñado alrededor de esta entidad.

Mientras que las Personas representan las relaciones de negocio y las Cotizaciones representan propuestas comerciales, el Proyecto representa la ejecución real del trabajo.

Todo aquello que sucede dentro de la organización termina relacionado con un Proyecto.

Por esta razón, el Proyecto es considerado el núcleo funcional de la plataforma.

---

# Objetivo

El objetivo del módulo es administrar de principio a fin la ejecución de un servicio contratado.

Un Proyecto concentra toda la información necesaria para planificar, ejecutar, controlar y cerrar un trabajo, manteniendo un historial completo de todo lo ocurrido durante su ciclo de vida.

---

# Filosofía

Un Proyecto nunca debe entenderse como una lista de tareas.

Un Proyecto representa un servicio que la organización presta a uno o varios clientes.

Las tareas, actividades, archivos, comentarios, participantes y eventos existen únicamente porque pertenecen a un Proyecto.

El Proyecto es el contexto donde ocurre todo el trabajo.

---

# Información General

Cada Proyecto deberá almacenar, como mínimo, la siguiente información:

- Nombre.
- Código interno.
- Persona principal.
- Cotización de origen.
- Responsable.
- Estado.
- Prioridad.
- Salud.
- Fecha de inicio.
- Fecha estimada de finalización.
- Fecha real de cierre.
- Presupuesto.
- Descripción.
- Observaciones.

---

# Estados del Proyecto

Todo Proyecto deberá encontrarse en uno de los siguientes estados.

## Borrador

El proyecto aún está siendo preparado y no ha iniciado formalmente.

---

## Pendiente

El proyecto fue aprobado pero aún no comienza su ejecución.

---

## En Proceso

El proyecto se encuentra activo y el equipo está trabajando en él.

---

## Pausado

La ejecución fue detenida temporalmente.

Debe registrarse el motivo de la pausa.

---

## Finalizado

El equipo concluyó el trabajo.

Se encuentra pendiente únicamente la entrega oficial.

---

## Entregado

El cliente recibió formalmente el proyecto.

A partir de este momento comienza el periodo de garantía o seguimiento, si aplica.

---

## Cancelado

El proyecto fue cancelado antes de concluir.

La información deberá conservarse para efectos históricos.

---

# Salud del Proyecto

Además del estado, cada Proyecto tendrá un indicador de salud.

La salud representa una evaluación general del desempeño del proyecto.

## Excelente

El proyecto avanza conforme a lo planeado.

No existen riesgos importantes.

---

## Estable

Existen pequeños retrasos o incidencias que no comprometen el resultado.

---

## En Riesgo

El proyecto presenta problemas que requieren atención.

---

## Crítico

La continuidad del proyecto está comprometida.

Requiere intervención inmediata.

---

# Participantes

Un Proyecto puede tener múltiples participantes.

Ejemplos:

- Cliente principal.
- Responsable.
- Coordinador.
- Supervisor.
- Colaborador.
- Proveedor.
- Contacto.

Los participantes siempre serán Personas.

Nunca se crearán entidades independientes para representar clientes o proveedores dentro del proyecto.

---

# Componentes

Todo Proyecto podrá contener los siguientes módulos.

- Etapas.
- Acciones.
- Bitácora.
- Archivos.
- Participantes.
- Comentarios.
- Portal del Cliente.
- Automatizaciones.

Estos componentes forman parte del Proyecto y no existen de manera aislada.

---

# Avance

El porcentaje de avance del Proyecto no será capturado manualmente.

Será calculado automáticamente con base en el progreso de las Etapas.

Esto garantiza que el avance refleje el estado real del trabajo.

---

# Bitácora

Toda acción relevante deberá generar automáticamente un registro en la Bitácora.

Ejemplos.

- Cambio de estado.
- Cambio de responsable.
- Inicio de una etapa.
- Finalización de una etapa.
- Archivo agregado.
- Comentario registrado.
- Acción completada.

La Bitácora representa la memoria histórica del Proyecto.

---

# Archivos

Todo documento relacionado con el Proyecto deberá almacenarse dentro de su expediente.

Ejemplos.

- Contratos.
- Planos.
- Fotografías.
- Evidencias.
- Entregables.
- Facturas.
- Manuales.

Los archivos deberán permanecer organizados y disponibles durante toda la vida del Proyecto.

---

# Portal del Cliente

Cada Proyecto podrá habilitar un Portal del Cliente.

El Portal mostrará únicamente la información autorizada por la organización.

Nunca deberá mostrar información interna del equipo.

---

# Automatizaciones

Los eventos importantes del Proyecto podrán generar acciones automáticas.

Ejemplos.

- Notificar cambios de estado.
- Crear actividades.
- Enviar correos.
- Recordar vencimientos.
- Actualizar indicadores.

Las automatizaciones siempre estarán basadas en eventos del Proyecto.

---

# Principios

Durante el diseño del sistema se establecieron los siguientes principios.

- Todo gira alrededor del Proyecto.
- El Proyecto representa un servicio y no una lista de tareas.
- El historial nunca debe perderse.
- Toda acción importante debe registrarse.
- El avance debe calcularse automáticamente.
- Toda información relacionada debe permanecer centralizada.

---

# Decisiones congeladas

## DA-009

El Proyecto es la entidad central de ProjectFlow.

Estado:

✅ Aprobada.

---

## DA-010

El avance del Proyecto se calcula mediante las Etapas.

Estado:

✅ Aprobada.

---

## DA-011

Toda actividad relevante genera un registro en la Bitácora.

Estado:

✅ Aprobada.

---

## DA-012

Clientes y Proveedores participan mediante Personas.

Estado:

✅ Aprobada.

---

## Objetivo final

El módulo de Proyectos debe convertirse en el expediente central de cada servicio administrado por la organización, concentrando toda la información operativa, documental e histórica necesaria para su correcta ejecución y seguimiento durante todo su ciclo de vida.

---

# Etapas

El módulo de Etapas representa la planificación estratégica de un Proyecto.

Una Etapa agrupa un conjunto de actividades relacionadas que forman parte de una fase importante del trabajo.

Las Etapas no representan tareas individuales.

Representan grandes bloques de ejecución que permiten organizar y medir el avance del Proyecto.

---

# Objetivo

El objetivo del módulo es dividir un Proyecto en fases claramente definidas, facilitando la planeación, el seguimiento y el cálculo del progreso general.

Cada Proyecto podrá tener tantas Etapas como sean necesarias.

---

# Filosofía

Durante el diseño de ProjectFlow se descartó el uso de Hitos (Milestones).

Aunque los hitos funcionan correctamente para marcar eventos importantes, no representan adecuadamente la ejecución del trabajo.

Las Etapas permiten planificar, asignar responsables, medir avances y agrupar Acciones.

Por esta razón, las Etapas sustituyen completamente el concepto de Hitos dentro del sistema.

---

# Información General

Cada Etapa deberá almacenar como mínimo:

- Nombre.
- Descripción.
- Proyecto al que pertenece.
- Responsable.
- Orden.
- Peso.
- Estado.
- Fecha estimada de inicio.
- Fecha estimada de finalización.
- Fecha real de inicio.
- Fecha real de finalización.

---

# Estados

Cada Etapa podrá encontrarse en alguno de los siguientes estados.

## Pendiente

La Etapa aún no ha comenzado.

---

## En Proceso

Actualmente se está ejecutando.

---

## Pausada

La ejecución fue suspendida temporalmente.

---

## Finalizada

Todo el trabajo correspondiente a la Etapa fue concluido.

---

## Cancelada

La Etapa dejó de formar parte del Proyecto.

---

# Peso

Cada Etapa posee un peso porcentual.

El peso representa la importancia relativa de esa Etapa dentro del Proyecto.

La suma de todas las Etapas deberá representar el 100 % del Proyecto.

Ejemplo.

| Etapa | Peso |
|-------|-----:|
| Planeación | 15 % |
| Diseño | 20 % |
| Desarrollo | 40 % |
| Pruebas | 15 % |
| Entrega | 10 % |

El porcentaje de avance del Proyecto será calculado utilizando estos pesos.

---

# Responsable

Cada Etapa podrá tener un responsable principal.

El responsable será quien coordine el trabajo de esa fase.

Esto no impide que existan múltiples colaboradores trabajando en sus Acciones.

---

# Relación con Acciones

Las Acciones pertenecen siempre a una Etapa.

Una Etapa puede contener múltiples Acciones.

Las Acciones representan el trabajo específico necesario para completar la Etapa.

---

# Cálculo del Avance

El avance de cada Etapa dependerá del progreso de sus Acciones.

Cuando todas las Acciones de una Etapa estén completadas, la Etapa podrá marcarse como Finalizada.

El avance general del Proyecto se calculará utilizando el peso de cada Etapa concluida.

---

# Bitácora

Toda modificación importante deberá registrarse automáticamente.

Ejemplos.

- Etapa creada.
- Responsable asignado.
- Cambio de estado.
- Cambio de fechas.
- Etapa finalizada.
- Etapa cancelada.

---

# Principios

Durante el diseño del sistema se establecieron los siguientes principios.

- Las Etapas representan fases del Proyecto.
- Las Acciones representan trabajo específico.
- El avance del Proyecto depende de las Etapas.
- Las Etapas nunca sustituyen a las Acciones.
- Toda Etapa pertenece a un único Proyecto.

---

# Decisiones congeladas

## DA-013

ProjectFlow utiliza Etapas en lugar de Hitos.

Estado:

✅ Aprobada.

---

## DA-014

Cada Etapa posee un peso porcentual.

Estado:

✅ Aprobada.

---

## DA-015

El avance del Proyecto se calcula mediante el progreso de sus Etapas.

Estado:

✅ Aprobada.

---

## DA-016

Toda Acción pertenece a una Etapa.

Estado:

✅ Aprobada.

---

# Objetivo final

El módulo de Etapas debe proporcionar una estructura clara para organizar el trabajo del Proyecto, medir su avance de forma objetiva y facilitar la coordinación del equipo durante todo el ciclo de ejecución.

---

# Acciones

El módulo de Acciones representa las unidades de trabajo que deben realizarse para completar una Etapa y, en consecuencia, un Proyecto.

Las Acciones constituyen el trabajo operativo del sistema.

Mientras las Etapas representan la planificación de alto nivel, las Acciones representan el trabajo específico que ejecutan las personas.

---

# Objetivo

El objetivo del módulo es administrar todo el trabajo pendiente del equipo, permitiendo asignar responsables, establecer prioridades, controlar fechas de vencimiento y registrar el avance de cada actividad.

Una Acción siempre deberá pertenecer a una Etapa y, por consecuencia, a un Proyecto.

Nunca existirán Acciones independientes.

---

# Filosofía

Una Acción representa algo que debe hacerse.

No representa algo que ya ocurrió.

Cuando una Acción finaliza deja de ser trabajo pendiente y pasa a formar parte de la Bitácora del Proyecto.

Esta separación permite distinguir claramente entre trabajo operativo e historial.

---

# Información General

Cada Acción deberá almacenar como mínimo.

- Título.
- Descripción.
- Proyecto.
- Etapa.
- Responsable.
- Prioridad.
- Estado.
- Fecha de inicio.
- Fecha límite.
- Fecha de finalización.
- Tiempo estimado.
- Tiempo invertido.

---

# Estados

Cada Acción podrá encontrarse en alguno de los siguientes estados.

## Pendiente

La Acción fue creada pero aún no inicia.

---

## En Progreso

El responsable comenzó a trabajar en ella.

---

## En Espera

La Acción depende de un tercero o de otra actividad para continuar.

---

## Completada

La Acción fue terminada satisfactoriamente.

---

## Cancelada

La Acción ya no será ejecutada.

---

## Vencida

La fecha límite fue superada sin concluir la Acción.

Este estado podrá calcularse automáticamente.

---

# Prioridades

Cada Acción tendrá un nivel de prioridad.

- Baja.
- Normal.
- Alta.
- Crítica.

La prioridad ayudará a ordenar el trabajo del equipo.

---

# Responsable

Cada Acción tendrá un responsable principal.

Únicamente un usuario será responsable directo de la ejecución.

Sin embargo, podrán existir colaboradores o seguidores que reciban notificaciones sobre su avance.

---

# Fechas

Cada Acción podrá registrar.

- Fecha de creación.
- Fecha estimada de inicio.
- Fecha límite.
- Fecha real de inicio.
- Fecha real de finalización.

Estas fechas permitirán medir puntualidad y desempeño.

---

# Dependencias

Una Acción podrá depender de otra.

Mientras la Acción anterior no se encuentre completada, la siguiente podrá permanecer bloqueada.

Esto permitirá representar flujos de trabajo más complejos.

---

# Evidencias

Cada Acción podrá incluir evidencias.

Ejemplos.

- Fotografías.
- Archivos.
- Documentos.
- Videos.
- Comentarios.
- Enlaces.

Las evidencias permanecerán asociadas al historial de la Acción.

---

# Comentarios

Cada Acción permitirá conversaciones internas entre los miembros del equipo.

Estos comentarios no serán visibles para el cliente.

Cuando sea necesario informar al cliente deberá utilizarse la Bitácora o el Portal del Cliente.

---

# Recordatorios

El sistema podrá generar recordatorios automáticos.

Ejemplos.

- Acción próxima a vencer.
- Acción vencida.
- Cambio de responsable.
- Acción asignada.
- Acción completada.

Las notificaciones podrán enviarse por distintos medios dependiendo de la configuración de la organización.

---

# Relación con la Bitácora

Cuando una Acción cambie de estado deberán registrarse automáticamente eventos importantes.

Ejemplos.

- Acción creada.
- Responsable asignado.
- Inicio de ejecución.
- Cambio de prioridad.
- Acción completada.
- Acción cancelada.

Esto garantiza la trazabilidad completa del Proyecto.

---

# Indicadores

El sistema podrá calcular automáticamente indicadores como.

- Acciones pendientes.
- Acciones vencidas.
- Tiempo promedio de resolución.
- Cumplimiento de fechas.
- Productividad por responsable.

Estos indicadores alimentarán el Dashboard.

---

# Principios

Durante el diseño del sistema se establecieron los siguientes principios.

- Una Acción siempre pertenece a una Etapa.
- Una Acción siempre pertenece a un Proyecto.
- Una Acción representa trabajo pendiente.
- Una Acción nunca representa historial.
- El historial pertenece a la Bitácora.
- Toda Acción tiene un responsable.

---

# Decisiones congeladas

## DA-017

Las Acciones representan trabajo pendiente.

Estado:

✅ Aprobada.

---

## DA-018

Las Acciones siempre pertenecen a una Etapa.

Estado:

✅ Aprobada.

---

## DA-019

La finalización de una Acción puede generar automáticamente un registro en la Bitácora.

Estado:

✅ Aprobada.

---

## DA-020

Toda Acción debe tener un responsable.

Estado:

✅ Aprobada.

---

# Objetivo final

El módulo de Acciones debe convertirse en el motor operativo de ProjectFlow, permitiendo planificar, asignar, ejecutar y controlar el trabajo diario del equipo, manteniendo siempre una trazabilidad completa dentro del Proyecto.

---

# Bitácora

La Bitácora representa el historial oficial de un Proyecto.

Todo acontecimiento importante que ocurra durante el ciclo de vida del Proyecto deberá quedar registrado en ella.

La Bitácora constituye la memoria operativa del Proyecto y permite consultar cronológicamente lo registrado desde su creación hasta su cierre.

Sus registros podrán crearse, editarse y eliminarse según los permisos del usuario.

El sistema deberá conservar auditoría técnica de las modificaciones y eliminaciones.

---

# Objetivo

El objetivo del módulo es proporcionar un registro claro, ordenado y permanente de todos los eventos relevantes relacionados con un Proyecto.

Este historial debe servir tanto para la operación diaria como para auditorías, seguimiento, soporte y comunicación con el cliente.

---

# Filosofía

La Bitácora registra hechos.

Nunca registra trabajo pendiente.

El trabajo pendiente pertenece al módulo de Acciones.

La Bitácora responde a una pregunta muy simple:

> **¿Qué ocurrió en este Proyecto?**

Mientras que las Acciones responden:

> **¿Qué falta por hacer?**

Esta separación constituye uno de los principios fundamentales de ProjectFlow.

---

# Tipos de registros

La Bitácora podrá contener diferentes tipos de eventos.

Ejemplos.

- Actividad registrada.
- Cambio de estado.
- Cambio de responsable.
- Inicio de Etapa.
- Finalización de Etapa.
- Acción completada.
- Archivo agregado.
- Comentario público.
- Solicitud del cliente.
- Entrega parcial.
- Entrega final.
- Incidencia.
- Reunión.
- Llamada.
- Correo importante.

Cada registro deberá identificarse claramente mediante un tipo.

---

# Registro automático

Muchos eventos deberán registrarse automáticamente por el sistema.

Ejemplos.

- Proyecto creado.
- Proyecto pausado.
- Proyecto finalizado.
- Responsable modificado.
- Etapa creada.
- Acción completada.
- Archivo cargado.
- Encuesta enviada.

Esto evita depender completamente de registros manuales.

---

# Registro manual

El equipo también podrá crear registros manuales.

Ejemplos.

- Minuta de reunión.
- Visita al cliente.
- Llamada telefónica.
- Acuerdo importante.
- Cambio solicitado.
- Observaciones.

Esto permite documentar acontecimientos que el sistema no puede detectar automáticamente.

---

# Información General

Cada registro deberá almacenar como mínimo.

- Proyecto.
- Tipo.
- Título.
- Descripción.
- Autor.
- Fecha y hora.
- Evidencias relacionadas.
- Visibilidad.

---

# Evidencias

Cada registro podrá contener evidencias.

Ejemplos.

- Fotografías.
- Documentos.
- Videos.
- Archivos PDF.
- Enlaces.
- Firmas.
- Capturas de pantalla.

Las evidencias permanecerán asociadas permanentemente al registro.

---

# Visibilidad

Cada registro tendrá un nivel de visibilidad.

## Interno

Solo será visible para el equipo.

---

## Compartido

También podrá mostrarse dentro del Portal del Cliente.

Esto permitirá mantener informado al cliente sin exponer información sensible.

---

# Cronología

La Bitácora deberá mostrarse siempre en orden cronológico.

Los registros más recientes aparecerán primero.

Cada evento deberá mostrar claramente.

- Fecha.
- Hora.
- Autor.
- Tipo.
- Descripción.

Esto permitirá comprender rápidamente la evolución del Proyecto.

---

# Relación con otros módulos

La Bitácora se integra con todos los módulos principales.

- Personas.
- Cotizaciones.
- Proyectos.
- Etapas.
- Acciones.
- Archivos.
- Portal del Cliente.
- Automatizaciones.

Prácticamente cualquier evento importante podrá generar un registro.

---

# Búsqueda y filtros

La Bitácora deberá permitir localizar información mediante filtros como.

- Fecha.
- Autor.
- Tipo de evento.
- Proyecto.
- Palabra clave.
- Visibilidad.

Esto facilitará localizar rápidamente cualquier acontecimiento.

---

# Principios

Durante el diseño del sistema se establecieron los siguientes principios.

- La Bitácora representa historial.
- Nunca representa trabajo pendiente.
- Los registros pueden editarse y eliminarse con los permisos correspondientes.
- Las modificaciones y eliminaciones deben conservar auditoría técnica.
- Toda acción importante debe registrarse.
- El registro automático tiene prioridad sobre el manual.
- La presentación cronológica debe mantenerse consistente.

---

# Decisiones congeladas

## DA-021

La Bitácora representa el historial oficial del Proyecto.

Estado:

✅ Aprobada.

---

## DA-022

Las Acciones representan trabajo pendiente.

La Bitácora representa hechos consumados.

Estado:

✅ Aprobada.

---

## DA-023

Los eventos importantes deberán registrarse automáticamente.

Estado:

✅ Aprobada.

---

## DA-024

Los registros podrán marcarse como Internos o Compartidos.

Estado:

✅ Aprobada.

---

## DA-025

Los registros de Bitácora podrán crearse, editarse y eliminarse según permisos.

Las modificaciones y eliminaciones conservarán auditoría técnica.

Estado:

✅ Aprobada.

---

# Objetivo final

El módulo de Bitácora debe convertirse en la memoria permanente de cada Proyecto, permitiendo conocer de forma cronológica, confiable y transparente todo lo que ocurrió durante su ejecución, facilitando la colaboración del equipo y la comunicación con el cliente.

---

# Estado actual del proyecto

Al momento de redactar esta versión del PROJECT_BIBLE, las siguientes decisiones se consideran oficiales y congeladas.

No deben replantearse durante la implementación salvo decisión expresa del Product Owner.

---

## Stack tecnológico

Se ha definido el siguiente stack tecnológico para el desarrollo del sistema.

- Laravel como framework principal.
- Livewire para la construcción de interfaces reactivas.
- Volt para componentes y páginas.
- Flux UI como biblioteca principal de componentes.
- Tailwind CSS para estilos.
- MariaDB como motor de base de datos.
- Redis, Horizon y Reverb preparados para futuras versiones y escalabilidad.

La versión exacta de cada tecnología podrá actualizarse antes del inicio de la implementación sin modificar la arquitectura del sistema.

---

## Modelo de negocio

El flujo oficial del sistema queda definido como:

Persona
→ Cotización
→ Proyecto
→ Etapas
→ Acciones
→ Bitácora
→ Portal del Cliente
→ Entrega
→ Encuesta

Todo el sistema gira alrededor del Proyecto.

---

## Personas

Se elimina completamente la separación entre Clientes y Proveedores.

Existe una única entidad llamada Persona.

Una Persona puede desempeñar múltiples roles simultáneamente.

Los roles cambian.

La Persona permanece.

---

## Cotizaciones

Toda Cotización pertenece a una Persona con rol Cliente.

Una Persona puede tener múltiples Cotizaciones.

Una Cotización aprobada podrá generar uno o varios Proyectos.

Las Cotizaciones forman parte permanente del historial comercial.

Nunca deberán eliminarse.

---

## Proyectos

El Proyecto representa la entidad central de ProjectFlow.

Todo módulo importante se relaciona con un Proyecto.

El avance del Proyecto será calculado automáticamente mediante el progreso de sus Etapas.

---

## Etapas

ProjectFlow utiliza Etapas en lugar de Hitos.

Cada Etapa posee un peso porcentual.

La suma de todas las Etapas representa el 100 % del Proyecto.

---

## Acciones

Toda Acción pertenece obligatoriamente a una Etapa.

No existen Acciones independientes.

Las Acciones representan trabajo pendiente.

Nunca representan historial.

---

## Bitácora

La Bitácora representa el historial oficial del Proyecto.

Registra únicamente hechos ocurridos.

No representa trabajo pendiente.

Los eventos importantes deberán registrarse automáticamente siempre que sea posible.

Los registros podrán editarse y eliminarse con autorización, conservando auditoría
técnica de los cambios.

---

## Automatizaciones

Las Automatizaciones forman parte del MVP.

Se basarán en eventos del sistema.

La inteligencia artificial no forma parte del MVP y se considera una evolución futura del producto.

---

## Dashboard

El Dashboard es un centro de decisiones.

No es una pantalla de navegación.

Debe responder inmediatamente:

- ¿Qué requiere mi atención?
- ¿Qué ocurrió mientras no estaba?
- ¿Cómo se encuentran mis proyectos?
- ¿Qué debo hacer ahora?

---

## Implementación

El orden oficial de implementación será el siguiente.

1. Sprint 0 — completado: plataforma, autenticación, Organización, miembros internos,
   invitaciones, roles, permisos, membresía comercial, configuración base y Dashboard
   inicial de infraestructura.
2. Sprint 1 — aprobado y congelado: Personas, roles comerciales, contactos,
   integridad multitenant en base de datos y análisis estático.
3. Sprint 2 — aprobado y cerrado (`v0.2.0`): Cotizaciones.
4. Sprint 3: Proyectos y participantes.
5. Sprint 4: Etapas y Acciones.
6. Sprint 5: Bitácora y Archivos.
7. Sprint 6: Portal del Cliente y Automatizaciones.
8. Sprint 7: Dashboard de negocio y estabilización.

---
## Stack tecnológico

El stack oficial de ProjectFlow queda definido de la siguiente manera.

Backend

- PHP 8.4+
- Laravel
- Livewire
- Volt

Frontend

- Tailwind CSS
- Flux UI

Base de datos

- MariaDB

Infraestructura

- Redis (preparado)
- Horizon (preparado)
- Reverb (preparado)

Herramientas

- Composer
- Larastan / PHPStan
- Vite
- Git

## Política obligatoria de cierre de Sprints

Cada Sprint deberá finalizar con implementación, validación técnica, auditoría
completa, corrección automática de problemas menores y reporte técnico. El siguiente
Sprint no podrá comenzar sin aprobación expresa.

## Flujo oficial de desarrollo

ProjectFlow utiliza Trunk-Based Development.

- `main` es la única rama estable y debe permanecer compilable, migrable, probada y
  lista para liberar.
- El trabajo se divide en ramas feature de corta duración y objetivo único.
- Toda integración ocurre mediante Pull Request.
- Composer Validate, Laravel Pint, Larastan/PHPStan, pruebas, build y validación de
  migraciones cuando aplique deben terminar correctamente antes del merge.
- Cada Sprint finaliza con una rama y Pull Request de auditoría.
- Los tags se crean únicamente sobre `main`, después de la auditoría, cierre aprobado
  y autorización del Product Owner.
- No se utiliza GitFlow clásico ni una rama `develop` permanente.

La especificación operativa se encuentra en
`docs/architecture/development-workflow.md`.

## Miembros internos y membresía comercial

ProjectFlow distingue dos conceptos.

Los Miembros internos representan a los Usuarios que pertenecen a una Organización.

La Membresía comercial representa el acceso de la Organización a ProjectFlow.

Estos conceptos nunca deberán utilizar la misma entidad.

Cuando se crea una Organización, el sistema asignará automáticamente una Membresía de
Prueba con las siguientes reglas:

- Duración de 14 días.
- Acceso completo a las funciones disponibles.
- Al expirar, la Organización conserva acceso de solo lectura.
- Inicialmente podrá sustituirse o renovarse mediante asignación manual.
- En el futuro podrá activarse o renovarse automáticamente mediante pagos confirmados.
- Todo cambio de membresía conservará historial.

Los planes, precios y proveedor de pagos se definirán posteriormente.

## Especificación técnica propuesta

Los siguientes temas se desarrollan en `MVP_TECHNICAL_SPEC.md` y requieren aprobación
del Product Owner antes de comenzar la implementación.

- Modelo completo de base de datos.
- Arquitectura técnica de Laravel.
- Estrategia de multitenancy por Organización.
- Modelo de Roles y Permisos.
- Seguridad del Portal del Cliente.
- Arquitectura de Automatizaciones.
- ADR (Architecture Decision Records).
- Convenciones de desarrollo.
- Estrategia de pruebas.
