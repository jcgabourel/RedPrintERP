Documento de Requisitos del Producto (PRD): ERP RedPrint
Versión: 1.0
Fecha: 07 de agosto de 2024
Autor: Product Manager / Analista de Negocios
Estado: Borrador para Revisión

1. Contexto del Negocio y Objetivos
1.1. Contexto del Negocio

RedPrint es una empresa con un modelo de negocio híbrido que opera en dos frentes principales:

Renta de Impresoras Multifuncionales: Se ofrecen contratos de servicio a largo plazo para empresas, que incluyen el equipo, mantenimiento preventivo y correctivo, y el suministro de consumibles (tóner, refacciones). El cobro se basa en un costo fijo más un variable por página impresa (monocromática y a color).

Venta Directa: Comercialización de equipo de cómputo (laptops, desktops, servidores), periféricos y consumibles a una base de clientes similar.

Actualmente, la gestión operativa se apoya en herramientas descentralizadas como hojas de cálculo, correo electrónico y sistemas de contabilidad básicos, lo que genera silos de información, ineficiencias y falta de visibilidad estratégica.

1.2. Problemas a Resolver

Falta de Visibilidad del Inventario: Desconocimiento en tiempo real del stock de equipos, refacciones y consumibles, provocando retrasos en ventas y servicios.

Rentabilidad Desconocida: Imposibilidad de calcular con precisión la rentabilidad por contrato, cliente o equipo, al no poder contrastar los ingresos por impresión contra los costos operativos (consumibles, visitas técnicas).

Gestión de Cobranza Ineficiente: El proceso de cuentas por cobrar es manual, propenso a errores y carece de un seguimiento sistemático, afectando el flujo de efectivo.

Procesos Manuales y Redundantes: La creación de contratos, seguimiento de servicios técnicos y generación de facturas consumen una cantidad significativa de tiempo y recursos humanos.

Ausencia de Conciliación: No existe un proceso automatizado para conciliar los ingresos registrados con los depósitos bancarios.

1.3. Objetivos del Producto (Metas SMART)

Reducir las discrepancias de inventario en un 95% en los primeros 6 meses post-implementación.

Mejorar el ciclo de cobro en un 30% (reducir los días promedio de cuentas por cobrar) en el primer año.

Automatizar el 100% de la facturación recurrente basada en contratos de renta.

Obtener visibilidad en tiempo real sobre la rentabilidad de al menos el 80% de los contratos activos.

Reducir en un 40% el tiempo administrativo dedicado a la gestión de tickets de servicio y asignación de técnicos.

2. Resumen Ejecutivo
Este documento define los requisitos para el desarrollo de un sistema de Planificación de Recursos Empresariales (ERP) a medida para RedPrint. El objetivo principal es centralizar y automatizar las operaciones críticas de las dos líneas de negocio: renta de impresoras y venta de equipo. El ERP integrará módulos de Inventario, Contratos, Ventas, Facturación, Servicio Técnico y Reportes en una única plataforma. La implementación de este sistema proporcionará una visibilidad 360° del negocio, optimizará la asignación de recursos, mejorará la rentabilidad y sentará las bases para un crecimiento escalable y controlado.

3. Alcance del Proyecto
3.1. Funcionalidades Incluidas (In-Scope)

Módulo de Inventario: Gestión centralizada de todos los productos (equipos para renta/venta, refacciones, consumibles).

Módulo de Contratos de Renta: Ciclo de vida completo de los contratos, desde la creación hasta la renovación o terminación.

Módulo de Ventas: Gestión de clientes (CRM), cotizaciones y órdenes de venta para equipo y consumibles.

Módulo de Facturación: Generación de facturas para ambas líneas de negocio, seguimiento de cuentas por cobrar y conciliación básica.

Módulo de Servicio Técnico: Sistema de tickets para gestionar solicitudes de mantenimiento y reparación.

Módulo de Reportes y Dashboard: Visualización de KPIs clave y reportes operativos.

3.2. Funcionalidades Excluidas (Out-of-Scope para MVP)

Módulo de Contabilidad Completo: No se reemplazará el sistema contable existente; se buscará una integración futura.

Módulo de Recursos Humanos: La gestión de nómina y personal queda fuera del alcance.

Portal de Clientes: Un portal de autogestión para que los clientes levanten tickets o consulten facturas se considera una fase futura.

Integración con E-commerce: No se construirá una tienda en línea en esta fase.

App Móvil Nativa Avanzada: Aunque el sistema será responsivo (accesible desde móviles), una app nativa con funcionalidades offline avanzadas no está en el alcance inicial.

4. Stakeholders
Rol

Nombre

Responsabilidad en el Proyecto

Director General

(Nombre)

Patrocinador del proyecto, aprobación final.

Gerente de Operaciones

(Nombre)

Dueño del producto, define prioridades operativas.

Gerente de Ventas

(Nombre)

Valida el flujo de ventas y CRM.

Líder Técnico de Servicio

(Nombre)

Define los requisitos del módulo de servicio técnico.

Encargado de Almacén

