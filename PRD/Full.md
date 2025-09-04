# Documento de Requisitos del Producto (PRD) - ERP RedPrint

**Versión:** 2.0  
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

- Inventario: Minimizar discrepancias de inventario asegurando que el sistema registre en tiempo real entradas, salidas, transferencias y asignaciones, con trazabilidad completa.
- Cobranza: Agilizar el ciclo de cobro mediante control de cuentas por cobrar.
- Rentabilidad: Brindar visibilidad inmediata de la rentabilidad de contratos y clientes, consolidando ingresos y costos asociados.
- Servicio Técnico: Reducir tiempos administrativos en la gestión de tickets mediante un sistema integrado de asignación, seguimiento y cierre digital.

---

## 2. Resumen Ejecutivo

El ERP RedPrint será un sistema modular que centraliza y automatiza operaciones críticas de renta de impresoras y venta de equipo. Integrará módulos de Inventario, Clientes, Ventas,  Servicio Técnico y Reportes, proporcionando visibilidad integral, optimización de recursos y soporte para el crecimiento escalable del negocio.

---

## 3. Alcance del Proyecto

### 3.1. Funcionalidades Incluidas (In-Scope)

- **Módulo de Inventario:** Gestión centralizada de productos, múltiples almacenes, trazabilidad y alertas.
- **Módulo de Clientes:** Catálogo central de clientes con vista unificada de su historial comercial (contratos, ventas, facturas, tickets).
- **Módulo de Rentas:** Gestión del ciclo de vida completo de contratos de renta, incluyendo asignación de equipos, términos de facturación, registro de lecturas y renovaciones.
           
- **Módulo de Ventas:** Proceso ágil de ventas directas y cotizaciones, con gestión de estatus e impacto automático en inventario.
- **Módulo de Compras y Proveedores:** Registro ágil de compras, gestión de proveedores y control de inventario mediante recepción de mercancía e importación de facturas XML.
- **Módulo de Servicio Técnico:** Registro de actividades, incidencias, control de insumos y calendario servicios.
- **Módulo de Contabilidad:** Gestión centralizada de cuentas por cobrar y por pagar, conciliación bancaria e importación de facturas XML para compras y ventas.
- **Módulo de Reportes y Dashboard:** KPIs, rentabilidad, inventario, ventas y cuentas por cobrar.

### 3.2. Funcionalidades Excluidas (Out-of-Scope para MVP)

- Recursos Humanos y nómina.
- Portal de autogestión para clientes (fase futura).
- Integración con e-commerce.
- App móvil nativa avanzada (solo web responsivo en MVP).

---

## 4. Roles Clave (Stakeholders de referencia)

- Usuario del sistema: Persona que opera el ERP para gestionar inventario, contratos, ventas, servicio técnico.
- Administrador del sistema: Responsable de configuraciones, seguridad, respaldos, auditoría y mantenimiento técnico del ERP.

## 5. Requerimientos Funcionales

### 5.1. Módulo de Inventario

- Creación de productos con SKU, número de serie, descripción, costo, precio y stock mínimo.
- Gestión de catálogos: categorías, marcas, unidades de almacén.
- Clasificación: equipo para renta, venta, refacciones, consumibles.
- Gestión de múltiples almacenes (central, vehículos técnicos).
- Trazabilidad de movimientos (entradas, salidas, transferencias, ajustes).
- Asignación de equipos a contratos de renta (cambia estado a "En Renta").
- Alertas automáticas por stock bajo.

### 5.2. Módulo de Clientes

- Catálogo de Clientes: Registro de información fiscal y de contacto (razón social, RFC, dirección, teléfono, email).
- Vista consolidada: Acceso desde la ficha del cliente a:
  - Historial de facturas emitidas
  - Estado de cuentas por cobrar
  - Contratos de renta activos e históricos
  - Historial de ventas
  - Tickets de servicio técnico
- Búsqueda y filtros por nombre, RFC o estado.

### 5.3. Módulo de Rentas

- Creación y gestión de contratos con cliente, fechas, equipos asignados.
- Términos de facturación: renta base, páginas incluidas, costo por página excedente.
- Registro manual mensual de lecturas de contador para cálculo de consumo.
- Historial de servicios técnicos asociados al contrato.
- Alertas automáticas para renovaciones, vencimientos y recordatorios de lecturas.
- Generación automática de registro en Cuentas por Cobrar al facturar.

### 5.4. Módulo de Ventas

- Cotizaciones (Opcional): Creación ágil de cotizaciones en PDF. Pueden convertirse en ventas con un clic.
- Registro ágil de ventas: Captura directa de ventas (cliente, productos, precios) sin necesidad de cotización previa.
- Gestión de estatus: Control de ventas (Borrador, Confirmada, Entregada, Facturada).
- Impacto en inventario:
  - Actualización automática al marcar como "Entregada".
  - Opción de vincular a una entrada de inventario manual previa.
