# 🐾 Pet Tracker

[Português](#-pet-tracker---arquitetura-de-microsserviços) | [English](#-pet-tracker---microservices-architecture)

---

# 🐾 Pet Tracker - Arquitetura de Microsserviços

Este é o repositório central do **Pet Tracker**, um ecossistema de rastreamento de animais de estimação. O projeto foi desenhado sob uma arquitetura de microsserviços focada em alta disponibilidade, processamento assíncrono e segurança de ponta a ponta.

## 🏗️ Arquitetura do Sistema

O fluxo de dados foi projetado para suportar alta volumetria de requisições de geolocalização sem sobrecarregar o banco de dados principal.

1. **Emissor Nativo (Roadmap):** Podem ser tags que nos disponibilizem dados básicos de geolocalização e cadastro da tag.
2. **Tracker API (Hyperf/PHP):** O "Porteiro". Um microsserviço de altíssima performance (corrotinas) que recebe os pacotes de coordenadas e os despacha imediatamente para a fila, devolvendo uma resposta rápida ao hardware.
3. **Message Broker (RabbitMQ):** Gerencia o enfileiramento das mensagens (`pet_locations`), garantindo que nenhuma coordenada seja perdida caso o banco de dados sofra instabilidades.
4. **Core API (Laravel):** - **Worker:** Consome a fila do RabbitMQ de forma assíncrona e persiste os dados no MySQL.
   - **Restful API:** Serve os dados históricos de localização para o frontend, protegida por um Middleware de segurança (API Key).
5. **Web Client (Vue.js 3):** Interface reativa de monitoramento. Utiliza Leaflet para renderização do mapa em tempo real, Tailwind CSS v4 para estilização e Pinia para gerenciamento de estado.

## 🚀 Tecnologias Utilizadas

**Frontend:**
- Vue 3 (Composition API) + TypeScript
- Pinia (State Management)
- Tailwind CSS v4
- Leaflet (Maps) + Axios

**Backend & Infraestrutura:**
- Laravel 11 (Core API & Workers)
- Hyperf (High-performance API)
- RabbitMQ (AMQP)
- MySQL 8.0
- Docker & Docker Compose

## 🛠️ Como Executar o Projeto

Todo o ecossistema está envelopado em containers Docker. Você não precisa ter PHP, Node.js ou bancos de dados instalados na sua máquina local.

### Pré-requisitos
- Docker
- Docker Compose

### Instalação

1. Clone este repositório:
   ```bash
   git clone [https://github.com/gustapng/pet-tracker-system](https://github.com/gustapng/pet-tracker-system)
   cd pet-tracker
   ```

2. Levante a infraestrutura:
Volte para a raiz do projeto e execute:
    ```bash
    docker-compose up -d --build
    ```

3. Acompanhe a execução dos processos:
Na raiz do projeto e execute:
    ```bash
    docker logs -f pet_core_api
    ```
Ao fim da execução você verá algo como:
   - ✅ SETUP CONCLUÍDO COM SUCESSO!
   - 🌐 Acesse: http://localhost:5173
   - 🛑 Parar: docker-compose stop
   - 🗑️ Excluir Containers: docker-compose down -v

### Acessos
Aplicação Web: http://localhost:5173

API Laravel: http://localhost:8000

Painel RabbitMQ: http://localhost:15672 (guest / guest)

## 🔒 Roadmap de Evolução
[x] MVP de persistência e fila de mensagens.

[x] Consumo de dados geográficos no Vue.js com Leaflet.

[x] Proteção inicial de endpoints com API Key estática.

[x] Tempo Real: Implementação de WebSockets (Laravel Reverb) para atualização autônoma do mapa sem recarregamento.

[ ] Segurança: Quando criar tela de registro/login, refatorar a autenticação para JWT (JSON Web Token) visando escalabilidade multi-inquilino (multi-tenant) e tela de Login de usuários.

---

# 🐾 Pet Tracker - Microservices Architecture

This is the central repository for **Pet Tracker**, a pet tracking ecosystem. The project is designed using a microservices architecture focused on high availability, asynchronous processing, and end-to-end security.

## 🏗️ System Architecture

The data flow is designed to support high volumes of geolocation requests without overloading the main database.

1. **Native Emitter (Roadmap):** External tags that provide basic geolocation data and registration.
2. **Tracker API (Hyperf/PHP):** The "Gatekeeper." A high-performance microservice (coroutines) that receives coordinate packets and immediately dispatches them to the queue, returning a fast response to the hardware.
3. **Message Broker (RabbitMQ):** Manages message queuing (`pet_locations`), ensuring no coordinates are lost in case of database instability.
4. **Core API (Laravel):**
   - **Worker:** Asynchronously consumes the RabbitMQ queue and persists data to MySQL.
   - **RESTful API:** Serves historical location data to the frontend, protected by security middleware (API Key).
5. **Web Client (Vue.js 3):** Reactive monitoring interface. Uses Leaflet for real-time map rendering, Tailwind CSS v4 for styling, and Pinia for state management.

## 🚀 Technologies Used

**Frontend:**
- Vue 3 (Composition API) + TypeScript
- Pinia (State Management)
- Tailwind CSS v4
- Leaflet (Maps) + Axios

**Backend & Infrastructure:**
- Laravel 11 (Core API & Workers)
- Hyperf (High-performance API)
- RabbitMQ (AMQP)
- MySQL 8.0
- Docker & Docker Compose

## 🛠️ How to Run the Project

The entire ecosystem is containerized. You do not need to have PHP, Node.js, or databases installed on your local machine.

### Prerequisites
- Docker
- Docker Compose

### Installation

1. Clone this repository:
   ```bash
   git clone [https://github.com/your-username/pet-tracker.git](https://github.com/your-username/pet-tracker.git)
   cd pet-tracker
   ```

2. Start the infrastructure:
From the project root, run:
    ```bash
    docker-compose up -d --build
    ```

3. Track the processes:
From the project root, run:
    ```bash
    docker logs -f pet_core_api
    ```
At the end of the execution, you will see:
   - ✅ SETUP COMPLETED SUCCESSFULLY!
   - 🌐 Access: http://localhost:5173
   - 🛑 Stop: docker-compose stop
   - 🗑️ Remove Containers: docker-compose down -v

### Access Points
Web Application: http://localhost:5173

Laravel API: http://localhost:8000

RabbitMQ Dashboard: http://localhost:15672 (guest / guest)

## 🔒 Roadmap
[x] MVP: Persistence and message queuing.

[x] Geodata consumption in Vue.js with Leaflet.

[x] Initial endpoint protection with static API Key.

[x] Real-time: Implementation of WebSockets (Laravel Reverb) for autonomous map updates.

[ ] Security: Refactor authentication to JWT (JSON Web Token) for multi-tenant scalability and user login screen.