(Nombre)

Valida el flujo de inventario.

Administrador/Contador

(Nombre)

Define los requisitos de facturación y cobranza.

5. Historias de Usuario
Como Gerente de Operaciones, quiero registrar un nuevo contrato de renta, asociando un cliente, un equipo del inventario y los costos por página, para automatizar su seguimiento y facturación.

Como Administrador, quiero que el sistema genere automáticamente las facturas mensuales de renta el día 1 de cada mes, basándose en las lecturas de contadores, para asegurar un flujo de cobro constante.

Como Técnico de Servicio, quiero recibir notificaciones en mi dispositivo móvil cuando se me asigne un nuevo ticket de servicio, para reducir mi tiempo de respuesta.

Como Técnico de Servicio, quiero consultar el stock de una refacción desde mi ubicación actual y registrar su uso en un ticket, para que el inventario se actualice en tiempo real.

Como Vendedor, quiero crear una cotización de venta de equipo de cómputo con precios y disponibilidad de inventario actualizados, para enviarla a un cliente potencial en minutos.

Como Encargado de Almacén, quiero registrar la entrada de nuevos productos al inventario mediante un número de serie único, para tener un control preciso de los activos.

Como Gerente de Operaciones, quiero ver un dashboard con la rentabilidad por contrato, para identificar clientes no rentables y tomar decisiones estratégicas.

Como Administrador, quiero registrar manualmente la lectura del contador de una impresora que no tiene conexión de red, para poder facturar correctamente.

Como Vendedor, quiero convertir una cotización aprobada en una orden de venta con un solo clic, para agilizar el proceso de facturación y entrega.

Como Administrador, quiero marcar una factura como "pagada" y registrar el método de pago, para llevar un control claro de las cuentas por cobrar.

Como Líder Técnico de Servicio, quiero ver un reporte de tickets abiertos, cerrados y el tiempo promedio de solución por técnico, para evaluar el desempeño del equipo.

Como Director General, quiero un reporte consolidado de ingresos mensuales, separando renta y venta directa, para tener una visión clara de la salud financiera del negocio.

6. Requerimientos Funcionales
6.1. Módulo de Inventario

(RF-INV-01) Creación de productos con SKU, número de serie (si aplica), descripción, costo, precio de venta y stock mínimo.

(RF-INV-02) Clasificación de productos: Equipo para renta, equipo para venta, refacciones, consumibles.

(RF-INV-03) Gestión de múltiples almacenes (ej. Almacén Central, Vehículo Técnico 1).

(RF-INV-04) Trazabilidad de movimientos de inventario (entradas, salidas, transferencias entre almacenes).

(RF-INV-05) Asignación de un equipo (por número de serie) a un contrato de renta.

6.2. Módulo de Contratos de Renta

(RF-CON-01) Creación de contratos con datos del cliente, fechas de inicio/fin, equipo(s) asignado(s).

(RF-CON-02) Definición de términos de facturación: renta base, volumen de páginas incluidas (monocromáticas/color), costo por página excedente.

(RF-CON-03) Registro de lecturas de contadores (manual o por integración futura).

(RF-CON-04) Historial de servicios y consumibles asociados a cada contrato.

6.3. Módulo de Ventas

(RF-VEN-01) Base de datos de clientes y prospectos (CRM).

(RF-VEN-02) Generación de cotizaciones en PDF con logo de la empresa.

(RF-VEN-03) Conversión de cotización a orden de venta.

(RF-VEN-04) Historial de ventas por cliente.

6.4. Módulo de Facturación

(RF-FAC-01) Generación de facturas a partir de contratos de renta (automatizada) y órdenes de venta (manual).

(RF-FAC-02) Integración con un Proveedor Autorizado de Certificación (PAC) para timbrado de CFDI 4.0.

(RF-FAC-03) Envío de facturas por correo electrónico al cliente.

(RF-FAC-04) Gestión de estatus de factura: Borrador, Timbrada, Enviada, Pagada, Cancelada.

(RF-FAC-05) Reporte de antigüedad de saldos.

6.5. Módulo de Servicio Técnico

(RF-ST-01) Creación de tickets de servicio asociados a un cliente y/o contrato.

(RF-ST-02) Asignación de tickets a técnicos disponibles.

(RF-ST-03) Registro de actividades en el ticket: diagnóstico, acciones realizadas, tiempo invertido.

(RF-ST-04) Posibilidad de adjuntar fotos o documentos al ticket.

(RF-ST-05) Cierre de ticket con firma de conformidad del cliente (digital).

6.6. Módulo de Reportes

(RF-REP-01) Dashboard principal con KPIs: Ingresos totales, Cuentas por Cobrar, Tickets Abiertos, Nivel de Inventario.

(RF-REP-02) Reporte de Rentabilidad por Contrato.

(RF-REP-03) Reporte de Valor de Inventario.

(RF-REP-04) Reporte de Ventas por Vendedor/Cliente.

7. Requerimientos No Funcionales
Categoría

Requerimiento

Rendimiento

