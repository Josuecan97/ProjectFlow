# ProjectFlow - Base de Datos

> Documento maestro del modelo de datos.

---

# Filosofía

La base de datos debe representar el negocio.

No almacenar información duplicada.

No crear tablas para funcionalidades futuras.

---

# Entidades principales

Organización

↓

Usuario

↓

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

Archivos

---

# Relaciones principales

Una Organización

tiene muchos Usuarios

tiene muchas Personas

tiene muchos Proyectos

---

Una Persona

puede tener muchas Cotizaciones

puede participar en muchos Proyectos

---

Una Cotización

pertenece a una Persona

puede convertirse en un Proyecto

---

Un Proyecto

pertenece a una Organización

puede tener varias Personas

tiene muchas Etapas

tiene muchas Actividades

tiene muchas Acciones

tiene muchos Archivos

---

Una Etapa

pertenece a un Proyecto

tiene muchas Acciones

tiene muchas Actividades

---

Una Acción

pertenece a un Proyecto

opcionalmente pertenece a una Etapa

---

Una Actividad

pertenece a un Proyecto

puede pertenecer a una Persona

puede pertenecer a una Etapa

---

# Convenciones

Todos los IDs serán BIGINT.

Todas las tablas tendrán:

id

created_at

updated_at

deleted_at cuando aplique.

---

# Claves foráneas

Siempre utilizar Foreign Keys.

No guardar IDs sin relación.

---

# Soft Deletes

Aplicar únicamente cuando tenga sentido recuperar información.

---

# Índices

Crear índices para:

organization_id

project_id

person_id

status

created_at

---

# Auditoría

Las actividades representan el historial funcional.

No utilizar tablas de auditoría hasta que exista una necesidad real.

---

# Objetivo

Mantener una base de datos limpia, consistente y preparada para escalar.