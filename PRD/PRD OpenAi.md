## Documento de Requisitos del Producto (PRD) - ERP para RedPrint

---

## 1. Contexto del Negocio y Objetivos

**Nombre de la empresa:** RedPrint

**Líneas de Negocio:**

* **Renta de impresoras:** Rentamos impresoras a negocios locales, a los cuales les suministramos tanto el servicio técnico como los consumibles necesarios, cobrando por número de páginas impresas.
* **Venta de equipo:** Vendemos equipo de cómputo y consumibles a negocios locales.

**Problemas Actuales:**

* Falta de un sistema centralizado para administrar las operaciones.
* Visibilidad limitada del inventario en tiempo real.
* Desconocimiento de los costos operativos por impresora y del rendimiento por cliente/impresora.
* Control deficiente de las cuentas por cobrar.
* Ausencia de conciliación bancaria.

**Objetivo General del ERP:**
Implementar un sistema integral que brinde visibilidad en tiempo real sobre los datos clave del negocio, ayude en la administración eficiente, y automatice procesos de contratos, inventario, facturación, servicio técnico y ventas.

---

## 2. Resumen Ejecutivo

RedPrint es una empresa local dedicada a dos líneas de negocio principales: la renta de impresoras multifuncionales con servicio técnico y consumibles incluidos, y la venta directa de equipo de cómputo y consumibles a negocios locales. Actualmente, la operación carece de un sistema centralizado que permita gestionar eficientemente sus procesos clave.

Entre los principales problemas destacan la falta de visibilidad del inventario, la ausencia de métricas claras de rendimiento operativo (por impresora o cliente), la gestión deficiente de las cuentas por cobrar y la inexistencia de conciliaciones bancarias automatizadas. Estas limitaciones generan pérdidas, retrasos en la cobranza y dificultad para tomar decisiones informadas.

La solución propuesta es el desarrollo e implementación de un sistema ERP a medida que automatice la administración de contratos, inventario, facturación, servicio técnico y ventas. Este ERP permitirá un control integral del negocio, mejorará la eficiencia operativa y proporcionará visibilidad en tiempo real sobre los números clave, facilitando una toma de decisiones basada en datos confiables.

---

## 3. Alcance del Proyecto

**Funcionalidades Incluidas (In-Scope):**

* Módulo de CRM para gestión de clientes
* Módulo de Inventario con soporte para múltiples almacenes y control por número de serie
* Módulo de Contratos de Renta
* Módulo de Facturación automatizada (ventas y renta)
* Módulo de Servicio Técnico y gestión de tickets
* Módulo de Reportes Operativos y Financieros

**Funcionalidades Excluidas (Out-of-Scope):**

* Portal de autogestión para clientes
* Aplicación móvil para técnicos (versión inicial)
* Integración con e-commerce
* Contabilidad electrónica avanzada

---

## 4. Stakeholders

* **Director General:** Toma decisiones estratégicas basadas en reportes del ERP.
* **Gerente de Ventas:** Supervisa cotizaciones, ventas y clientes.
* **Administrador de Contratos:** Crea, gestiona y renueva contratos de renta.
* **Encargado de Almacén:** Controla entradas y salidas de inventario.
* **Técnico de Campo:** Atiende tickets de servicio técnico y realiza mantenimientos.
* **Personal de Facturación:** Genera facturas y da seguimiento a pagos y cuentas por cobrar.

---

## 5. Historias de Usuario

