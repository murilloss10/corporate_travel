# Serviço de Criação de Ordens de Viagem

Este projeto é um serviço de criação de ordens de viagem. Ele permite que os usuários criem ordens de viagem, adicionando detalhes como ```destino```, ```data de partida``` e ```data de retorno```.

O serviço é dividido em ações de usuário comuns e usuário administradores, sendo a diferenciação feita por ```scopes``` do token.
Os tipos de tokens e suas ações são:
- 'user-permission': Pode criar ordens de viagem, visualizar suas ordens de viagem criadas, cancelar suas ordens de viagem e receber e-mails de confirmação de cancelamento e/ou atualização de status de ordem de viagem.
- 'admin-permission': Pode visualizar todas as ordens de viagem criadas por outros usuários e aprovar ou desaprovar pedidos.

## Informações do Projeto
- **Nome do Projeto:** Corporate Travel
- **Descrição:** Serviço de criação de ordens de viagem.
- **Tecnologias Utilizadas:** PHP, Laravel, MySQL, Docker, Docker Compose.
- **Versão do Laravel:** 11.x
- **Versão do PHP:** 8.3
### Regras de Negócio
- Somente os usuários autenticados e com token ```user-permission``` podem criar ordens de viagem;
- Para ver os detalhes de uma ordem de viagem específica, o usuário deve ser o proprietário da ordem de viagem ou ter permissão de administrador (token ```admin-permission```);
- Somente os usuários com token ```admin-permission``` e que não sejam proprietários da ordem, podem aprovar ou desaprovar ordens de viagem.
    - Somente pedidos com o status "Solicitado" podem ser aprovados ou desaprovados;
    - Após a avaliação, o status da ordem de viagem será atualizado para "Aprovado" ou "Cancelado";
    - Um 'Job' é acionado através do 'Obverser' para enviar e-mails de confirmação de aprovação ou reprovação de uma ordem de viagem.
- Um usuário com token ```user-permission``` pode cancelar uma ordem de viagem apenas após a aprovação da mesma;
- A listagem de ordens de viagem é paginada e exibem diferentes resultados, dado o escopo do token.
    - Filtros podem ser aplicados para a listagem de ordens de viagem, sendo os campos:
        - 'status', 'city', 'state', 'startDate', 'endDate' e 'perPage';
        - Todos opcionais e sendo passados como query params; e
        - Onde 'perPage' é o número de resultados por página;
        - 'status' sendo 'Aprovado', 'Solicitado' ou 'Cancelado';
        - 'city', o nome da cidade;
        - 'state', o nome do estado;
        - 'startDate', no formato 'YYYY-MM-DD', a data de início da viagem;
        - 'endDate', no formato 'YYYY-MM-DD', a data de retorno da viagem;
        - Ex.: `http://localhost:8000/api/v1/orders?status=Aprovado&city=São Paulo&state=SP&startDate=2023-01-01&endDate=2023-01-31&perPage=10`
    - Usuário com token ```user-permission``` receberá somente as suas solicitações;
    - Usuário com token ```admin-permission``` receberá as solicitações de todos os usuários;


## Pré-requisitos
Ter instalado o Docker e o Docker Compose no seu sistema.

## Configuração
1. Clone o repositório:
```
git clone https://github.com/murilloss10/corporate_travel.git
```
2. Navegue até o diretório do projeto e execute o seguinte comando para criar e iniciar os containers:

```
docker-compose up -d
```
3. É necessário instalar as dependências do projeto, com o comando:
```
docker exec -it corporate_travel_app composer install
```
4. Crie um arquivo .env a partir do arquivo .env.example:
```
docker exec -it corporate_travel_app cp .env.example .env
```
5. Gere uma nova chave para o Laravel:
```
docker exec -it corporate_travel_app php artisan key:generate
```
6. Execute as migrações do banco de dados:
```
docker exec -it corporate_travel_app php artisan migrate
```
7. Gere as chaves do Passport:
```
docker exec corporate_travel_app php artisan passport:keys
```
8. Crie um novo acesso pessoal de cliente do Passport:
```
docker exec corporate_travel_app php artisan passport:client --personal
```
9. Copie as chaves geradas e retornadas no terminal e cole no arquivo .env para as seguintes variáveis de ambiente:
```
PASSPORT_PERSONAL_ACCESS_CLIENT_ID="client-id-value"
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET="client-secret-value"
```
### Disparo de e-mails
Por padrão, o serviço de disparo de e-mails está configurado no arquivo .env como 'log', mas caso seja da preferência, é possível utilizar o serviço de e-mail do Gmail, que está pré-configurado, faltando apenas as variáveis de ambiente: ```MAIL_MAILER=smtp```, ```MAIL_USERNAME```, ```MAIL_PASSWORD``` e ```MAIL_FROM_ADDRESS```. Onde ```MAIL_USERNAME``` e ```MAIL_FROM_ADDRESS``` são destinados ao e-mail do Gmail e ```MAIL_PASSWORD``` é a senha do e-mail.
Obs.: É necessário habilitar o acesso de aplicativos menos seguros nas configurações da sua conta, entretanto, esta habilitação é permitidas apenas para contas antigas.