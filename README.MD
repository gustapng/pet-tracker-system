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
   git clone [https://github.com/seu-usuario/pet-tracker.git](https://github.com/seu-usuario/pet-tracker.git)
   cd pet-tracker
   cp .env.example .env
   ```
2. Sincronizar a Chave no Frontend (Vue 3):
Abra o arquivo pet-tracker-web/src/stores/pet.ts e certifique-se de que a propriedade X-API-KEY dentro dos headers do Axios confere exatamente com a string configurada no .env do Laravel.

3. Levante a infraestrutura:
Volte para a raiz do projeto e execute:
    ```bash
    docker-compose up -d --build
    ```

### Acessos
Aplicação Web: http://localhost:5173

API Laravel: http://localhost:8000

Painel RabbitMQ: http://localhost:15672 (guest / guest)

## 🔒 Roadmap de Evolução
[x] MVP de persistência e fila de mensagens.

[x] Consumo de dados geográficos no Vue.js com Leaflet.

[x] Proteção inicial de endpoints com API Key estática.

[ ] Segurança: Refatorar a autenticação para JWT (JSON Web Token) visando escalabilidade multi-inquilino (multi-tenant) e tela de Login de usuários.

[x] Tempo Real: Implementação de WebSockets (Laravel Reverb) para atualização autônoma do mapa sem recarregamento.