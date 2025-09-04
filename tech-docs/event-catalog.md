# Catálogo de Eventos de Dominio - ERP RedPrint

## 📋 Introducción

Este documento cataloga todos los eventos de dominio que cruzan los bounded contexts del sistema ERP RedPrint. Los eventos siguen el patrón de Event-Driven Architecture y permiten la comunicación asíncrona entre contextos.

## 🎯 Convenciones de Nomenclatura

- **Formato**: `[Entidad][Acción]Event`
- **Idioma**: Inglés (consistencia técnica)
- **Versionado**: `v1`, `v2` para cambios breaking

## 📊 Eventos por Categoría

### 🛒 Eventos de Negocio

#### VentaRealizadaEvent
- **Contexto Origen**: Sales Management
- **Contextos Destino**: Financial Management, Inventory Management, Reporting & Analytics
- **Payload**:
  ```json
  {
    "eventId": "uuid",
    "eventVersion": "1.0",
    "timestamp": "iso8601",
    "saleId": "int",
    "customerId": "int",
    "totalAmount": "decimal",
    "items": [
      {
        "productId": "int",
        "quantity": "int",
        "unitPrice": "decimal"
      }
    ]
  }
  ```
- **Descripción**: Se emite cuando una venta es confirmada y facturada

#### ContratoActivadoEvent
- **Contexto Origen**: Contract Management  
- **Contextos Destino**: Financial Management, Inventory Management
- **Payload**:
  ```json
  {
    "eventId": "uuid",
    "eventVersion": "1.0",
    "timestamp": "iso8601",
    "contractId": "int",
    "customerId": "int",
    "startDate": "date",
    "endDate": "date",
    "baseRent": "decimal",
    "equipmentIds": ["int"]
  }
  ```
- **Descripción**: Se emite cuando un contrato de renta es activado

#### CompraRegistradaEvent
- **Contexto Origen**: Purchase Management
- **Contextos Destino**: Financial Management, Inventory Management
- **Payload**:
  ```json
  {
    "eventId": "uuid", 
    "eventVersion": "1.0",
    "timestamp": "iso8601",
    "purchaseId": "int",
    "supplierId": "int",
    "totalAmount": "decimal",
    "items": [
      {
        "productId": "int",
        "quantity": "int",
        "unitCost": "decimal"
      }
    ]
  }
  ```
- **Descripción**: Se emite cuando una compra es registrada y recibida

### 🔧 Eventos de Servicio Técnico

#### TicketCerradoEvent
- **Contexto Origen**: Technical Service
- **Contextos Destino**: Inventory Management, Financial Management, Reporting & Analytics
- **Payload**:
  ```json
  {
    "eventId": "uuid",
    "eventVersion": "1.0", 
    "timestamp": "iso8601",
    "ticketId": "int",
    "customerId": "int",
    "contractId": "int?",
    "timeSpent": "int",
    "partsUsed": [
      {
        "productId": "int",
        "quantity": "int"
      }
    ],
    "digitalSignature": "string?"
  }
  ```
- **Descripción**: Se emite cuando un ticket de servicio técnico es cerrado

### 📦 Eventos de Inventario

#### InventarioActualizadoEvent
- **Contexto Origen**: Inventory Management
- **Contextos Destino**: Todos los contextos (Published Language)
- **Payload**:
  ```json
  {
    "eventId": "uuid",
    "eventVersion": "1.0",
    "timestamp": "iso8601",
    "productId": "int",
    "warehouseId": "int",
    "movementType": "IN|OUT|ADJUSTMENT",
    "quantity": "int",
    "newStock": "int",
    "relatedDocumentType": "SALE|PURCHASE|TICKET|ADJUSTMENT",
    "relatedDocumentId": "int"
  }
  ```
- **Descripción**: Se emite por cualquier movimiento de inventario

#### StockBajoEvent
- **Contexto Origen**: Inventory Management
- **Contextos Destino**: Sales Management, Purchase Management
- **Payload**:
  ```json
  {
    "eventId": "uuid",
    "eventVersion": "1.0",
    "timestamp": "iso8601", 
    "productId": "int",
    "currentStock": "int",
    "minStock": "int",
    "warehouseId": "int"
  }
  ```
- **Descripción**: Se emite cuando el stock cae below del mínimo configurado

### 💰 Eventos Financieros

#### PagoRegistradoEvent
- **Contexto Origen**: Financial Management
- **Contextos Destino**: Todos los contextos relevantes
- **Payload**:
  ```json
  {
    "eventId": "uuid",
    "eventVersion": "1.0",
    "timestamp": "iso8601",
    "paymentId": "int",
    "type": "CUSTOMER_PAYMENT|SUPPLIER_PAYMENT",
    "amount": "decimal",
    "currency": "MXN",
    "referenceNumber": "string",
    "relatedDocuments": [
      {
        "type": "INVOICE|RECEIVABLE|PAYABLE",
        "id": "int"
      }
    ]
  }
  ```
