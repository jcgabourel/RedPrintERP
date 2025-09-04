# Mapa de Contextos (Context Map) - ERP RedPrint

## 1. Contextos Acotados (Bounded Contexts)

### 1.1 Contexto de Gestión de Clientes (Customer Management)
- **Dominio Core**: Gestión de información de clientes y su relación con la empresa
- **Agregados**:
  - Cliente (Customer)
  - Información de Contacto (ContactInfo)
  - Información Fiscal (TaxInfo)
- **Entidades**:
  - Customer
  - CustomerProfile
- **Value Objects**:
  - RFC
  - Email
  - PhoneNumber
  - Address

### 1.2 Contexto de Contratos (Contract Management)
- **Dominio Core**: Gestión del ciclo de vida de contratos de renta de equipos
- **Agregados**:
  - Contrato (Contract)
  - Equipo Asignado (AssignedEquipment)
  - Lecturas de Contador (MeterReading)
  - Términos de Contrato (ContractTerms)
- **Entidades**:
  - Contract
  - MeterReading
  - ContractTerms
- **Value Objects**:
  - ContractPeriod
  - RentalFee
  - PageCount
  - OverageCost

### 1.3 Contexto de Inventario (Inventory Management)
- **Dominio Core**: Control y trazabilidad de productos y equipos
- **Agregados**:
  - Producto (Product)
  - Almacén (Warehouse)
  - Movimiento de Inventario (InventoryMovement)
  - Categoría (Category)
  - Marca (Brand)
  - Unidad (Unit)
- **Entidades**:
  - Product
  - Warehouse
  - StockMovement
  - Category
  - Brand
  - Unit
- **Value Objects**:
  - SKU
  - SerialNumber
  - StockQuantity
  - ProductCategory
  - Location

### 1.4 Contexto de Ventas (Sales Management)
- **Dominio Core**: Gestión de ventas directas y cotizaciones
- **Agregados**:
  - Venta (Sale)
  - Cotización (Quotation)
  - Línea de Venta (SaleItem)
- **Entidades**:
  - Sale
  - Quotation
  - SaleItem
- **Value Objects**:
  - Price
  - Quantity
  - SaleStatus
  - QuotationNumber

### 1.5 Contexto de Compras (Purchase Management)
- **Dominio Core**: Gestión de compras y proveedores
- **Agregados**:
  - Compra (Purchase)
  - Proveedor (Supplier)
  - Línea de Compra (PurchaseItem)
- **Entidades**:
  - Purchase
  - Supplier
  - PurchaseItem
- **Value Objects**:
  - Cost
  - PurchaseStatus
  - SupplierCode

### 1.6 Contexto de Servicio Técnico (Technical Service)
- **Dominio Core**: Gestión de tickets de servicio y mantenimiento
- **Agregados**:
  - Ticket
  - Actividad de Servicio (ServiceActivity)
  - Refacciones Usadas (UsedParts)
- **Entidades**:
  - ServiceTicket
  - TechnicianAssignment
  - ServiceActivity
- **Value Objects**:
  - TicketStatus
  - DiagnosisReport
  - TimeSpent
  - DigitalSignature

### 1.7 Contexto de Gestión Financiera (Financial Management)
- **Dominio Core**: Gestión integral de finanzas, contabilidad, cuentas por cobrar/pagar y conciliación
- **Agregados**:
  - Cuenta por Cobrar (AccountReceivable)
  - Cuenta por Pagar (AccountPayable)
  - Movimiento Bancario (BankMovement)
  - Proceso de Conciliación (ReconciliationProcess)
  - Factura (Invoice)
  - Pago (Payment)
- **Entidades**:
  - AccountReceivable
  - AccountPayable
  - BankMovement
  - ReconciliationProcess
  - ReconciliationItem
  - Invoice
  - Payment
- **Value Objects**:
  - Amount
  - Balance
  - DueDate
  - ReconciliationStatus
  - MatchConfidence
  - PeriodoConciliacion

### 1.8 Contexto de Reportes y Analíticas (Reporting & Analytics)
- **Dominio Core**: Generación de informes, dashboards y análisis de datos para la toma de decisiones.
- **Agregados**:
  - Reporte (Report)
  - Dashboard (Dashboard)
  - KPI (KeyPerformanceIndicator)
- **Entidades**:
  - Report
  - Dashboard
  - KPI
- **Value Objects**:
  - ReportPeriod
  - MetricValue
  - ChartType

## 2. Relaciones entre Contextos

### 2.1 Relaciones Upstream-Downstream
1. **Inventario → Ventas**
   - Inventario es upstream de Ventas
   - Conformist: Ventas se adapta al modelo de Inventario

