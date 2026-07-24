# Auditoría técnica del módulo Personas

> Fecha: 2026-07-24  
> Alcance: código, pruebas y documentación del Sprint 1  
> Resultado: sin defectos críticos abiertos en el alcance implementado

## Resumen ejecutivo

Se revisaron migraciones, modelos, relaciones, Actions, Services, Queries, Policies,
Form Requests, componentes Volt, rutas, pruebas y documentación del módulo Personas.

El diseño actual respeta la entidad única Persona, el aislamiento por Organización,
los roles comerciales múltiples, el modo de solo lectura y los contactos como
Personas relacionadas. Las correcciones menores descritas abajo se aplicaron sin
modificar reglas de negocio, arquitectura ni modelo de datos.

## Correcciones aplicadas

### Seguridad e integridad

- Los Form Requests ahora autorizan mediante PersonPolicy en lugar de aceptar
  cualquier petición por defecto.
- CreatePerson y UpdatePerson validan nuevamente los roles comerciales en la
  frontera de dominio. Un identificador inexistente ya no se descarta silenciosamente.
- Los identificadores de roles recibidos por formulario deben ser distintos y su
  cantidad está limitada al catálogo oficial.
- El país se valida como código alfabético de dos caracteres y se normaliza a
  mayúsculas.
- Se conserva la validación por tenant tanto en formularios como en Actions y
  Policies.

### Compatibilidad y mantenibilidad

- Los helpers estáticos de reglas dejaron de llamarse `validationRules`, nombre que
  colisiona con un método interno de FormRequest en PHP 8.5/Laravel.
- Los campos del formulario de contactos usan nombres consistentes con las reglas de
  validación, de modo que los errores quedan asociados al control correcto.
- Se centralizó la resolución de roles en PersonRoleResolver para eliminar
  duplicación entre alta y edición.
- Se corrigió el nombre de una prueba para reflejar exactamente lo que verifica.

### Rendimiento y consultas

- El listado calcula permisos de edición y archivo una vez por petición, evitando
  consultas de autorización repetidas por cada fila.
- La detección de duplicados selecciona solamente las columnas necesarias.
- Las opciones de contacto se consultan únicamente para Personas morales y usuarios
  autorizados a escribir.
- Una búsqueda compuesta solo por espacios ya no genera un filtro `LIKE '%%'`.
- Los contactos archivados se muestran como referencia histórica sin generar
  enlaces que terminan en 404.

### Pruebas añadidas o ampliadas

- Rechazo de roles comerciales inválidos desde la Action.
- Normalización del país.
- Rechazo de país inválido y roles duplicados.
- Actualización de la interacción Volt del formulario de contactos.

## Resolución oficial de hallazgos

### 1. Medios de contacto múltiples y redes sociales — pospuesto

PROJECT_BIBLE.md define que el expediente debe permitir múltiples teléfonos,
correos y redes sociales. El alcance técnico del Sprint 1 implementa únicamente
`primary_email` y `primary_phone`, y no implementa redes sociales.

Resolverlo implica ampliar el modelo de datos y la interfaz. Queda registrado como
deuda técnica posterior al MVP. En el futuro se recomienda crear una
entidad tenant-aware de medios de contacto, conservar campos principales para acceso
rápido o migrarlos de forma controlada, y definir tipos, etiquetas, principalidad y
normalización. Requiere aprobación de alcance y modelo de datos.

### 2. Integridad multitenant de relaciones en la base — implementado

Las Actions ya impedían autorrelaciones y relaciones entre Organizaciones. La
auditoría detectó que `person_relationships` no podía garantizar por sí sola que ambos
extremos pertenecieran al mismo tenant ante escrituras directas, importaciones
defectuosas o código futuro que evitara las Actions.

Se implementaron claves compuestas desde `person_relationships` y una restricción
contra autorrelaciones. La migración fue validada, revertida y reaplicada en MariaDB.

### 3. Selector de contactos para Organizaciones grandes — pospuesto

La pantalla de expediente carga todas las Personas activas del tenant en un selector.
La consulta ya se evita cuando no es necesaria, pero continuará creciendo de forma
lineal.

Queda registrado como deuda técnica posterior al MVP.

### 4. Estrategia de búsqueda — pospuesto

La búsqueda actual usa coincidencias parciales con comodín inicial sobre nombre,
RFC, correo y teléfono. Es correcta para el MVP, pero los índices convencionales no
serán suficientes con volúmenes altos.

Se definirán umbrales y, cuando sean necesarios, se evaluará búsqueda full-text
compatible con MariaDB o un servicio de búsqueda. No debe introducirse antes de
contar con métricas reales.

### 5. Estado de formularios Volt duplicado — pospuesto

Alta y edición repiten propiedades y mapeo de atributos. No representa un fallo,
pero aumenta el costo de agregar campos.

Queda registrada una refactorización futura hacia un Livewire Form
Object compartido, manteniendo las Actions como frontera de negocio.

### 6. Análisis estático — implementado

Larastan 3.10 y PHPStan 2.2 quedaron integrados en nivel 5 mediante
`composer analyse`. El proyecto termina sin errores y no utiliza baseline.

## Validación final

- Laravel Pint: correcto.
- Pest/Laravel: 59 pruebas, 59 correctas, 178 aserciones.
- Larastan/PHPStan nivel 5: cero errores.
- `composer validate --strict`, instalación desde lock y autoload optimizado: correctos.
- Limpieza de cachés, `route:list` y `artisan about`: correctos.
- Estado de migraciones MariaDB: todas ejecutadas.
- Build de Vite: correcto.
- La consulta en línea de avisos de seguridad de Composer/npm no pudo repetirse
  durante esta auditoría porque el entorno denegó el envío de metadatos de
  dependencias a servicios externos. La entrega original registró cero
  vulnerabilidades, pero ese resultado no se considera revalidado aquí.

Se implementaron únicamente las dos mejoras aprobadas. Las cuatro mejoras funcionales
pospuestas quedaron registradas como deuda técnica. No se inició el Sprint 2.