- **Descripción**: Se emite cuando se registra un pago o cobro

#### FacturaEmitidaEvent
- **Contexto Origen**: Financial Management
- **Contextos Destino**: Sales Management, Contract Management, Reporting & Analytics
- **Payload**:
  ```json
  {
    "eventId": "uuid",
    "eventVersion": "1.0",
    "timestamp": "iso8601",
    "invoiceId": "int",
    "type": "SALE|RENTAL",
    "customerId": "int",
    "totalAmount": "decimal",
    "xmlUuid": "string",
    "relatedDocumentId": "int"
  }
  ```
- **Descripción**: Se emite cuando se genera una factura XML

#### FacturaRecibidaEvent
- **Contexto Origen**: Financial Management
- **Contextos Destino**: Purchase Management, Reporting & Analytics
- **Payload**:
  ```json
  {
    "eventId": "uuid",
    "eventVersion": "1.0",
    "timestamp": "iso8601",
    "invoiceId": "int",
    "supplierId": "int",
    "totalAmount": "decimal", 
    "xmlUuid": "string",
    "relatedDocumentId": "int"
  }
  ```
- **Descripción**: Se emite cuando se recibe una factura XML de proveedor

### 🔄 Eventos de Conciliación

#### ConciliacionIniciadaEvent
- **Contexto Origen**: Financial Management
- **Contextos Destino**: Todos los contextos
- **Payload**:
  ```json
  {
    "eventId": "uuid",
    "eventVersion": "1.0",
    "timestamp": "iso8601",
    "processId": "int",
    "period": "YYYY-MM",
    "startDate": "date",
    "endDate": "date"
  }
  ```
- **Descripción**: Se emite al iniciar un proceso de conciliación mensual

#### ConciliacionItemMatchedEvent
- **Contexto Origen**: Financial Management
- **Contextos Destino**: Reporting & Analytics
- **Payload**:
  ```json
  {
    "eventId": "uuid",
    "eventVersion": "1.0",
    "timestamp": "iso8601",
    "processId": "int",
    "itemType": "BANK|INVOICE|INVENTORY|PAYMENT",
    "itemId": "int",
    "matchConfidence": "HIGH|MEDIUM|LOW",
    "notes": "string?"
  }
  ```
- **Descripción**: Se emite cuando un ítem es conciliado exitosamente

#### ConciliacionCompletaEvent
- **Contexto Origen**: Financial Management
- **Contextos Destino**: Todos los contextos, Reporting & Analytics
- **Payload**:
  ```json
  {
    "eventId": "uuid",
    "eventVersion": "1.0",
    "timestamp": "iso8601",
    "processId": "int",
    "status": "COMPLETED|PARTIAL|WITH_ISSUES",
    "matchedItems": "int",
    "unmatchedItems": "int",
    "totalItems": "int"
  }
  ```
- **Descripción**: Se emite al completar un proceso de conciliación

### 📈 Eventos de Reportes

#### ReporteGeneradoEvent
- **Contexto Origen**: Reporting & Analytics
- **Contextos Destino**: UI/Frontend
- **Payload**:
  ```json
  {
    "eventId": "uuid",
    "eventVersion": "1.0",
    "timestamp": "iso8601",
    "reportId": "int",
    "reportType": "SALES|INVENTORY|FINANCIAL|RECONCILIATION",
    "period": "YYYY-MM",
    "format": "PDF|EXCEL|HTML",
    "downloadUrl": "string"
  }
  ```
- **Descripción**: Se emite cuando un reporte es generado exitosamente

#### DashboardActualizadoEvent
- **Contexto Origen**: Reporting & Analytics
- **Contextos Destino**: UI/Frontend
- **Payload**:
  ```json
  {
    "eventId": "uuid",
    "eventVersion": "1.0",
    "timestamp": "iso8601",
    "dashboardId": "int",
    "updateType": "REALTIME|HOURLY|DAILY",
    "affectedMetrics": ["string"]
  }
  ```
- **Descripción**: Se emite cuando el dashboard es actualizado con nuevos datos

## 🛠️ Implementación Técnica

### Serialización
- **Formato**: JSON
- **Encoding**: UTF-8
- **Compresión**: Opcional (gzip para volumen alto)

### Transporte
- **Protocolo**: AMQP 1.0/RabbitMQ recomendado
- **Persistencia**: Event Store opcional para auditing
- **Retry**: Exponential backoff con dead letter queue

### Seguridad
- **Encripción**: TLS 1.3+ para transporte
- **Autenticación**: JWT tokens por servicio
- **Autorización**: RBAC basado en claims

## 📈 Métricas y Monitoreo

- **Latencia**: < 100ms para eventos críticos
- **Throughput**: 1000+ eventos/segundo
- **Disponibilidad**: 99.95% SLA
- **Retención**: 30 días para debugging, 1 año para auditoría

---
*Última actualización: ${new Date().toISOString().split('T')[0]}*