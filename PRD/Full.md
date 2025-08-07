# Documento de Requisitos del Producto (PRD) - ERP RedPrint

**Versión:** 1.0  
**Fecha:** 07 de agosto de 2024  
**Autor:** Product Manager / Analista de Negocios  
**Estado:** Borrador para Revisión

---

## 1. Contexto del Negocio y Objetivos

### 1.1. Contexto del Negocio

RedPrint es una empresa que opera bajo dos modelos principales:

- **Renta de impresoras multifuncionales:** Contratos de servicio a largo plazo para empresas, incluyendo equipo, mantenimiento y consumibles, con cobro por página impresa.
- **Venta directa:** Comercialización de equipo de cómputo, periféricos y consumibles.

Actualmente, la gestión se apoya en herramientas descentralizadas (hojas de cálculo, correo, sistemas básicos), generando silos de información, ineficiencias y falta de visibilidad estratégica.

### 1.2. Problemas a Resolver

- Falta de visibilidad y control de inventario en tiempo real.
- Desconocimiento de la rentabilidad por contrato, cliente o equipo.
- Gestión manual y propensa a errores de cuentas por cobrar y conciliación bancaria.
- Procesos administrativos manuales y redundantes.
- Ausencia de reportes analíticos y dashboards integrados.

### 1.3. Objetivos del Producto (Metas SMART)

- Reducir discrepancias de inventario en un 95% en 6 meses.
- Mejorar el ciclo de cobro en un 30% en el primer año.
- Automatizar el 100% de la facturación recurrente basada en contratos.
- Obtener visibilidad en tiempo real sobre la rentabilidad del 80% de los contratos activos.
- Reducir en un 40% el tiempo administrativo dedicado a tickets y asignación de técnicos.

---

## 2. Resumen Ejecutivo

El ERP RedPrint será un sistema modular que centraliza y automatiza operaciones críticas de renta de impresoras y venta de equipo. Integrará módulos de Inventario, Contratos, Ventas, Facturación, Servicio Técnico y Reportes, proporcionando visibilidad 360°, optimización de recursos y soporte para el crecimiento escalable del negocio.

---

## 3. Alcance del Proyecto

### 3.1. Funcionalidades Incluidas (In-Scope)

- **Módulo de Inventario:** Gestión centralizada de productos, múltiples almacenes, trazabilidad y alertas.
- **Módulo de Contratos de Renta:** Ciclo de vida completo, asignación de equipos, términos de facturación y renovaciones.
- **Módulo de Ventas:** CRM, cotizaciones, órdenes de venta, historial y conversión de cotizaciones.
- **Módulo de Facturación:** Facturación automatizada/manual, integración con PAC, gestión de cuentas por cobrar y conciliación.
- **Módulo de Servicio Técnico:** Tickets, asignación, registro de actividades, adjuntos y cierre con firma digital.
- **Módulo de Reportes y Dashboard:** KPIs, rentabilidad, inventario, ventas y cuentas por cobrar.

### 3.2. Funcionalidades Excluidas (Out-of-Scope para MVP)

- Módulo de contabilidad completo (solo integración futura).
- Recursos Humanos y nómina.
- Portal de autogestión para clientes (fase futura).
- Integración con e-commerce.
- App móvil nativa avanzada (solo web responsivo en MVP).

---

## 4. Stakeholders

| Rol                      | Nombre   | Responsabilidad en el Proyecto                        |
|--------------------------|----------|-------------------------------------------------------|
| Director General         | (Nombre) | Patrocinador, aprobación final                        |
| Gerente de Operaciones   | (Nombre) | Dueño del producto, define prioridades                |
| Gerente de Ventas        | (Nombre) | Valida flujo de ventas y CRM                          |
| Líder Técnico de Servicio| (Nombre) | Define requisitos de servicio técnico                 |
| Encargado de Almacén     | (Nombre) | Valida flujo de inventario                            |
| Administrador/Contador   | (Nombre) | Define requisitos de facturación y cobranza           |
| Equipo de TI             | (Nombre) | Soporte técnico e implementación                      |

