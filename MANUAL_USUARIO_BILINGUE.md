# Manual de Usuario - Pizzería Dominico
## User Manual - Pizzería Dominico

---

## 🇪🇸 MANUAL EN ESPAÑOL

### 📋 Tabla de Contenidos
1. [Introducción](#introducción)
2. [Acceso al Sistema](#acceso-al-sistema)
3. [Registro de Usuario](#registro-de-usuario)
4. [Inicio de Sesión](#inicio-de-sesión)
5. [Navegación Principal](#navegación-principal)
6. [Funcionalidades para Clientes](#funcionalidades-para-clientes)
7. [Panel Administrativo](#panel-administrativo)
8. [Gestión de Pedidos](#gestión-de-pedidos)
9. [Gestión de Menús](#gestión-de-menús)
10. [Gestión de Usuarios](#gestión-de-usuarios)
11. [Solución de Problemas](#solución-de-problemas)

---

### 1. Introducción

**Pizzería Dominico** es una aplicación web que permite a los clientes realizar pedidos en línea y a los administradores gestionar el negocio de manera eficiente. El sistema está diseñado para ser intuitivo y fácil de usar.

#### Características Principales:
- **Pedidos en línea**: Los clientes pueden navegar por el menú y realizar pedidos
- **Gestión de usuarios**: Sistema de roles con diferentes niveles de acceso
- **Panel administrativo**: Herramientas completas para gestionar el negocio
- **Carrito de compras**: Funcionalidad completa de e-commerce
- **Gestión de pedidos**: Seguimiento y actualización del estado de pedidos

---

### 2. Acceso al Sistema

#### URL del Sistema:
- **Desarrollo**: `http://localhost/PizzeriaDominico`
- **Producción**: `https://pizzeriadominico.com`

#### Requisitos del Navegador:
- Chrome, Firefox, Safari o Edge (versiones recientes)
- JavaScript habilitado
- Cookies habilitadas

---

### 3. Registro de Usuario

#### Pasos para Registrarse:

1. **Acceder a la página de registro**:
   - Hacer clic en el ícono de usuario en la barra de navegación
   - Seleccionar "Registrarse" si no tienes cuenta

2. **Completar el formulario**:
   - **Nombre completo**: Tu nombre y apellido
   - **Teléfono**: Número de contacto (opcional)
   - **Correo electrónico**: Email válido (será tu usuario)
   - **Dirección**: Dirección de entrega predeterminada
   - **Contraseña**: Mínimo 6 caracteres

3. **Confirmar registro**:
   - Hacer clic en "Registrarse"
   - El sistema te redirigirá automáticamente al perfil

#### Notas Importantes:
- El correo electrónico debe ser único
- La contraseña se almacena de forma segura (encriptada)
- Todos los usuarios nuevos tienen rol de "Cliente" por defecto

---

### 4. Inicio de Sesión

#### Pasos para Iniciar Sesión:

1. **Acceder a la página de login**:
   - Hacer clic en el ícono de usuario en la barra de navegación
   - Seleccionar "Iniciar Sesión"

2. **Ingresar credenciales**:
   - **Correo electrónico**: El email registrado
   - **Contraseña**: Tu contraseña

3. **Confirmar acceso**:
   - Hacer clic en "Iniciar Sesión"
   - El sistema te redirigirá según tu rol:
     - **Cliente**: Página de perfil
     - **Administrador/Vendedor**: Panel administrativo

#### Recuperación de Contraseña:
- Contactar al administrador del sistema
- No hay funcionalidad automática de recuperación implementada

---

### 5. Navegación Principal

#### Barra de Navegación:
- **Logo**: Regresa a la página principal
- **Inicio**: Página de bienvenida con información de la pizzería
- **Tienda**: Catálogo de productos disponibles
- **Carrito**: Ver productos seleccionados (ícono con contador)
- **Usuario**: Acceso a perfil o panel administrativo

#### Navegación Responsiva:
- En dispositivos móviles, el menú se adapta automáticamente
- El carrito mantiene su funcionalidad en todas las pantallas

---

### 6. Funcionalidades para Clientes

#### 6.1 Explorar la Tienda

1. **Acceder a la tienda**:
   - Hacer clic en "Tienda" en la navegación
   - O usar el botón "Ordenar ahora" en la página principal

2. **Navegar por categorías**:
   - Los productos están organizados por familias (Pizzas, Bebidas, etc.)
   - Usar las flechas para navegar entre productos
   - Hacer clic en un producto para ver detalles

3. **Ver detalles del producto**:
   - Modal con imagen, descripción y precio
   - Botón "Agregar al carrito" con selector de cantidad

#### 6.2 Gestión del Carrito

1. **Agregar productos**:
   - Desde la tienda, hacer clic en "Agregar al carrito"
   - Seleccionar cantidad deseada
   - El producto se añade automáticamente

2. **Ver carrito**:
   - Hacer clic en el ícono del carrito (muestra cantidad)
   - Ver lista de productos seleccionados
   - Modificar cantidades o eliminar productos

3. **Proceder al pago**:
   - Hacer clic en "Continuar con el pedido"
   - Completar información de entrega y pago

#### 6.3 Realizar un Pedido

1. **Completar información**:
   - **Dirección de entrega**: Confirmar o modificar dirección
   - **Método de pago**: Seleccionar entre opciones disponibles
   - **Observaciones**: Notas especiales para el pedido

2. **Revisar pedido**:
   - Ver resumen de productos y precios
   - Confirmar total del pedido
   - Hacer clic en "Confirmar Pedido"

3. **Confirmación**:
   - El sistema procesa el pedido
   - Se muestra número de pedido y estado
   - Recibirás confirmación por email (si está configurado)

#### 6.4 Gestión del Perfil

1. **Ver información personal**:
   - Acceder desde el ícono de usuario
   - Ver datos registrados y último pedido

2. **Editar dirección**:
   - Hacer clic en "Editar dirección"
   - Modificar información de entrega
   - Guardar cambios

3. **Gestionar pedidos**:
   - Ver último pedido realizado
   - Modificar pedido pendiente (si aplica)
   - Cancelar pedido (si está pendiente)

---

### 7. Panel Administrativo

#### 7.1 Acceso al Panel

**Roles con acceso**:
- **Super Administrador** (ID_Rol = 3): Acceso completo
- **Administrador/Vendedor** (ID_Rol = 2): Acceso limitado

**Funcionalidades por rol**:
- **Super Administrador**: Gestión de usuarios, pedidos y menús
- **Administrador/Vendedor**: Solo gestión de pedidos y menús

#### 7.2 Dashboard Principal

1. **Métricas generales**:
   - Resumen de pedidos del día
   - Estadísticas de ventas
   - Estado de pedidos pendientes

2. **Accesos rápidos**:
   - Gestión de pedidos
   - Gestión de menús
   - Gestión de usuarios (solo Super Admin)

---

### 8. Gestión de Pedidos

#### 8.1 Ver Todos los Pedidos

1. **Acceder a gestión de pedidos**:
   - Desde el panel administrativo
   - Hacer clic en "Gestión de Pedidos"

2. **Filtrar pedidos**:
   - Ver todos los pedidos ordenados por fecha
   - Estados disponibles: Pendiente, Confirmado, Preparando, Enviado, Entregado, Cancelado

#### 8.2 Actualizar Estado de Pedido

1. **Seleccionar pedido**:
   - Hacer clic en el pedido deseado
   - Ver detalles completos del pedido

2. **Cambiar estado**:
   - Seleccionar nuevo estado del dropdown
   - Confirmar cambio
   - El sistema actualiza automáticamente

#### 8.3 Ver Detalles de Pedido

1. **Información del cliente**:
   - Nombre y datos de contacto
   - Dirección de entrega

2. **Detalles del pedido**:
   - Lista de productos con cantidades
   - Precios individuales y total
   - Método de pago seleccionado
   - Observaciones del cliente

---

### 9. Gestión de Menús

#### 9.1 Gestionar Familias de Productos

1. **Crear nueva familia**:
   - Hacer clic en "Agregar Familia"
   - Ingresar nombre y descripción
   - Guardar cambios

2. **Editar familia existente**:
   - Hacer clic en el nombre de la familia
   - Modificar información
   - Guardar cambios

3. **Eliminar familia**:
   - Hacer clic en el botón de eliminar
   - Confirmar eliminación
   - **Nota**: Solo se puede eliminar si no tiene productos

#### 9.2 Gestionar Productos

1. **Agregar producto**:
   - Seleccionar familia de destino
   - Hacer clic en "Agregar Producto"
   - Completar formulario:
     - Nombre del producto
     - Descripción
     - Precio
     - Imagen (opcional)

2. **Editar producto**:
   - Hacer clic en el producto deseado
   - Modificar información
   - Actualizar imagen si es necesario
   - Guardar cambios

3. **Eliminar producto**:
   - Hacer clic en el botón de eliminar
   - Confirmar eliminación
   - **Nota**: El producto se eliminará de todos los pedidos

#### 9.3 Asignar Productos a Familias

1. **Seleccionar familia**:
   - Hacer clic en el nombre de la familia
   - Ver productos asignados

2. **Agregar producto a familia**:
   - Hacer clic en "Agregar Producto"
   - Seleccionar producto existente
   - Confirmar asignación

---

### 10. Gestión de Usuarios (Solo Super Administrador)

#### 10.1 Ver Todos los Usuarios

1. **Acceder a gestión de usuarios**:
   - Desde el panel administrativo
   - Hacer clic en "Gestión de Usuarios"

2. **Información mostrada**:
   - Nombre del usuario
   - Correo electrónico
   - Rol actual
   - Opciones de gestión

#### 10.2 Cambiar Rol de Usuario

1. **Seleccionar usuario**:
   - Encontrar el usuario en la lista
   - Usar el dropdown de roles

2. **Roles disponibles**:
   - **Cliente**: Acceso básico al sistema
   - **Vendedor**: Gestión de pedidos y menús
   - **Super Administrador**: Acceso completo

3. **Confirmar cambio**:
   - El cambio se aplica inmediatamente
   - El usuario debe reiniciar sesión para ver cambios

#### 10.3 Eliminar Usuario

1. **Seleccionar usuario**:
   - Hacer clic en el botón "Eliminar"
   - Confirmar eliminación

2. **Consideraciones**:
   - Se eliminan todos los pedidos del usuario
   - La acción no se puede deshacer
   - Solo eliminar usuarios sin pedidos importantes

---

### 11. Solución de Problemas

#### 11.1 Problemas de Acceso

**No puedo iniciar sesión**:
- Verificar correo electrónico y contraseña
- Asegurarse de que JavaScript esté habilitado
- Limpiar caché del navegador
- Contactar al administrador

**Error de permisos**:
- Verificar que tu rol tenga acceso a la funcionalidad
- Contactar al Super Administrador

#### 11.2 Problemas con Pedidos

**No se puede agregar al carrito**:
- Verificar conexión a internet
- Recargar la página
- Limpiar cookies del sitio

**Error al procesar pedido**:
- Verificar información de dirección
- Asegurarse de tener productos en el carrito
- Intentar nuevamente

#### 11.3 Problemas del Panel Administrativo

**No se cargan los datos**:
- Verificar conexión a base de datos
- Recargar la página
- Verificar permisos de usuario

**Error al actualizar información**:
- Verificar que todos los campos requeridos estén completos
- Asegurarse de tener permisos de edición
- Contactar al administrador del sistema

---

## 🇺🇸 ENGLISH MANUAL

### 📋 Table of Contents
1. [Introduction](#introduction)
2. [System Access](#system-access)
3. [User Registration](#user-registration)
4. [Login](#login)
5. [Main Navigation](#main-navigation)
6. [Customer Features](#customer-features)
7. [Administrative Panel](#administrative-panel)
8. [Order Management](#order-management)
9. [Menu Management](#menu-management)
10. [User Management](#user-management)
11. [Troubleshooting](#troubleshooting)

---

### 1. Introduction

**Pizzería Dominico** is a web application that allows customers to place orders online and administrators to manage the business efficiently. The system is designed to be intuitive and easy to use.

#### Main Features:
- **Online Orders**: Customers can browse the menu and place orders
- **User Management**: Role-based system with different access levels
- **Administrative Panel**: Complete tools for business management
- **Shopping Cart**: Full e-commerce functionality
- **Order Management**: Order tracking and status updates

---

### 2. System Access

#### System URL:
- **Development**: `http://localhost/PizzeriaDominico`
- **Production**: `https://pizzeriadominico.com`

#### Browser Requirements:
- Chrome, Firefox, Safari or Edge (recent versions)
- JavaScript enabled
- Cookies enabled

---

### 3. User Registration

#### Steps to Register:

1. **Access registration page**:
   - Click on the user icon in the navigation bar
   - Select "Register" if you don't have an account

2. **Complete the form**:
   - **Full Name**: Your first and last name
   - **Phone**: Contact number (optional)
   - **Email**: Valid email address (will be your username)
   - **Address**: Default delivery address
   - **Password**: Minimum 6 characters

3. **Confirm registration**:
   - Click "Register"
   - The system will automatically redirect you to your profile

#### Important Notes:
- Email address must be unique
- Password is stored securely (encrypted)
- All new users have "Customer" role by default

---

### 4. Login

#### Steps to Login:

1. **Access login page**:
   - Click on the user icon in the navigation bar
   - Select "Login"

2. **Enter credentials**:
   - **Email**: Registered email address
   - **Password**: Your password

3. **Confirm access**:
   - Click "Login"
   - The system will redirect you according to your role:
     - **Customer**: Profile page
     - **Administrator/Seller**: Administrative panel

#### Password Recovery:
- Contact the system administrator
- No automatic recovery functionality is implemented

---

### 5. Main Navigation

#### Navigation Bar:
- **Logo**: Returns to the main page
- **Home**: Welcome page with pizzeria information
- **Store**: Available products catalog
- **Cart**: View selected products (icon with counter)
- **User**: Access to profile or administrative panel

#### Responsive Navigation:
- On mobile devices, the menu adapts automatically
- Cart maintains its functionality on all screens

---

### 6. Customer Features

#### 6.1 Explore the Store

1. **Access the store**:
   - Click "Store" in the navigation
   - Or use the "Order now" button on the main page

2. **Browse categories**:
   - Products are organized by families (Pizzas, Beverages, etc.)
   - Use arrows to navigate between products
   - Click on a product to see details

3. **View product details**:
   - Modal with image, description and price
   - "Add to cart" button with quantity selector

#### 6.2 Cart Management

1. **Add products**:
   - From the store, click "Add to cart"
   - Select desired quantity
   - Product is automatically added

2. **View cart**:
   - Click on the cart icon (shows quantity)
   - View list of selected products
   - Modify quantities or remove products

3. **Proceed to payment**:
   - Click "Continue with order"
   - Complete delivery and payment information

#### 6.3 Place an Order

1. **Complete information**:
   - **Delivery address**: Confirm or modify address
   - **Payment method**: Select from available options
   - **Observations**: Special notes for the order

2. **Review order**:
   - View product summary and prices
   - Confirm order total
   - Click "Confirm Order"

3. **Confirmation**:
   - The system processes the order
   - Order number and status are displayed
   - You will receive email confirmation (if configured)

#### 6.4 Profile Management

1. **View personal information**:
   - Access from the user icon
   - View registered data and last order

2. **Edit address**:
   - Click "Edit address"
   - Modify delivery information
   - Save changes

3. **Manage orders**:
   - View last order placed
   - Modify pending order (if applicable)
   - Cancel order (if pending)

---

### 7. Administrative Panel

#### 7.1 Panel Access

**Roles with access**:
- **Super Administrator** (ID_Rol = 3): Full access
- **Administrator/Seller** (ID_Rol = 2): Limited access

**Functionality by role**:
- **Super Administrator**: User, order and menu management
- **Administrator/Seller**: Only order and menu management

#### 7.2 Main Dashboard

1. **General metrics**:
   - Daily order summary
   - Sales statistics
   - Pending order status

2. **Quick access**:
   - Order management
   - Menu management
   - User management (Super Admin only)

---

### 8. Order Management

#### 8.1 View All Orders

1. **Access order management**:
   - From the administrative panel
   - Click "Order Management"

2. **Filter orders**:
   - View all orders sorted by date
   - Available states: Pending, Confirmed, Preparing, Sent, Delivered, Cancelled

#### 8.2 Update Order Status

1. **Select order**:
   - Click on the desired order
   - View complete order details

2. **Change status**:
   - Select new status from dropdown
   - Confirm change
   - System updates automatically

#### 8.3 View Order Details

1. **Customer information**:
   - Name and contact data
   - Delivery address

2. **Order details**:
   - Product list with quantities
   - Individual prices and total
   - Selected payment method
   - Customer observations

---

### 9. Menu Management

#### 9.1 Manage Product Families

1. **Create new family**:
   - Click "Add Family"
   - Enter name and description
   - Save changes

2. **Edit existing family**:
   - Click on the family name
   - Modify information
   - Save changes

3. **Delete family**:
   - Click the delete button
   - Confirm deletion
   - **Note**: Can only be deleted if it has no products

#### 9.2 Manage Products

1. **Add product**:
   - Select destination family
   - Click "Add Product"
   - Complete form:
     - Product name
     - Description
     - Price
     - Image (optional)

2. **Edit product**:
   - Click on the desired product
   - Modify information
   - Update image if necessary
   - Save changes

3. **Delete product**:
   - Click the delete button
   - Confirm deletion
   - **Note**: Product will be removed from all orders

#### 9.3 Assign Products to Families

1. **Select family**:
   - Click on the family name
   - View assigned products

2. **Add product to family**:
   - Click "Add Product"
   - Select existing product
   - Confirm assignment

---

### 10. User Management (Super Administrator Only)

#### 10.1 View All Users

1. **Access user management**:
   - From the administrative panel
   - Click "User Management"

2. **Information displayed**:
   - User name
   - Email address
   - Current role
   - Management options

#### 10.2 Change User Role

1. **Select user**:
   - Find the user in the list
   - Use the role dropdown

2. **Available roles**:
   - **Customer**: Basic system access
   - **Seller**: Order and menu management
   - **Super Administrator**: Full access

3. **Confirm change**:
   - Change is applied immediately
   - User must restart session to see changes

#### 10.3 Delete User

1. **Select user**:
   - Click the "Delete" button
   - Confirm deletion

2. **Considerations**:
   - All user orders are deleted
   - Action cannot be undone
   - Only delete users without important orders

---

### 11. Troubleshooting

#### 11.1 Access Issues

**Cannot login**:
- Verify email and password
- Ensure JavaScript is enabled
- Clear browser cache
- Contact administrator

**Permission error**:
- Verify your role has access to the functionality
- Contact Super Administrator

#### 11.2 Order Issues

**Cannot add to cart**:
- Verify internet connection
- Reload the page
- Clear site cookies

**Error processing order**:
- Verify address information
- Ensure you have products in cart
- Try again

#### 11.3 Administrative Panel Issues

**Data not loading**:
- Verify database connection
- Reload the page
- Verify user permissions

**Error updating information**:
- Verify all required fields are completed
- Ensure you have editing permissions
- Contact system administrator

---

## 📞 Soporte Técnico / Technical Support

### 🇪🇸 Contacto
- **Email**: soporte@pizzeriadominico.com
- **Teléfono**: +54 11 1234-5678
- **Horario**: Lunes a Viernes 9:00 - 18:00

### 🇺🇸 Contact
- **Email**: support@pizzeriadominico.com
- **Phone**: +54 11 1234-5678
- **Hours**: Monday to Friday 9:00 - 18:00

---

**Versión del Manual / Manual Version**: 1.0  
**Última Actualización / Last Update**: Diciembre 2024 / December 2024

