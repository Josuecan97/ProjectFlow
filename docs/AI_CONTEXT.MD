# AI_CONTEXT.md

> Última actualización: 2026-07-24
> Proyecto: ProjectFlow
> Estado: Sprint 2 — especificación de Cotizaciones aprobada; implementación pendiente

---

# ¿Qué es ProjectFlow?

ProjectFlow es un SaaS especializado en la administración de proyectos de servicios.

Su objetivo es ayudar a empresas a organizar, ejecutar y comunicar el avance de sus proyectos de forma profesional, manteniendo informado tanto al equipo interno como al cliente.

ProjectFlow NO pretende ser:

- Un ERP
- Un CRM tradicional
- Un gestor de tareas genérico

Su enfoque es la ejecución de proyectos.

---

# Filosofía del producto

Todo gira alrededor del proyecto.

El flujo natural del sistema es:

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

Cada módulo debe respetar esta filosofía.

---

# Principios del sistema

1. Simplicidad sobre complejidad.

2. Todo debe tener un propósito.

3. No agregar funciones por "si algún día sirven".

4. El MVP es prioridad.

5. Cada módulo debe poder crecer sin romper el resto del sistema.

6. La experiencia del usuario es más importante que tener cientos de funciones.

---

# Stack tecnológico

Backend

- Laravel 13

Frontend

- Livewire 4
- Volt 1
- Flux UI 2
- Tailwind CSS

Base de datos

- MariaDB

Servicios preparados

- Redis
- Horizon
- Reverb
- AWS S3

---

# Estado de implementación

Sprint 0 está completado e incluye plataforma, autenticación, Organizaciones, equipo,
invitaciones, roles, permisos, membresía comercial y Dashboard inicial.

Sprint 1 completó Personas, roles comerciales y contactos. Sprint 2 corresponde a
Cotizaciones. Sus reglas funcionales fueron aprobadas; la implementación no comienza
hasta aprobar la actualización documental.

ProjectFlow utiliza Trunk-Based Development. `main` es la única rama estable; toda
implementación usa ramas pequeñas y Pull Requests. La auditoría precede al merge y los
tags se crean únicamente después del cierre aprobado de cada Sprint. No se utiliza
GitFlow clásico.

---

# Filosofía de desarrollo

Siempre seguir las convenciones oficiales de Laravel.

Preferir:

- Eloquent
- Policies
- Form Requests
- Events
- Jobs
- Queues

Evitar sobreingeniería.

No crear patrones complejos cuando Laravel ya ofrece una solución.

---

# Estructura del proyecto

/projectflow

app/

bootstrap/

config/

database/

docs/

resources/

routes/

tests/

---

# Módulos del MVP

## Dashboard

Resumen ejecutivo.

---

## Organización

Configuración de la empresa.

---

## Usuarios

Usuarios internos.

Roles.

Permisos.

---

## Personas

Persona Física

Persona Moral

Contactos

Historial

---

## Cotizaciones

Generación de propuestas comerciales.

Conversión a Proyecto.

---

## Proyectos

Centro del sistema.

Incluye:

- Información
- Participantes
- Estado
- Salud
- Archivos
- Bitácora
- Etapas

---

## Etapas

Representan el plan de ejecución.

No son tareas.

Cada etapa tiene:

- Orden
- Peso
- Responsable
- Estado

---

## Acciones

Representan trabajo pendiente.

Cuando una Acción termina puede generar automáticamente una Actividad.

---

## Bitácora

Registro cronológico de hechos.

Permite crear, editar y eliminar registros según permisos, conservando auditoría
técnica de los cambios.

Incluye:

- Evidencias
- Llamadas
- Documentos
- Visitas
- Entregas
- Avances

---

## Portal del Cliente

Consulta pública del proyecto.

Incluye:

- Estado
- Avance
- Archivos públicos
- Galería
- Línea del tiempo

---

# Reglas importantes

Las Personas NO son clientes.

Una Persona puede ser:

- Cliente
- Proveedor
- Prospecto
- Contacto
- Socio

El sistema nunca debe asumir un rol fijo.

---

Las Actividades representan hechos.

Las Acciones representan pendientes.

Nunca mezclar ambos conceptos.

---

Las Etapas representan planificación.

No representan tareas.

---

Las Notas internas son diferentes de la Bitácora.

---

El historial técnico de cambios es automático y no editable desde la interfaz.

La Bitácora funcional sí permite edición y eliminación autorizadas.

---

# Objetivos técnicos

El código debe ser:

- Limpio
- Escalable
- Fácil de mantener
- Fácil de leer

Siempre preferir claridad sobre optimización prematura.

---

# Convenciones

Utilizar nombres descriptivos.

No abreviaturas innecesarias.

Seguir PSR.

Usar tipado estricto.

Crear relaciones correctamente.

Validar mediante Form Requests.

Autorizar mediante Policies.

---

# Qué evitar

No implementar funcionalidades futuras.

No crear columnas "por si acaso".

No agregar configuraciones innecesarias.

No crear tablas que todavía no se utilizan.

No sobrecomplicar la arquitectura.

---

# Roadmap

## MVP

- Login
- Organización
- Usuarios
- Personas
- Cotizaciones
- Proyectos
- Etapas
- Acciones
- Bitácora
- Archivos
- Portal
- Automatizaciones básicas
- Dashboard

---

## V1

- Automatizaciones avanzadas
- Reportes
- Calendario
- Notificaciones

---

## V2

- IA
- API Pública
- Marketplace
- Integraciones
- Aplicación móvil

---

# Forma de trabajar

Antes de escribir código:

1. Analizar.

2. Explicar brevemente.

3. Implementar.

4. Verificar.

---

# Rol de las IA

ChatGPT

- CTO
- Arquitecto
- Product Manager

Codex

- Implementador principal

Claude

- Revisión
- UX
- Segunda opinión

---

# Rol del usuario

CEO

Product Owner

Desarrollador principal

Todas las decisiones funcionales pertenecen al usuario.

Las IA pueden proponer mejoras, pero nunca modificar la lógica del negocio sin aprobación.

---

# Regla más importante

Siempre preguntarse:

"¿Esta implementación hace más simple ProjectFlow?"

Si la respuesta es NO, probablemente no deba implementarse.