2. **Inventario → Servicio Técnico**
   - Inventario es upstream de Servicio Técnico
   - Customer-Supplier: Coordinación para control de refacciones

3. **Ventas → Gestión Financiera**
   - Ventas es upstream de Gestión Financiera
   - Conformist: Gestión Financiera se adapta a los eventos de Ventas

4. **Contratos → Gestión Financiera**
   - Contratos es upstream de Gestión Financiera
   - Open Host Service: Contratos proporciona API para facturación

5. **Compras → Gestión Financiera**
   - Compras es upstream de Gestión Financiera
   - Conformist: Gestión Financiera se adapta a los eventos de Compras

6. **Gestión Financiera → Reportes y Analíticas**
   - Gestión Financiera es upstream de Reportes y Analíticas
   - Published Language: Gestión Financiera publica datos financieros para reportes

7. **Todos los Contextos → Reportes y Analíticas** (Published Language)
   - Todos los contextos publican datos relevantes para Reportes y Analíticas


### 2.2 Relaciones Partnership
1. **Clientes ↔ Contratos**
   - Colaboración cercana para gestión de relaciones comerciales

2. **Ventas ↔ Clientes**
   - Compartición de información de cliente y historial comercial

3. **Servicio Técnico ↔ Contratos**
   - Coordinación para mantenimiento y soporte de equipos rentados

### 2.3 Shared Kernel
- **Kernel Compartido de Facturación y Pagos**
  - Compartido entre Ventas, Contratos, Compras y Gestión Financiera
  - Incluye modelos comunes de facturación, pagos y documentos fiscales (XML)

## 3. Políticas Anti-Corrupción

### 3.1 Traducción de Datos
- Implementar traductores entre:
  - Inventario → Ventas (para disponibilidad de productos)
  - Ventas/Contratos/Compras → Gestión Financiera (para facturación y pagos)
  - Servicio Técnico → Inventario (para control de refacciones)

### 3.2 Eventos de Dominio
- Eventos clave que cruzan contextos:
  - **Eventos de Negocio:**
    - VentaRealizadaEvent (Ventas → Gestión Financiera + Inventario)
    - ContratoActivadoEvent (Contratos → Gestión Financiera + Inventario)
    - CompraRegistradaEvent (Compras → Gestión Financiera + Inventario)
    - TicketCerradoEvent (Servicio Técnico → Inventario + Gestión Financiera)
  
  - **Eventos de Inventario:**
    - InventarioActualizadoEvent (Inventario → Todos los contextos)
    - StockBajoEvent (Inventario → Ventas/Compras)
  
  - **Eventos Financieros:**
    - PagoRegistradoEvent (Gestión Financiera → Todos los contextos)
    - FacturaEmitidaEvent (Gestión Financiera → Ventas/Contratos)
    - FacturaRecibidaEvent (Gestión Financiera → Compras)
    - ConciliacionIniciadaEvent (Gestión Financiera → Todos)
    - ConciliacionItemMatchedEvent (Gestión Financiera → Reportes)
    - ConciliacionCompletaEvent (Gestión Financiera → Reportes + Todos)
  
  - **Eventos de Reportes:**
    - ReporteGeneradoEvent (Reportes → Todos)
    - DashboardActualizadoEvent (Reportes → UI)

## 4. Integraciones Técnicas

### 4.1 APIs y Servicios
- RESTful APIs para comunicación entre contextos
- Event Bus para publicación/suscripción de eventos de dominio
- Servicios de Aplicación para orquestar flujos que cruzan contextos

### 4.2 Consistencia y Transacciones
- Consistencia eventual entre contextos
- Transacciones ACID dentro de cada contexto
- Saga Pattern para operaciones que cruzan múltiples contextos

## 5. Consideraciones de Implementación

### 5.1 Estructura de Carpetas Sugerida
```
src/
├── Shared/
│   ├── Domain/
│   └── Infrastructure/
├── CustomerManagement/
├── ContractManagement/
├── InventoryManagement/
├── SalesManagement/
├── PurchaseManagement/
├── TechnicalService/
├── FinancialManagement/
└── ReportingAndAnalytics/
```

### 5.2 Patrones Recomendados
- Repository Pattern para persistencia
- Factory Pattern para creación de agregados
- Specification Pattern para reglas de negocio
- CQRS para operaciones de lectura/escritura
- Event Sourcing para trazabilidad

### 5.3 Tecnologías Sugeridas
- Laravel para implementación de bounded contexts
- Event Bus para comunicación asíncrona
- PostgreSQL para persistencia
- Redis para caché y mensajería
- API Gateway para exposición de servicios
