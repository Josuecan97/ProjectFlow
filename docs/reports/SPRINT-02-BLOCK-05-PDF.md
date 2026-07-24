# Sprint 2 — Bloque 05: PDF de Cotizaciones

## Estado

Completado y validado el 24 de julio de 2026.

## Objetivo

Generar bajo demanda un PDF reproducible desde una QuoteVersion autorizada, sin
almacenarlo y sin incorporar funcionalidades fuera del Sprint.

## Rama

`feature/s2-quotation-pdf`

## Implementación

- Renderizado PDF mediante Dompdf.
- Action autorizada y servicio de renderizado separados.
- Documento generado desde una versión específica, no desde datos de formulario.
- Datos de Organización, Cliente, folio, versión, vigencia, conceptos y totales.
- Alcance, condiciones comerciales y observaciones.
- Ruta protegida por autenticación, contexto de Organización y Policy.
- Validación explícita de que la versión pertenece a la Cotización y al tenant.
- Respuesta inline con nombre estable `COT-000001-v1.pdf`.
- Acceso al PDF desde el expediente.
- Recursos remotos y ejecución PHP deshabilitados en Dompdf.

## Archivos creados

- `app/Domain/Quotes/Actions/GenerateQuotePdf.php`
- `app/Domain/Quotes/Services/QuotePdfRenderer.php`
- `app/Domain/Quotes/ValueObjects/GeneratedQuotePdf.php`
- `app/Http/Controllers/Quotes/QuotePdfController.php`
- `resources/views/pdf/quote.blade.php`
- `tests/Feature/Quotes/QuotePdfTest.php`

## Archivos modificados

- `composer.json`
- `composer.lock`
- `docs/ARCHITECTURE.MD`
- `resources/views/pages/quotes/show.blade.php`
- `routes/web.php`

## Migraciones y eventos

No se agregaron migraciones ni eventos.

## Pruebas agregadas

Tres pruebas cubren:

- respuesta PDF autorizada, headers y firma del archivo;
- bloqueo de acceso cruzado entre Organizaciones;
- rechazo de una QuoteVersion perteneciente a otra Cotización.

## Verificación del documento

- PDF válido, versión 1.7 y tamaño A4.
- Una página para el caso funcional inspeccionado.
- Extracción de texto completa mediante Poppler.
- Renderizado a PNG revisado visualmente.
- Sin texto recortado, solapamientos, glifos rotos ni tablas desalineadas.
- Encabezado, pie, jerarquía, vigencia y totales legibles.

## Riesgos

- Dompdf debe mantenerse actualizado mediante el flujo normal de Composer.
- Documentos con un número elevado de conceptos producirán varias páginas. La tabla
  permite paginación natural y los bloques finales evitan saltos internos, pero deben
  incluirse casos extensos en pruebas de regresión futuras.
- Logo, plantillas personalizadas, almacenamiento, firma y correo permanecen fuera de
  alcance según la especificación.

## Próximo bloque

Realizar la auditoría integral del Sprint 2: código, seguridad, multitenancy,
rendimiento, migraciones, documentación, interfaz y PDF. Corregir problemas menores y
presentar el informe final sin cerrar el Sprint ni crear tags.