---

## 5. Historias de Usuario

1. Como Gerente de Operaciones, quiero registrar un nuevo contrato de renta, asociando cliente y equipo, para automatizar seguimiento y facturación.
2. Como Administrador, quiero que el sistema genere automáticamente las facturas mensuales de renta, para asegurar flujo de cobro constante.
3. Como Técnico de Servicio, quiero recibir notificaciones en mi dispositivo cuando se me asigne un ticket, para reducir mi tiempo de respuesta.
4. Como Técnico, quiero consultar el stock de una refacción y registrar su uso en un ticket, para actualizar inventario en tiempo real.
5. Como Vendedor, quiero crear una cotización de venta con precios y disponibilidad actualizados, para enviarla rápidamente a un cliente.
6. Como Encargado de Almacén, quiero registrar la entrada de productos con número de serie, para tener control preciso de activos.
7. Como Gerente de Operaciones, quiero ver un dashboard con rentabilidad por contrato, para identificar clientes no rentables.
8. Como Administrador, quiero registrar manualmente la lectura del contador de una impresora, para facturar correctamente.
9. Como Vendedor, quiero convertir una cotización aprobada en orden de venta con un clic, para agilizar facturación y entrega.
10. Como Administrador, quiero marcar una factura como "pagada" y registrar el método de pago, para controlar cuentas por cobrar.
11. Como Líder Técnico, quiero ver un reporte de tickets abiertos/cerrados y tiempo promedio de solución, para evaluar desempeño.
12. Como Director General, quiero un reporte consolidado de ingresos mensuales por línea de negocio, para visión financiera clara.

---

## 6. Requerimientos Funcionales

### 6.1. Módulo de Inventario

- Creación de productos con SKU, número de serie, descripción, costo, precio y stock mínimo.
- Clasificación: equipo para renta, venta, refacciones, consumibles.
- Gestión de múltiples almacenes (central, vehículos técnicos).
- Trazabilidad de movimientos (entradas, salidas, transferencias).
- Asignación de equipos a contratos de renta.
- Alertas automáticas por stock bajo.

### 6.2. Módulo de Contratos de Renta

- Creación de contratos con datos del cliente, fechas, equipos asignados.
- Definición de términos de facturación: renta base, páginas incluidas, costo por excedente.
- Registro de lecturas de contadores (manual o integración futura).
- Historial de servicios y consumibles asociados.
- Notificaciones automáticas para renovaciones o vencimientos.

### 6.3. Módulo de Ventas

- CRM: base de datos de clientes y prospectos.
- Generación de cotizaciones en PDF con logo.
- Conversión de cotización a orden de venta.
- Historial de ventas por cliente.
- Integración con métodos de pago.

### 6.4. Módulo de Facturación

- Generación de facturas a partir de contratos (automática) y ventas (manual).
- Integración con PAC para timbrado CFDI 4.0.
- Envío de facturas por correo electrónico.
- Gestión de estatus: Borrador, Timbrada, Enviada, Pagada, Cancelada.
- Reporte de antigüedad de saldos.
- Conciliación bancaria básica.

### 6.5. Módulo de Servicio Técnico

- Creación de tickets asociados a cliente y/o contrato.
- Asignación de tickets a técnicos disponibles.
- Registro de actividades: diagnóstico, acciones, tiempo invertido.
- Adjuntar fotos o documentos al ticket.
- Cierre de ticket con firma digital del cliente.
- Programación de mantenimientos preventivos.

### 6.6. Módulo de Reportes y Dashboard

- Dashboard principal con KPIs: ingresos, cuentas por cobrar, tickets abiertos, nivel de inventario.
- Reporte de rentabilidad por contrato y cliente.
- Reporte de valor de inventario.
- Reporte de ventas por vendedor/cliente.
- Eficiencia de técnicos y análisis de costos operativos.

---

## 7. Requerimientos No Funcionales

