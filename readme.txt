=== Shipping Servientrega Panamá Woocommerce ===
Contributors: saulmoralespa
Tags: shipping, servientrega, panama, woocommerce, wordpress, envio, guia, tracking, cotizacion
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 8.1
Stable tag: 1.0.0
WC requires at least: 6.9
WC tested up to: 10.2.2
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Integración completa de Servientrega Panamá con WooCommerce para cotización y generación de guías de envío.

== Description ==

**Shipping Servientrega Panamá Woocommerce** es un plugin que integra los servicios de Servientrega Panamá con tu tienda WooCommerce, permitiéndote ofrecer opciones de envío en tiempo real y generar guías de despacho de manera automática.

= Características principales =

* **Cotización en tiempo real** - Obtén tarifas de envío actualizadas directamente desde la API de Servientrega
* **Generación de guías de envío** - Crea guías de despacho automáticamente desde el panel de administración
* **Seguimiento de envíos** - Integración con el sistema de tracking de Servientrega
* **Modo Sandbox** - Prueba la integración en entorno de desarrollo antes de ir a producción
* **Compatible con HPOS** - Totalmente compatible con las tablas personalizadas de pedidos de WooCommerce
* **Tarea programada** - Incluye cron mensual para mantenimiento automático
* **Integración con provincias y distritos** - Funciona con el [plugin de provincias y distritos de Panamá](https://github.com/saulmoralespa/provincias-y-distritos-de-panama-para-woocommerce)

= Requisitos del sistema =

* WordPress 5.0 o superior
* WooCommerce 6.9 o superior
* PHP 8.1 o superior
* Extensión PHP XML habilitada
* Plugin: "Provincias y Distritos de Panamá para WooCommerce"
* País de la tienda configurado en Panamá (PA)
* Cuenta activa con Servientrega Panamá

= Funcionalidades técnicas =

* **API REST** - Comunicación mediante Guzzle HTTP Client con los servicios web de Servientrega
* **SOAP/NuSOAP** - Integración para servicios de tracking y generación de guías
* **Validaciones automáticas** - Verifica configuración del país y extensiones PHP requeridas
* **Notificaciones de admin** - Alertas en el panel cuando faltan configuraciones necesarias

= Servicios de Servientrega integrados =

* **Cotizador**: https://ws-servientrega.appsiscore.com/cotizador/ws_cotizador.php
* **Guías de envío**: https://ws-servientrega.appsiscore.com/
* **Tracking de despachos**: https://ws-servientrega.appsiscore.com/server_wst.php

= Modo Sandbox =

El plugin incluye un modo de pruebas que utiliza el entorno sandbox de Servientrega:
* URL Sandbox: https://ws-servientrega.appsiscore.com/test/

Esto te permite probar todas las funcionalidades sin afectar tus envíos reales.

= Compatibilidad =

* Compatible con WordPress Multisite
* Compatible con WooCommerce High-Performance Order Storage (HPOS)
* Compatible con las últimas versiones de WooCommerce

= Soporte y documentación =

Para obtener tus credenciales de API y más información sobre los servicios de Servientrega Panamá, visita su sitio web oficial o contacta con su equipo comercial.

== Installation ==

= Instalación manual =

1. Descarga el archivo ZIP del plugin
2. Ve a tu panel de administración de WordPress
3. Navega a Plugins > Añadir nuevo > Subir plugin
4. Selecciona el archivo ZIP y haz clic en "Instalar ahora"
5. Activa el plugin

= Configuración inicial =

1. **Verificar requisitos previos**:
   * Asegúrate de que tu tienda esté configurada con país Panamá (PA)
   * Ve a WooCommerce > Ajustes > General
   * En "País / Estado de la tienda" selecciona Panamá

2. **Instalar dependencias**:
   * Instala y activa el plugin "[Provincias y Distritos de Panamá para WooCommerce](https://github.com/saulmoralespa/provincias-y-distritos-de-panama-para-woocommerce)"
   * Verifica que la extensión PHP XML esté habilitada en tu servidor

3. **Configurar método de envío**:
   * Ve a WooCommerce > Ajustes > Envío
   * Selecciona la zona de envío correspondiente
   * Añade el método de envío "Servientrega Panamá"
   * Ingresa tus credenciales de API (usuario y contraseña)

4. **Configurar opciones**:
   * Habilita/deshabilita el modo sandbox según necesites
   * Configura las opciones adicionales de cotización
   * Guarda los cambios

= Verificación de la instalación =

Después de instalar y configurar:

1. Crea un pedido de prueba en tu tienda
2. Verifica que aparezcan las opciones de envío de Servientrega
3. Revisa que las tarifas se calculen correctamente
4. Prueba la generación de una guía de envío desde el panel de pedidos

== Frequently Asked Questions ==

= ¿Necesito una cuenta con Servientrega Panamá? =

Sí, necesitas tener una cuenta comercial activa con Servientrega Panamá y obtener tus credenciales de API (usuario y contraseña) para usar este plugin.

= ¿El plugin funciona fuera de Panamá? =

No, este plugin está diseñado específicamente para tiendas ubicadas en Panamá. El país de tu tienda debe estar configurado como Panamá (PA) para que el plugin funcione.

= ¿Qué es el modo sandbox? =

El modo sandbox es un entorno de pruebas que te permite probar la integración sin generar guías reales ni afectar tus operaciones. Es ideal para desarrollo y testing.

= ¿El plugin es compatible con WooCommerce HPOS? =

Sí, el plugin declara compatibilidad completa con las tablas personalizadas de pedidos de WooCommerce (High-Performance Order Storage).

= ¿Necesito otros plugins para que funcione? =

Sí, necesitas tener instalado y activado el plugin "[Provincias y Distritos de Panamá para WooCommerce](https://github.com/saulmoralespa/provincias-y-distritos-de-panama-para-woocommerce)" para el correcto funcionamiento de las zonas de envío.

= ¿Qué hace la tarea programada (cron)? =

El plugin incluye una tarea programada que se ejecuta mensualmente para tareas de mantenimiento automático del sistema.

= ¿Puedo generar guías de envío desde el panel de pedidos? =

Sí, una vez configurado correctamente, podrás generar guías de envío directamente desde la página de edición de cada pedido en WooCommerce.

= ¿El plugin incluye seguimiento de envíos? =

Sí, el plugin se integra con el sistema de tracking de Servientrega para consultar el estado de los despachos.

= ¿Qué extensiones PHP necesito? =

Necesitas tener habilitada la extensión XML de PHP. El plugin verificará automáticamente si está disponible y mostrará una notificación si falta.

= ¿Es compatible con Multisite? =

Sí, el plugin es compatible con instalaciones WordPress Multisite.

== Screenshots ==

1. Configuración del método de envío Servientrega
2. Opciones de envío en el checkout
3. Panel de generación de guías
4. Ejemplo de cotización en tiempo real

== Changelog ==

= 1.0.0 - 2025-10-03 =
* Lanzamiento inicial
* Integración con API de cotizador de Servientrega
* Generación de guías de envío
* Sistema de tracking de despachos
* Modo sandbox para pruebas
* Compatibilidad con WooCommerce HPOS
* Validación automática de requisitos
* Tarea programada mensual (cron)
* Integración con provincias y distritos de Panamá
* Soporte para PHP 8.1+
* Compatibilidad con WooCommerce 6.9 - 10.2

== Upgrade Notice ==

= 1.0.0 =
Versión inicial del plugin. Asegúrate de tener WooCommerce 6.9 o superior y PHP 8.1 o superior.

== Additional Info ==

= Créditos =

* Desarrollado por Saúl Morales Pacheco
* Sitio web: https://saulmoralespa.com

= Dependencias técnicas =

El plugin utiliza las siguientes librerías:
* GuzzleHttp/Guzzle - Cliente HTTP para comunicación con API REST
* GuzzleHttp/Promises - Manejo de promesas asíncronas
* GuzzleHttp/PSR7 - Implementación de mensajes HTTP PSR-7
* econea/nusoap - Cliente SOAP para servicios web SOAP
* PSR HTTP Client/Factory/Message - Interfaces PSR estándar

= Estructura de la API =

**Cotización de envíos:**
* Endpoint: POST https://ws-servientrega.appsiscore.com/cotizador/ws_cotizador.php
* Formato: JSON
* Autenticación: Usuario y contraseña en el body

**Generación de guías:**
* Producción: https://ws-servientrega.appsiscore.com/
* Sandbox: https://ws-servientrega.appsiscore.com/test/

**Tracking:**
* WSDL: https://ws-servientrega.appsiscore.com/server_wst.php?wsdl
* Protocolo: SOAP

= Soporte =

Si tienes problemas o preguntas sobre el plugin, puedes:
* Visitar el sitio web del desarrollador: https://saulmoralespa.com
* Contactar con el soporte de Servientrega Panamá para temas relacionados con la API

= Contribuciones =

Las contribuciones son bienvenidas. Si deseas mejorar el plugin, por favor contacta al desarrollador.

== Privacy Policy ==

Este plugin se conecta con los servicios web de Servientrega Panamá para procesar información de envíos. Los datos enviados incluyen:
* Información de direcciones de envío
* Peso y dimensiones de paquetes
* Datos del remitente y destinatario

Toda la comunicación se realiza de forma segura mediante HTTPS. Por favor, revisa la política de privacidad de Servientrega Panamá para más información sobre cómo manejan tus datos.