1. Como Administrador de Contratos, quiero que el sistema me alerte 30 días antes del vencimiento de un contrato para poder gestionarlo con el cliente.
2. Como Encargado de Almacén, quiero escanear el número de serie de una impresora para asignarla a un contrato de renta.
3. Como Técnico de Campo, quiero ver mis tickets asignados ordenados por prioridad para organizar mi ruta diaria.
4. Como Personal de Facturación, quiero generar facturas masivas para contratos de renta el primer día del mes.
5. Como Gerente de Ventas, quiero generar cotizaciones y convertirlas en órdenes de venta.
6. Como Director General, quiero ver reportes de rentabilidad por cliente para evaluar su continuidad.
7. Como Encargado de Almacén, quiero recibir alertas cuando el stock de consumibles esté bajo.
8. Como Técnico de Campo, quiero registrar los materiales usados en cada servicio.
9. Como Personal de Facturación, quiero registrar pagos y ver el estado de cuenta del cliente.
10. Como Administrador de Contratos, quiero visualizar el historial de mantenimientos de cada impresora.
11. Como Director General, quiero visualizar métricas de eficiencia por técnico.
12. Como Gerente de Ventas, quiero consultar el historial de compras de un cliente.

---

## 6. Requerimientos Funcionales

**Gestión de Inventario:**

* Registro y seguimiento de equipos y consumibles por número de serie.
* Control de inventario en múltiples almacenes.
* Asignación de equipos a contratos de renta.
* Alertas por stock bajo.

**Gestión de Contratos de Renta:**

* Creación de plantillas de contratos.
* Asignación de equipos y volúmenes de impresión.
* Registro de lecturas de contadores.
* Historial de mantenimientos.

**Gestión de Ventas:**

* Flujo de cotización → orden de venta → remisión → factura.
* Registro de pagos parciales o completos.

**Facturación:**

* Facturación automática mensual de contratos.
* Facturación de ventas puntuales.
* Control de impuestos y cuentas por cobrar.

**Servicio Técnico:**

* Registro y asignación de tickets.
* Seguimiento de tiempos de atención.
* Registro de materiales utilizados y acciones realizadas.

**Reportes:**

* Reportes de rentabilidad por contrato.
* Eficiencia de técnicos.
* Inventario valorizado.
* Cuentas por cobrar.

---

## 7. Requerimientos No Funcionales

* **Rendimiento:** Las vistas deben cargar en menos de 3 segundos.
* **Seguridad:** Autenticación basada en roles. Cifrado de datos sensibles.
* **Usabilidad:** Interfaz intuitiva, en español, con mínima curva de aprendizaje.
* **Escalabilidad:** Capacidad para duplicar la carga en 3 años sin afectar el rendimiento.
* **Disponibilidad:** 99.8% de uptime mensual.

---

## 8. Flujos de Usuario

**Contrato de Renta:**

1. Crear cliente.
2. Crear contrato con equipo asignado y volúmenes de impresión.
3. Asignar impresora con número de serie.
4. Registrar lecturas mensuales.
5. Generar factura.
6. Renovar o cerrar contrato.

**Proceso de Venta:**

1. Crear cliente.
2. Generar cotización.
3. Confirmar orden de venta.
4. Generar remisión.
5. Facturar y registrar pago.

**Atención de Ticket de Servicio Técnico:**

1. Cliente reporta incidente.
2. Se genera ticket y se asigna técnico.
3. Técnico atiende servicio y registra detalles.
4. Se cierra ticket.

---

## 9. Requerimientos Técnicos

* **Backend:** Node.js con Express o Python con Django
* **Frontend:** Vue.js o React.js
* **Base de Datos:** PostgreSQL
* **Infraestructura:** Amazon Web Services (AWS) o Google Cloud Platform (GCP)
* **APIs:** Arquitectura basada en APIs RESTful

---

## 10. Plan de Implementación

**Fase 1 (MVP):**

* Módulo de CRM
* Módulo de Inventario

**Fase 2:**

* Módulo de Contratos de Renta
* Módulo de Facturación

**Fase 3:**

* Módulo de Servicio Técnico
* Módulo de Reportes

**Migración de Datos:**

* Importar clientes, productos, contratos activos y stock inicial desde hojas de cálculo.

**Capacitación:**

* Entrenamiento por rol: ventas, almacén, técnicos, administrativos.

---

## 11. Anexos

* Glosario de términos clave: "Contador", "Excedente", "Contrato de Renta", etc.
* Diagramas de arquitectura del sistema.
* Wireframes de las interfaces principales (si están disponibles).