- El tiempo de carga de cualquier página no debe exceder los 2 segundos. <br> - Las consultas a reportes complejos no deben exceder los 10 segundos.

Seguridad

- Sistema de autenticación basado en roles y permisos (Admin, Vendedor, Técnico, etc.). <br> - Todos los datos sensibles (ej. contraseñas) deben estar encriptados. <br> - Registro de auditoría (logs) para acciones críticas.

Usabilidad

- Interfaz limpia, intuitiva y consistente en todos los módulos. <br> - Diseño responsivo que garantice la operatividad en desktops, tablets y smartphones.

Escalabilidad

- La arquitectura debe soportar un incremento del 50% en usuarios y volumen de datos en los próximos 2 años sin degradación del rendimiento.

Disponibilidad

- El sistema debe tener un uptime del 99.8%, excluyendo ventanas de mantenimiento programadas.

8. Flujos de Usuario
8.1. Ciclo de Vida del Contrato de Renta

Vendedor/Operaciones: Crea un nuevo contrato en el sistema, selecciona el cliente y el equipo del inventario.

Sistema: El equipo se marca como "Asignado" y se descuenta del stock disponible para renta.

Operaciones: Se instala el equipo en el domicilio del cliente.

Cada mes: El administrador registra la lectura del contador.

Sistema: Calcula el monto a facturar (renta base + excedentes) y genera la factura.

Administrador: Timbra y envía la factura al cliente.

Técnico: Si hay un problema, se crea un ticket asociado al contrato. Se atiende y se cierra.

Al final del periodo: Operaciones decide si renovar o terminar el contrato.

8.2. Proceso de Venta Directa

Vendedor: Crea una cotización para un cliente, agregando productos del inventario.

Sistema: Verifica la disponibilidad de stock en tiempo real.

Vendedor: Envía la cotización en PDF al cliente.

Cliente: Aprueba la cotización.

Vendedor: Convierte la cotización en una orden de venta.

Administrador: Genera la factura a partir de la orden de venta.

Almacén: Prepara el pedido para envío, el sistema descuenta el stock.

8.3. Atención de Ticket de Servicio Técnico

Cliente/Operaciones: Reporta una falla. Se crea un ticket en el sistema, detallando el problema.

Líder Técnico: Asigna el ticket al técnico más cercano o con la especialidad requerida.

Sistema: Notifica al técnico sobre la nueva asignación.

Técnico: Acude al sitio, diagnostica el problema y registra sus hallazgos en el ticket.

Técnico: Si usa refacciones, las selecciona del inventario de su vehículo. El sistema las descuenta.

Técnico: Soluciona el problema y pide al cliente una firma de conformidad en su dispositivo.

Técnico: Cierra el ticket. El sistema actualiza el estatus y registra el tiempo de solución.

9. Requerimientos Técnicos
Pila Tecnológica Sugerida:

Backend: Node.js (NestJS) o Python (Django) por su robustez y ecosistema para APIs RESTful.

Frontend: React (con Next.js) o Vue.js (con Nuxt.js) por su madurez y capacidad para crear interfaces interactivas.

Base de Datos: PostgreSQL, por su fiabilidad, escalabilidad y soporte para datos complejos.

Infraestructura Cloud: AWS o Google Cloud Platform para gestionar servidores, bases de datos y almacenamiento de archivos (S3/Cloud Storage).

APIs e Integraciones:

API RESTful interna para la comunicación entre el frontend y el backend.

Integración obligatoria con un PAC para el timbrado de CFDI.

(Opcional a futuro) API para conectar con sistemas de monitoreo de impresoras.

10. Plan de Implementación
Fase 1: MVP - El Núcleo Operativo (3-4 meses)

Módulos: Inventario, Contratos de Renta, Facturación (manual y semi-automatizada para renta).

Objetivo: Centralizar el inventario y los contratos, y empezar a gestionar la facturación desde el sistema.

Migración: Migración inicial de datos maestros (clientes, inventario, contratos activos) desde hojas de cálculo.

Fase 2: Expansión Comercial y de Servicio (2-3 meses)

Módulos: Venta Directa (CRM, Cotizaciones), Servicio Técnico (Tickets).

Objetivo: Integrar al equipo de ventas y técnico a la plataforma.

Capacitación: Sesiones de capacitación intensivas para estos dos equipos.

Fase 3: Inteligencia de Negocio y Automatización (2 meses)

Módulos: Reportes Avanzados, Dashboard de KPIs, Automatización completa de facturación.

Objetivo: Empoderar a la dirección con datos para la toma de decisiones y finalizar la automatización de procesos.

Capacitación: Capacitación a gerencia y dirección sobre el uso de reportes.

11. Anexos
Glosario: Se adjuntará un documento con la definición de términos clave (ej. SKU, KPI, SLA, Contrato Activo).

Diagramas: Se adjuntarán diagramas de flujo de procesos (BPMN) y un diagrama de la arquitectura del sistema.

Wireframes: Se adjuntarán wireframes de baja fidelidad para las pantallas más importantes (Dashboard, Creación de Contrato, Vista de Ticket).