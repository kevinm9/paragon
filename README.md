# Laravel + Vue Starter Kit
<img width="4104" height="2232" alt="fondoooo" src="https://github.com/user-attachments/assets/7adeff45-9d10-4327-a408-093c6eb23b6a" />

# Maquetado Frontend: Sistema Integral de Gestión de Cobranza y Procesos Judiciales
Este repositorio presenta el maquetado frontend completo de un sistema robusto y completo, diseñado para optimizar la gestión de cobranza y el seguimiento de procesos judiciales. Las capturas de pantalla ilustran una interfaz de usuario intuitiva y detallada, desarrollada para agilizar las operaciones de recuperación de deudas, ofreciendo una visión clara y organizada de cada etapa, desde la gestión inicial de pagos hasta la resolución de casos en instancias judiciales.
Características Principales:
Agente de Pagos Centralizado: Módulo principal para el registro y seguimiento de compromisos de pago. Permite visualizar pagos próximos, realizados y vencidos, con detalles de montos, fechas y estados. Facilita la asignación de responsabilidades y la toma de acciones rápidas sobre cada compromiso.
Listado de Deudores e Instituciones Públicas: Un panel exhaustivo que presenta un listado completo de deudores, con información clave como número de identificación, nombres, estado y montos asociados. Incluye filtros avanzados para una búsqueda y clasificación eficientes.
Gestión Judicial Detallada: Módulos específicos para el control de la información judicial de cada deudor. Permite registrar y consultar datos relevantes como la fecha de inicio del proceso, el número de gestión judicial, el juez asignado, los valores implicados y el estado actual del caso. Esto asegura un seguimiento preciso de cada etapa legal.
Historial de Etapas Judiciales: Proporciona un registro cronológico de todas las fases por las que ha pasado un proceso judicial. Desde la presentación de la demanda hasta la resolución, cada etapa se documenta con fechas, observaciones y el responsable de la gestión, ofreciendo total transparencia y trazabilidad.
Ficha de Deudor Completa: Una vista detallada de cada deudor que consolida toda la información relevante: datos personales, compromisos de pago, histórico de gestión, estado del crédito y cualquier otra nota pertinente. Facilita una comprensión integral del perfil del deudor.
Gestión de Contactos Asociados: Permite registrar y gestionar múltiples contactos relacionados con un deudor o un caso judicial. Esto incluye detalles como tipo de contacto (titular, garante, familiar), números telefónicos, correos electrónicos y observaciones, mejorando la comunicación y la recuperación.
Creación y Edición de Compromisos de Pago: Funcionalidades para establecer nuevos compromisos de pago con deudores, especificando el tipo de compromiso, el monto, la fecha de inicio, la periodicidad y el estado. Se pueden registrar múltiples cuotas y llevar un control del balance pendiente.
Interfaz Intuitiva y Orientada al Usuario: La aplicación cuenta con una interfaz limpia y organizada, que facilita la navegación y la interacción con los distintos módulos. El uso de pestañas, filtros y tablas claras contribuye a una experiencia de usuario eficiente.
Este sistema es ideal para instituciones financieras, bufetes de abogados o departamentos de cobranza que buscan una solución integral para gestionar sus procesos de recuperación de activos de manera eficaz y organizada.

# Frontend Layout: Comprehensive Collection Management and Judicial Process System

This repository showcases the complete frontend layout of a robust and comprehensive system designed to optimize debt collection management and the monitoring of judicial processes. The included screenshots display an intuitive and detailed user interface built to streamline debt recovery operations, offering a clear and organized view of each stage — from initial payment management to the resolution of judicial cases.

Key Features

Centralized Payment Management
Main module for registering and tracking payment agreements. It allows users to view upcoming, completed, and overdue payments, including details such as amounts, dates, and statuses. It supports assigning responsibilities and taking quick action on each commitment.

Debtors and Public Institutions Directory
A comprehensive dashboard that provides a complete list of debtors, including key information such as identification number, full name, status, and associated amounts. It features advanced filters for efficient search, sorting, and classification.

Detailed Judicial Case Management
Specialized modules to control and monitor judicial information for each debtor. Users can record and consult key data such as case initiation date, judicial case number, assigned judge, involved amounts, and current case status—ensuring precise tracking of each legal stage.

Judicial Stage History Timeline
Provides a chronological record of all stages a judicial process has gone through. From the filing of the lawsuit to the final resolution, each phase is documented with dates, notes, and the person in charge, ensuring full transparency and traceability.

Complete Debtor Profile
A detailed view that consolidates all relevant information for each debtor: personal data, payment commitments, management history, credit status, and additional notes. This facilitates a comprehensive understanding of each debtor’s profile.

Associated Contact Management
Enables the registration and management of multiple contacts linked to a debtor or judicial case. This includes details such as type of contact (primary, guarantor, family member), phone numbers, emails, and notes—enhancing communication and recovery efforts.

Payment Commitment Creation and Editing
Features for setting up new payment agreements with debtors, specifying type, amount, start date, frequency, and status. Users can record multiple installments and maintain control over the remaining balance.

User-Friendly and Intuitive Interface
The application includes a clean, well-structured, and easy-to-navigate interface. Tabs, filters, and clearly formatted tables are used to promote an efficient and user-centered experience.

This system is ideal for financial institutions, law firms, and collection departments seeking a fully integrated solution to efficiently manage their asset recovery processes in an organized and effective manner.

## Introduction

Our Vue starter kit provides a robust, modern starting point for building Laravel applications with a Vue frontend using [Inertia](https://inertiajs.com).

Inertia allows you to build modern, single-page Vue applications using classic server-side routing and controllers. This lets you enjoy the frontend power of Vue combined with the incredible backend productivity of Laravel and lightning-fast Vite compilation.

This Vue starter kit utilizes Vue 3 and the Composition API, TypeScript, Tailwind, and the [shadcn-vue](https://www.shadcn-vue.com) component library.

## Official Documentation

Documentation for all Laravel starter kits can be found on the [Laravel website](https://laravel.com/docs/starter-kits).

## Contributing

Thank you for considering contributing to our starter kit! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## License

The Laravel + Vue starter kit is open-sourced software licensed under the MIT license.


para ngrok 
APP_URL=https://90eead2264d6.ngrok-free.app
ASSET_URL=https://90eead2264d6.ngrok-free.app

php artisan config:clear
php artisan cache:clear
npm run build  # o npm run dev