- Importación de XML: Carga de facturas XML para asociar a ventas
  > Nota: La generación física del CFDI se realiza en un sistema externo (PAC). Este ERP se integra importando el XML resultante para conciliar y registrar la transacción como facturada.
- Registrar cuentas por cobrar.

### 5.5. Módulo de Compras y Proveedores

- Catálogo de proveedores: Registro de datos fiscales y de contacto.
- Registro ágil de compras: Captura directa de compras (proveedor, productos, costos).
- Gestión de estatus: Control de compras (Pendiente, Recibida, Facturada).
- Impacto en inventario:
  - Actualización automática al marcar como "Recibida".
  - Opción de vincular a una entrada de inventario manual previa.
- Importación de XML: Carga de facturas XML para asociar a compras
  > Nota: La generación física del CFDI se realiza en un sistema externo (PAC). Este ERP se integra importando el XML resultante para conciliar y registrar la transacción como facturada.
- Registrar cuentas por pagar.

### 5.6. Módulo de Servicio Técnico

- Creación de tickets asociados a cliente y/o contrato.
- Asignación de tickets a técnicos disponibles.
- Registro de actividades: diagnóstico, acciones, tiempo invertido, refacciones utilizadas.
- Adjuntar fotos o documentos al ticket.
- Cierre de ticket con firma digital del cliente.
- Programación de mantenimientos preventivos.

### 5.7. Módulo de Contabilidad

- Cuentas por Cobrar: Gestión centralizada de saldos pendientes de clientes (originados en Rentas y Ventas). Registro de pagos y aplicación a saldos.
- Cuentas por Pagar: Registro y seguimiento de facturas pendientes de pago a proveedores (originadas en Compras).
- Conciliación Bancaria: Registro de movimientos bancarios y conciliación con facturas emitidas/pagadas.
- Importación de XML:
  - Facturas de compra (proveedores)
  - Facturas emitidas (ventas/rentas)
- Registro de Pagos: Funcionalidad para registrar pagos recibidos de clientes (abonando a sus cuentas por cobrar) y pagos realizados a proveedores (para cerrar sus facturas en cuentas por pagar).
- Reportes Financieros: Estado de cuentas, flujo de caja, reporte de antigüedad de saldos.

### 5.8. Módulo de Reportes y Dashboard

- Dashboard principal con KPIs: ingresos, cuentas por cobrar, tickets abiertos, nivel de inventario.
- Reporte de rentabilidad por contrato y cliente.
- Reporte de valor de inventario.
- Reporte de ventas por vendedor/cliente.
- Eficiencia de técnicos y análisis de costos operativos.

---

## 6. Requerimientos No Funcionales

- **Rendimiento:** Tiempo de carga de páginas < 2 segundos; reportes complejos < 10 segundos; soporte para 100 usuarios concurrentes.
- **Seguridad:** Autenticación basada en roles (RBAC), 2FA para admins, cifrado de datos sensibles (AES-256/TLS), logs de auditoría.
- **Usabilidad:** Interfaz limpia, intuitiva, responsiva y en español.
- **Escalabilidad:** Soporte para duplicar usuarios y datos en 2 años sin degradación.
- **Disponibilidad:** 99.8% uptime mensual (SLA).
- **Cumplimiento:** Protección de datos conforme a regulaciones locales.

---

## 7. Flujos de Usuario

### 7.1. Ciclo de Vida del Contrato de Renta

1. Vendedor/Operaciones crea contrato, selecciona cliente y equipo.
2. Sistema marca equipo como "Asignado" y descuenta del stock.
3. Instalación del equipo en domicilio del cliente.
4. Mensualmente: registro de lectura de contador.
5. Sistema calcula monto a facturar y genera factura.
6. Administrador timbra y envía factura.
7. Si hay problema, se crea ticket de servicio técnico.
8. Al final del periodo: decisión de renovar o terminar contrato.

### 7.2. Proceso de Venta Directa


1. Vendedor crea una venta directa (opcionalmente puede generar una cotización primero).
2. Sistema verifica disponibilidad en tiempo real.
3. Al confirmar la venta, se genera el movimiento de venta y se descuenta el inventario.
4. Administrador genera factura (o importa XML facturado).
5. Sistema registra automáticamente la cuenta por cobrar.

### 7.3. Atención de Ticket de Servicio Técnico

1. Cliente/Operaciones reporta falla y se crea ticket.
2. Líder Técnico asigna ticket a técnico disponible.
3. Sistema notifica al técnico.
4. Técnico diagnostica y registra hallazgos.
5. Si usa refacciones, las descuenta del inventario.
6. Soluciona problema y obtiene firma digital del cliente.
7. Cierra ticket y sistema actualiza estatus.

---

## 8. Requerimientos Técnicos

- **Backend:** Laravel (PHP).
- **Frontend:** Nuxt.js (Vue.js).
- **Base de Datos:** PostgreSQL (transaccional).
- **Infraestructura :** Local (servidor propio de la empresa).
- **APIs:** RESTful internas.
- **Autenticación:** OAuth 2.0/JWT, soporte para 2FA.

 

---