- **Rendimiento:** Tiempo de carga de páginas < 2 segundos; reportes complejos < 10 segundos; soporte para 100 usuarios concurrentes.
- **Seguridad:** Autenticación basada en roles (RBAC), 2FA para admins, cifrado de datos sensibles (AES-256/TLS), logs de auditoría.
- **Usabilidad:** Interfaz limpia, intuitiva, responsiva y en español.
- **Escalabilidad:** Soporte para duplicar usuarios y datos en 2 años sin degradación.
- **Disponibilidad:** 99.8% uptime mensual (SLA).
- **Cumplimiento:** Protección de datos conforme a regulaciones locales.

---

## 8. Flujos de Usuario

### 8.1. Ciclo de Vida del Contrato de Renta

1. Vendedor/Operaciones crea contrato, selecciona cliente y equipo.
2. Sistema marca equipo como "Asignado" y descuenta del stock.
3. Instalación del equipo en domicilio del cliente.
4. Mensualmente: registro de lectura de contador.
5. Sistema calcula monto a facturar y genera factura.
6. Administrador timbra y envía factura.
7. Si hay problema, se crea ticket de servicio técnico.
8. Al final del periodo: decisión de renovar o terminar contrato.

### 8.2. Proceso de Venta Directa

1. Vendedor crea cotización, agrega productos del inventario.
2. Sistema verifica disponibilidad en tiempo real.
3. Vendedor envía cotización en PDF.
4. Cliente aprueba cotización.
5. Vendedor convierte cotización en orden de venta.
6. Administrador genera factura.
7. Almacén prepara pedido y descuenta stock.

### 8.3. Atención de Ticket de Servicio Técnico

1. Cliente/Operaciones reporta falla y se crea ticket.
2. Líder Técnico asigna ticket a técnico disponible.
3. Sistema notifica al técnico.
4. Técnico diagnostica y registra hallazgos.
5. Si usa refacciones, las descuenta del inventario.
6. Soluciona problema y obtiene firma digital del cliente.
7. Cierra ticket y sistema actualiza estatus.

---

## 9. Requerimientos Técnicos

- **Backend:** Node.js (NestJS/Express) o Python (Django).
- **Frontend:** React (Next.js) o Vue.js (Nuxt.js).
- **Base de Datos:** PostgreSQL (transaccional), posible uso de MongoDB para reportes.
- **Infraestructura Cloud:** AWS o Google Cloud Platform (EC2, RDS, S3/Cloud Storage).
- **APIs:** RESTful internas, integración obligatoria con PAC para timbrado CFDI, API para monitoreo de impresoras (futuro).
- **Autenticación:** OAuth 2.0/JWT, soporte para 2FA.

---

## 10. Plan de Implementación

### Fase 1: MVP - Núcleo Operativo (3-4 meses)

- Módulos: Inventario, Contratos de Renta, Facturación básica.
- Migración inicial de datos maestros desde hojas de cálculo.

### Fase 2: Expansión Comercial y de Servicio (2-3 meses)

- Módulos: Venta Directa (CRM, Cotizaciones), Servicio Técnico (Tickets).
- Capacitación intensiva para ventas y técnicos.

### Fase 3: Inteligencia de Negocio y Automatización (2 meses)

- Módulos: Reportes avanzados, Dashboard de KPIs, automatización completa de facturación.
- Capacitación a gerencia y dirección.

---

## 11. Migración de Datos y Capacitación

- Importar clientes, productos, contratos activos y stock inicial desde hojas de cálculo.
- Limpieza y normalización de datos.
- Pruebas de integridad tras la migración.
- Capacitación por rol (ventas, almacén, técnicos, administrativos).
- Manuales de usuario digitales y soporte post-implementación.

---

## 12. Anexos

- **Glosario:** Definición de términos clave (SKU, KPI, SLA, Contrato Activo, etc.).
- **Diagramas:** BPMN de procesos, arquitectura del sistema.
- **Wireframes:** Pantallas clave (Dashboard, Contratos, Tickets).
- **Exportaciones:** Plantillas para migración de datos.

---

**Nota:** Este documento es vivo y se actualizará según feedback de stakeholders.

**Aprobado por:**  
[Nombre del Dueño/Gerente]  
[Fecha de aprobación]

---
