**ID:** RF-001  
**Nome: Navbar \- Padrão Tela Inicial**   
**Descrição:** A tela inicial do sistema deve exibir um menu de navegação com as seguintes funcionalidades:   
**Início**: Retorna à página principal do sistema, exibindo a mensagem: “Bem-vindo ao Apê Pousada, sua estadia de luxo e conforto em um ambiente sofisticado.”, abaixo o botão “Reservar agora”  
**Quartos:** O sistema oferece a funcionalidade de “Monte Seu Quarto, configure suas datas e acomodação” com seleção de datas por meio das opções "Check-in" e "Check-out". Em seguida, apresenta uma lista de camas disponíveis, exibindo informações detalhadas sobre os tipos (beliche, casal e solteiro) e a respectiva capacidade máxima de acomodação. Adicionalmente, o sistema realiza o cálculo automático do total de hóspedes por quarto, que deve ter no máximo um total de quatro e envia esse filtro criado de quantidade de tipos de acomodações para as reservas quarto cliente.  
**Serviços:** O sistema conta com uma seção específica com o título “Serviços Essenciais  
Tudo para sua produtividade e conforto básico” para a apresentação dos serviços oferecidos pelo estabelecimento, fornecendo uma descrição detalhada de cada um: a conexão Wi-Fi corporativa, com informações sobre sua capacidade; o serviço de café da manhã, incluindo o horário de funcionamento; e a assistência administrativa eficiente, que visa otimizar os processos de check-in e check-out, além de oferecer suporte logístico aos hóspedes.   
**Sobre**: Contém informações institucionais sobre o estabelecimento, incluindo uma breve descrição dos serviços oferecidos e dos valores priorizados pela gestão. Além disso, destaca os principais pontos de interesse para os hóspedes, juntamente de uma foto do local a esquerda do texto: 

***“**Um Refúgio Essencial*

*Na Apê Pousada, entendemos que seu descanso é fundamental para um dia produtivo. Oferecemos o básico bem feito:*

*Localização tranquila: afastada do centro urbano, em área arborizada*  
*Quartos funcionais: camas confortáveis*  
*Café reforçado: servido das 5h30 às 8h30 no refeitório*  
*Conexão prática: Wi-Fi estável nas áreas comuns.*

*Um lugar para recarregar as energias após um dia intenso de trabalho, sem luxos desnecessários mas com tudo que realmente importa para seu descanso.”*

**Contatos:** Disponibiliza os meios de contato da administração, incluindo telefone, e-mail e WhatsApp da pousada, além disso ao clicar em qualquer uma dessas opções o sistema redireciona para a execução de cada contato.  
**Login:** Direciona o usuário para a página de autenticação, onde ele pode acessar sua conta por meio de e-mail e senha previamente cadastrados na base de dados ou realizar um novo cadastro.   
O sistema também permite a alteração de senha, onde é solicitado o e-mail cadastrado, a nova senha e a confirmação da mesma.  
No processo de cadastro, a inserção dos dados é realizada em três etapas:   
Primeira etapa, dados de acesso – Solicitação de informações básicas: nome completo, CPF ou CNPJ, e-mail e senha.  
Segunda etapa, dados pessoais – Coleta de dados pessoais adicionais: telefone celular, telefone fixo, data de nascimento, sexo, profissão, empresa, nacionalidade e RG.  
Terceira e última etapa, endereço – Cadastro do endereço residencial, incluindo: CEP, logradouro, número, complemento, bairro, cidade e estado.  
Após o preenchimento das três etapas, o cadastro é finalizado e os dados são armazenados na base do sistema.  
**Entradas:** Clique do usuário em uma das opções do menu de navegação.  
Clique do usuário para seleção do filtro de tipos de acomodação.  
Clique do usuário em um dos botões para reservar.  
Clique do usuário em uma das opções de forma de contato.  
Entrada de dados pelo usuário para realização do login, alteração de senha e novo cadastro.  
**Origem:** Ação iniciada pelo usuário ao acessar a tela inicial.  
**Saída:** Redirecionamento para a seção correspondente da página ou outra página do sistema.  
**Destino:** Página ou seção específica relacionada à opção escolhida pelo usuário.  
**Ação:** O sistema deve processar a interação do usuário e carregar a página ou seção correspondente.  
**Pré-condição:** O sistema deve estar operacional e acessível ao usuário. O menu de navegação deve estar visível na tela inicial.  
**Pós-condição:** O usuário é direcionado corretamente para a funcionalidade selecionada.  
**Efeitos Colaterais:** Caso o sistema esteja fora do ar ou a funcionalidade não esteja disponível, o usuário pode receber uma mensagem de erro.

\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_

**ID:** RF-002  
**Nome: Módulo de Reserva**  
**Descrição:** Após o cliente clicar em “Reservar Agora” na tela inicial, ou no filtro de “Monte seu quarto”, ele é redirecionado para a página de quartos disponíveis. Logo abaixo, é exibido o filtro de datas em que o cliente deseja realizar sua reserva, incluindo também a opção de selecionar o tipo de cama e a quantidade de hóspedes que irão se hospedar, concluindo assim o menu de filtro da reserva. O sistema exibe fotos dos quartos, previamente cadastradas pela equipe administrativa, seguidas, respectivamente, por suas descrições, valor da reserva e capacidade de cada quarto. Após o cliente selecionar o quarto e as especificações, e clicar em “Reservar”, ele é direcionado para a tela “Reservar Quarto” o sistema solicita os dados pessoais do hóspede e exibe a imagem da acomodação selecionada, juntamente com as regras do quarto, que foram cadastradas previamente no cadastro do mesmo.

O sistema solicita ao usuário o nome, e-mail, a confirmação das datas de check-in e check-out, confirmação do valor da reserva, o tipo de pensão e a forma de pagamento. Ao finalizar a inserção dos dados, o valor total da reserva deve ser calculado de forma automática. O sistema também permite ao usuário inserir observações adicionais a respeito da reserva.

**Entradas:** Clique do usuário no botão "Reservar Agora" na tela inicial. Seleção de datas desejadas, clique no botão “Filtrar para buscar as datas". Clique no botão "Reserva" de um quarto específico. Preenchimento do formulário com Nome, E-mail, Check-in e Check-out, forma de pagamento. Clique no botão "Confirmar Reserva" para confirmar a reserva.   
**Origem:** Página Inicial do site, onde o cliente inicia o processo de reserva.

#### **Saída**: Confirmação de reserva bem-sucedida ou mensagem de erro, caso haja problemas.Redirecionamento para uma tela de confirmação da reserva.

#### **Destino:** Página de quartos disponíveis após clicar em "Reservar" na tela inicial. Página de confirmação de reserva após selecionar um quarto e preencher os dados.

#### **Ação**: O sistema deve exibir os quartos disponíveis conforme a data selecionada. O cliente pode escolher um quarto e preencher seus dados para concluir a reserva. O sistema deve registrar a reserva automaticamente após a confirmação.

#### **Pré-condição**: O usuário deve estar na tela inicial e clicar no botão "Reservar". O sistema deve ter quartos disponíveis para exibição. O usuário deve preencher corretamente todos os campos obrigatórios.

#### **Pós-condição:** A reserva é registrada no sistema e confirmada para o cliente. O usuário recebe uma mensagem de confirmação.

#### **Efeitos Colaterais:** Caso não haja quartos disponíveis para a data selecionada, o sistema deve informar ao usuário. Caso o usuário preencha informações inválidas, o sistema deve exibir uma mensagem de erro. Se houver falha na conexão ou erro interno, a reserva pode não ser concluída corretamente.

\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_

**ID:** RF-003  
**Nome: Módulo Clientes**

#### **Descrição:** O sistema deve permitir que o cliente, após finalizar uma reserva, realize login e acesse a tela "Minhas Reservas". Nesta tela, o cliente poderá visualizar suas reservas, incluindo detalhes como número do quarto, status, foto, valor, tipo de quarto, datas de check-in, check-out e a informação de quem realizou a reserva, além de um botão para confirmação da reserva ou para avaliar a reserva que devem ser desabilitados caso a reserva já tenha sido confirmada ou a avaliação realizada.

#### **Entrada:** Credenciais de login do cliente (e-mail e senha). Dados das reservas associadas ao cliente.

#### **Origem:** O cliente realiza o login na mesma interface utilizada pelos funcionários. O banco de dados contém as informações das reservas associadas ao cliente.

#### Saída Exibição da lista de reservas do cliente na tela "Minhas Reservas". Exibição de detalhes das reservas (número do quarto, status, foto, valores, tipo do quarto, check-in e check-out). Ação do botão de confirmação de reserva.

#### **Destino:** Interface do cliente dentro do sistema de reservas do hotel.

#### **Ação:** O cliente acessa a interface de login e inserir suas credenciais. O sistema verifica e autentica o usuário. A tela "Minhas Reservas" exibe a lista de reservas associadas ao cliente. O cliente pode visualizar detalhes de suas reservas. Se necessário, pode confirmar a reserva utilizando o botão correspondente.

#### **Pré-condição:** O cliente deve possuir uma conta e ter realizado pelo menos uma reserva. O sistema deve estar disponível e operacional.

#### **Pós-condição:** O cliente visualiza corretamente suas reservas. Se a reserva for confirmada, o status é atualizado no sistema.

#### **Efeitos Colaterais:** Caso o cliente não tenha reservas, a tela pode exibir uma mensagem indicando a ausência de registros. Se houver erro no login, o cliente não poderá acessar suas reservas. Se a reserva for confirmada, pode haver impacto na disponibilidade dos quartos.

\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_

**ID:** RF-004  
**Nome: Navbar Padrão de Gerenciamento**  
**Descrição:** A barra de navegação superior permite o acesso rápido aos principais módulos do sistema por meio de ícones e textos. É exibida horizontalmente no topo da tela, com ícones e rótulos identificando cada seção.

**Funcionalidades da barra de navegação**:

**Home:** Direciona o usuário à página inicial do sistema, onde é exibido o painel geral (dashboard), clicar no nome do hotel e logo tem a mesma função.  
**Gerenciar Reservas:** Acesso ao módulo de gerenciamento de reservas de hóspedes, incluindo cadastro, edição e exclusão de reservas.  
**Gerenciar Quartos:** Redireciona para o módulo de administração de quartos, com funcionalidades de cadastro, edição e visualização de disponibilidade.  
**Serviços de Quarto:** Abre o módulo de serviços, onde é possível cadastrar e gerenciar pedidos de serviços de quarto associados a reservas.  
**Clientes:** Acesso ao gerenciamento de clientes, permitindo visualizar, cadastrar, editar e excluir registros de hóspedes.  
**Funcionários:** Direciona para o módulo de funcionários, possibilitando gerenciar dados dos colaboradores da pousada.  
**Pagamentos:**  Abre a área destinada ao controle e visualização de pagamentos efetuados e pendentes.  
**Financeiro:** Direcionado ao módulo financeiro, onde é possível acompanhar relatórios de contas a pagar e a receber.  
**Perfil do Usuário (ícone no canto direito):** Ícone de usuário no canto superior direito, que permite o acesso a opções relacionadas ao perfil do usuário logado, como: visualizar dados pessoais, alterar senha ou sair do sistema.

**Origem:** Após realizar o login com as credenciais de e-mail e senha na tela inicial, o funcionário estará autenticado na base de dados do sistema para acessar essa funcionalidade.  
**Destino:** Página ou seção específica relacionada à opção escolhida pelo usuário.  
**Ação:** O sistema deve processar a interação do usuário e carregar a página ou seção correspondente.  
**Pré-condição**:  Usuário deve estar autenticado no sistema para visualizar e interagir com a barra de navegação.  
**Pós-condição**: Acesso redirecionado com sucesso para o módulo correspondente ao item clicado.  
**Efeitos Colaterais: ** Caso o sistema esteja fora do ar ou a funcionalidade não esteja disponível, o usuário pode receber uma mensagem de erro.

**ID:** RF-005  
**Nome: Módulo Home**  
**Descrição:** A tela home do sistema “Apê Pousada” deve apresentar um painel de controle com informações consolidadas das principais áreas operacionais do sistema. Cada módulo é exibido em um cartão com dados numéricos e indicadores de status. Os módulos visíveis incluem: Reservas, Quartos, Clientes, Funcionários, Serviços de Quarto, Baixas de Pagamentos e Relatório Financeiro. Funcionalidades visíveis:

**Reservas**: Total de reservas no mês, número de reservas confirmadas, pendentes e canceladas.  
**Quartos**: Quantidade total de quartos, ocupação atual, reservas previstas para o mês seguinte e média geral de avaliações.  
**Clientes**: Número de clientes cadastrados no sistema.  
**Funcionários**: Total de funcionários ativos, valor total de despesas com funcionários e quantidade de funcionários com férias vencidas.  
**Serviços de Quarto**: Total de atendimentos realizados e serviços pendentes.  
**Baixas de Pagamentos**: Valores recebidos no mês atual, valores pendentes em andamento e valores previstos para o próximo mês.  
**Relatório Financeiro:** Total de contas a pagar, valores a receber e a próxima conta a pagar com data e valor destacados.  
**Origem:** Após realizar o login com as credenciais de e-mail e senha na tela inicial, o funcionário estará autenticado na base de dados do sistema para acessar essa funcionalidade.  
**Saída:** Visualização das métricas e indicadores em tempo real, com destaque de informações críticas por meio de cores (vermelho, verde, amarelo).  
**Destino:** Página ou seção específica relacionada à opção escolhida pelo usuário.  
**Ação:**O sistema deve processar a interação do usuário e carregar a página ou seção correspondente.  
**Pré-condição:** Usuário autenticado e com permissão de acesso ao painel administrativo.  
**Pós-condição:** Usuário visualiza os dados atualizados do sistema, podendo acessar os módulos correspondentes para mais detalhes ou ações.  
**Efeitos colaterais:** Caso o sistema esteja fora do ar ou a funcionalidade não esteja disponível, o usuário pode receber uma mensagem de erro.  
\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_

**ID:** RF-006  
**Nome: Módulo de Gerenciamento de Reservas**  
**Descrição:** O sistema deve permitir que os funcionários da pousada acessem a página de gerenciamento de reservas. Nessa página, os usuários podem pesquisar uma reserva específica por meio da barra de pesquisa, que permite gerar uma busca específica realizada por datas. Caso a reserva em questão não esteja cadastrada, o sistema disponibiliza o botão “Cadastrar Reserva”, que solicita as seguintes informações: CPF, CNPJ e o ícone de usuário para cadastrar um novo cliente e vinculá-lo à reserva — esse botão direciona para a tela de cadastro, que solicita as seguintes informações obrigatórias: nome completo, e-mail, telefone fixo, telefone celular, data de nascimento, sexo, profissão, empresa, nacionalidade e documento de identificação (CPF ou CNPJ). Em seguida, são preenchidos os dados de endereço: CEP, logradouro, número, complemento, bairro, cidade e estado. O cadastro é finalizado ao clicar em “Cadastrar Cliente”. Abaixo desse box de preenchimento, o sistema exibe os quartos disponíveis e indica sua disponibilidade por meio de cores: verde (disponível) e vermelho (ocupado). Além do hóspede responsável pela reserva, é possível adicionar um hóspede secundário, que estará hospedado em conjunto. Para isso, o sistema solicita: nome do hóspede adicional, CPF/CNPJ e número do quarto. Também é possível remover o hóspede adicional, caso necessário. Na sequência, o sistema solicita as datas de check-in e check-out, juntamente com os respectivos horários e os horários limite. Após esse preenchimento, é possível informar o valor da reserva, o tipo de pensão, a forma de pagamento e adicionar observações. O sistema calcula automaticamente o valor total da reserva.O processo de cadastro é finalizado quando o funcionário clica em “Cadastrar Reserva”.  
Para reservas previamente cadastradas, o sistema exibe, em formato de tabela, as seguintes informações: número do quarto, valor da diária, status da reserva (disponível ou ocupada), nome do cliente responsável pela reserva, data de check-in e data de check-out. O sistema também disponibiliza ao funcionário a funcionalidade de serviço de quarto, acessível dentro do módulo de gerenciamento de reservas, permitindo vincular serviços solicitados à respectiva reserva. Ao acessar essa funcionalidade, o sistema exibe os campos para preenchimento com a descrição do serviço e o número do quarto. Abaixo, há uma flag que pode ser marcada caso haja custo adicional associado ao serviço. Após o preenchimento, o cadastro do serviço de quarto é finalizado ao clicar em “Solicitar”. O sistema também permite a atualização dos serviços de quartos já cadastrados. Na parte inferior, é exibido o menu “Meus Pedidos”, com as seguintes informações: descrição do serviço solicitado, status, data e hora da solicitação, número do quarto, existência ou não de taxa adicional, e o valor da taxa, quando aplicável. A segunda funcionalidade do módulo é composta pelo botão “EDITAR”, que, ao ser clicado pelo cliente, o direciona para a tela de edição, solicitando: nome, e-mail, data de check-in e check-out, número do quarto, tipo de cama, quantidade de hóspedes, tipo de pensão, valor total, valor já pago e valor a ser pago, nome do hóspede secundário, CPF/CNPJ, número do quarto e a opção “Adicionar hóspede” para confirmar a edição. Abaixo, é solicitado o status da reserva, composto por: verde (confirmada), amarelo (pendente), azul (em andamento) e vermelho (cancelada). Após as edições serem realizadas, o sistema conta com a funcionalidade de adicionar observações. A edição é finalizada ao clicar em “Salvar reserva”.  
A terceira funcionalidade do módulo é a avaliação da hospedagem, que pode ser preenchida com números de 1 a 5, sendo 1 (péssimo) e 5 (excelente). Abaixo, também há um campo disponível para comentários sobre a hospedagem. A avaliação é finalizada ao clicar em “Finalizar avaliação”.  
**Entrada:** Credenciais do funcionário para acessar o sistema. Dados das reservas cadastradas no banco de dados. Informações inseridas no formulário de cadastro de reservas (Nome do Cliente, Quarto, Check-in, Check-out, Valor, Tipo de Pensão, Observações). Informações para edição da reserva (Valor, Tipo de Pensão, Observações). Solicitação de serviços de quarto com descrição e valor adicional (se aplicável).  
**Origem:** O funcionário autorizado acessa o sistema de gerenciamento de reservas. O banco de dados contém as reservas registradas. O usuário insere ou modifica dados por meio dos formulários.  
**Saída:** Exibição da lista de reservas cadastradas em formato de tabela. Resultado da pesquisa de uma reserva específica. Confirmação de cadastro de novas reservas. Atualização de informações da reserva após edição. Solicitação de serviço de quarto adicionada ao sistema.  
**Destino:** Interface de gerenciamento de reservas dentro do sistema da pousada. Banco de dados onde as informações das reservas são armazenadas e recuperadas.  
**Ação:** O funcionário autorizado acessa o módulo de gerenciamento de reservas.  
A tela exibe a lista de reservas cadastradas, com opções de busca e filtros.  
O usuário pode pesquisar uma reserva específica utilizando o campo de busca.  
Para adicionar uma nova reserva, o usuário clica em "Cadastrar Reserva" e preencher o formulário com os dados necessários.  
O sistema processa a reserva e a armazena no banco de dados.  
Para editar uma reserva existente, o usuário acessa a opção de edição, altera os dados permitidos e salva as modificações.  
Caso necessário, o usuário pode solicitar um serviço de quarto, adicionando uma descrição e um valor adicional.  
O sistema reflete todas as alterações na tabela de gerenciamento de reservas.  
**Pré-condição:** O funcionário deve estar autenticado no sistema com permissões adequadas para gerenciar reservas. O banco de dados deve estar acessível para recuperar e armazenar as informações.  
**Pós-condição:** A lista de reservas é atualizada conforme novos cadastros, edições ou solicitações de serviços de quarto. As informações da reserva são armazenadas corretamente no sistema.  
**Efeitos Colaterais:** Caso um cliente seja cadastrado com informações erradas, será necessário editar posteriormente. Se uma reserva for excluída ou modificada incorretamente, pode ser necessário ajustar manualmente. Caso o sistema apresente falhas na conexão ou erro interno, a reserva pode não ser registrada corretamente. A adição de serviços de quarto pode impactar o valor final da reserva.  
\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_

**ID:** RF-007  
**Nome: Módulo Gerenciamento de Quartos**

#### **Descrição:** O sistema deve permitir que os funcionários da pousada acessem a página de gerenciamento de quartos. Nessa página, os usuários podem pesquisar um quarto específico por meio da barra de pesquisa. Abaixo, o sistema exibe os quartos já previamente cadastrados, com sua numeração, tipo, preço, descrição e capacidade. Caso o quarto em questão não esteja cadastrado, o sistema disponibiliza o botão “Cadastrar quarto”, que solicita as seguintes informações: número do quarto, tipo de cama (solteiro, beliche ou casal), preço por noite, descrição, regras do hotel e imagem do quarto, possibilitando ao usuário realizar o upload da imagem correspondente.

#### **Entrada:** Credenciais do usuário para acessar o sistema. Dados dos quartos cadastrados no banco de dados. Novas informações inseridas via formulários de cadastro e edição (Número do Quarto, Tipo, Preço por Noite, Descrição, Capacidade e Imagens).

#### **Origem:** O funcionário autorizado acessa o sistema de gerenciamento de quartos.O banco de dados contém os registros dos quartos cadastrados. O usuário insere ou modifica dados por meio dos formulários.

#### **Saída:** Exibição da lista de quartos cadastrados em formato de tabela.Resultado da pesquisa de um quarto específico.Cadastro de novos quartos na base de dados.Atualização de informações de um quarto já existente.

#### **Destino:** Interface de gerenciamento de quartos dentro do sistema da pousada. Banco de dados onde as informações são armazenadas e recuperadas.

#### **Ação:** O funcionário autorizado acessa o módulo de gerenciamento de quartos. A tela exibe a lista de quartos cadastrados, com um campo de pesquisa e um botão para cadastrar novos quartos.

* O usuário pode buscar um quarto específico informando critérios no campo de pesquisa.

* Para adicionar um novo quarto, o usuário clica no botão "Cadastrar Novo Quarto" e preenche o formulário com os dados necessários.  
* O sistema permite adicionar imagens e definir a capacidade do quarto.  
* O usuário confirma o cadastro clicando no botão "Adicionar".  
* Para editar um quarto existente, o usuário seleciona a opção de edição, altera os dados necessários e salva as modificações.  
* Todas as alterações são refletidas na tabela principal de gerenciamento.

#### **Pré-condição:** O usuário deve estar autenticado no sistema com permissões adequadas para gerenciar quartos. O banco de dados deve estar acessível para recuperar e armazenar as informações.

#### **Pós-condição:** A lista de quartos é atualizada conforme novos cadastros ou edições. Os dados dos quartos são armazenados corretamente no sistema.

#### **Efeitos Colaterais:** Caso um quarto seja cadastrado com informações erradas, será necessário editá-lo posteriormente. Se um quarto for excluído por engano, pode ser necessário cadastrá-lo novamente. Imagens não compatíveis ou muito grandes podem impactar o carregamento do sistema.

\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_

**ID:**RF-008  
**Nome: Módulo Serviços de Quarto**  
**Descrição:** O sistema deve exibir ao usuário a tela “Relatório de Serviço de Quarto”, contendo, abaixo do título, dois indicadores: “Serviços de Quarto Atendidos” e “Serviços de Quarto Pendentes”. O sistema deve identificar os serviços pendentes na cor vermelha e os atendidos na cor verde, para facilitar o entendimento do usuário. Abaixo, o software apresenta os filtros do relatório, permitindo a seleção de um período através dos campos “De” e “Até”, além do tipo de serviço. Ao lado, o usuário pode concluir filtragem clicando no botão “Filtrar”. Após a aplicação dos filtros, o sistema deve exibir os resultados em formato de tabela, contendo, respectivamente, o número do quarto, o nome do cliente e o valor (R$).  
**Entrada:** Credenciais do usuário para acessar o sistema. Dados dos serviços oferecidos cadastrados no banco de dados. Novas informações inseridas via formulários de cadastro e edição (Nº do Quarto, Nome do Cliente e Valor).  
**Origem:** O funcionário autorizado acessa o sistema de gerenciamento de serviços de quartos.O banco de dados contém os registros dos serviços oferecidos cadastrados. O usuário insere ou modifica dados por meio dos formulários.  
**Saída:** Total de Serviços de Quarto Atendidos (em verde)  
Total de Serviços de Quarto Pendentes (em vermelho)  
Lista dos serviços no período filtrado, contendo: Nº do Quarto, Nome do Cliente e Valor (R$).  
**Destino:** Exibição dos dados na própria tela de relatório, atualizando os campos e a tabela conforme os filtros aplicados.  
**Ação:** Ao clicar no botão Filtrar, o sistema recupera os dados de serviços de quarto com base nos parâmetros informados (data inicial, data final e tipo) e atualiza os indicadores e a tabela de resultados.  
**Pré-condição:** O usuário deve estar autenticado no sistema.  
Deve haver serviços de quarto registrados no sistema (para que haja dados a exibir).  
**Pós-condição:** Os indicadores e a tabela serão atualizados conforme os critérios de filtragem definidos pelo usuário.  
A tela apresentará "0" nos indicadores e nenhuma linha na tabela caso não haja registros no período filtrado.  
**Efeitos Colaterais:** Nenhum dado é alterado no banco de dados, apenas lido e exibido. Pode ocorrer atraso no carregamento caso haja um grande volume de dados.

\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_

**ID:**RF-009  
**Nome: Módulo Clientes**  
**Descrição:** O sistema deve exibir ao usuário a tela de Consulta de Clientes, possibilitando ao funcionário, por meio da barra de "Pesquisar cliente", buscar de maneira rápida um cliente previamente cadastrado, utilizando seu nome completo de registro. Caso o cliente não esteja previamente cadastrado, o sistema disponibiliza a funcionalidade de redirecionar o usuário para a tela "Cadastrar Cliente", onde é possível realizar o cadastro de um novo cliente na base de dados, por meio de formulários com os seguintes campos: nome completo, e-mail, telefone fixo, telefone celular, data de nascimento, sexo, profissão, empresa, nacionalidade e documento de identificação (CPF ou CNPJ). Para os clientes já cadastrados, o sistema exibe as informações em formato de tabela, contendo, respectivamente, o número sequencial do cliente, nome completo, e-mail e telefone. Caso haja necessidade de alguma alteração posterior ao cadastro, o sistema disponibiliza a ferramenta "Editar", que redireciona o usuário novamente à tela de "Cadastrar Cliente", com os dados preenchidos para edição.  
**Entrada:** Credenciais do usuário para acessar o sistema. Dados dos serviços oferecidos cadastrados no banco de dados. Novas informações inseridas via formulários de cadastro e edição (Nome do cliente, Email, Telefone e Botão de edição por cliente).  
**Origem:** O funcionário autorizado acessa o sistema de gerenciamento de clientes.O banco de dados contém os de clientes cadastrados. O usuário insere ou modifica dados por meio dos formulários.  
**Saída:** Lista de clientes exibida em formato de tabela, contendo: Número sequencial, Nome do cliente, Email, Telefone e Botão de edição (por cliente)  
**Destino:**  Exibição dos dados na própria tela de consulta de clientes, atualizando os campos e a tabela conforme os filtros aplicados.  
**Ação:**O sistema exibe todos os clientes cadastrados por padrão. Ao inserir um nome no campo de busca e clicar em “Buscar”, a lista é filtrada com base no nome informado. Ao clicar em “Cadastrar Cliente”, o sistema redireciona o usuário para a tela de cadastro de novo cliente. Ao clicar em “Editar”, o sistema redireciona o usuário para a tela de edição do cliente correspondente.  
**Pré-condição:** O usuário deve estar autenticado no sistema.  
Deve haver serviços de quarto registrados no sistema (para que haja dados a exibir).  
**Pós-condição:** A lista de clientes será atualizada conforme os critérios de busca, se aplicável. O usuário poderá iniciar o processo de cadastro ou edição de cliente.  
**Efeitos Colaterais:** A busca pode não retornar resultados caso o nome digitado não corresponda a nenhum cliente. A edição ou cadastro de clientes pode afetar os dados que serão exibidos futuramente nesta tela.  
\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_

**ID:** RF-010  
**Nome: Módulo Funcionários**  
**Descrição:** O sistema deve permitir que os funcionários da pousada acessem a página de gerenciamento de funcionários. Nessa página, os usuários podem pesquisar um funcionário específico por meio da barra de pesquisa. Caso o funcionário em questão não esteja cadastrado, o sistema disponibiliza o botão “Cadastrar Funcionário”, que solicita as seguintes informações: nome, e-mail, cargo, senha e telefone. Após inserir todos os dados, o cadastro é finalizado ao clicar em “Cadastrar”. Na lista de funcionários já cadastrados previamente, é exibida uma tabela contendo: número do funcionário no sistema, nome completo, e-mail, cargo e telefone. À frente desses dados, o sistema apresenta o botão “Editar”, que direciona para a tela de edição, onde é possível atualizar as informações pessoais do funcionário.  
**Entrada:** Credenciais do funcionário para acessar o sistema. Dados dos funcionários cadastrados no banco de dados. Novos registros de funcionários inseridos via formulário (Nome, Email, Cargo, senha e Telefone).

#### **Origem:** O funcionário autorizado acessa o sistema de gerenciamento. O banco de dados contém as informações dos funcionários cadastrados. O usuário insere novos dados via formulário de cadastro.

#### **Saída:** Exibição da lista de funcionários na tabela da tela de gerenciamento. Resultado da busca por um funcionário específico. Cadastro de novos funcionários no sistema. Atualização dos dados de funcionários existentes.

#### **Destino:** Interface de gerenciamento de funcionários dentro do sistema da Pousada Mazin. Banco de dados para armazenamento e recuperação das informações.

#### **Ação:** O funcionário autorizado acessa o sistema e visualizar a página de gerenciamento de funcionários.

* A tabela exibe a lista de funcionários cadastrados.

* O usuário pode buscar um funcionário específico utilizando o campo de busca.

* Para cadastrar um novo funcionário, o usuário clica no botão "Cadastrar Funcionário" e preenche o formulário.  
* O sistema gera automaticamente um Id para o novo funcionário e o armazena no banco de dados.  
* O usuário pode editar os dados de um funcionário existente através do botão de edição.  
* As alterações são salvas e refletidas na tabela.

#### **Pré-condição:** O usuário deve estar autenticado no sistema com permissões adequadas para gerenciar funcionários. O banco de dados deve estar acessível para recuperar e armazenar as informações.

#### **Pós-condição:** A lista de funcionários é atualizada conforme novas adições ou edições. Os dados cadastrados ou alterados são salvos corretamente no sistema.

#### **Efeitos Colaterais:** Se houver erro na busca, o funcionário pode não ser encontrado. Caso um funcionário seja cadastrado com dados incorretos, será necessário editar posteriormente. Se um funcionário for removido erroneamente, pode ser necessário um novo cadastro.

\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_

**ID:** RF-011  
**Nome: Módulo Gerenciamento Pagamentos**

#### **Descrição:** O sistema deve permitir que os funcionários realizem a baixa de pagamentos de reservas. A interface exibe uma lista de pagamentos pendentes, informando o Tipo, Descrição, Valor e data, permitindo a pesquisa e o processamento de baixas. O usuário pode visualizar detalhes como código do pagamento, número do quarto, cliente, tipo de pagamento e valores. Ao clicar no botão "Baixar", uma tela de confirmação é exibida antes de processar a ação.

#### **Entrada:** Credenciais do funcionário para acessar o sistema. Dados dos pagamentos pendentes cadastrados no banco de dados. Informações inseridas no campo de pesquisa. Confirmação do usuário ao realizar a baixa de um pagamento.

#### **Origem:** O funcionário autorizado acessa o sistema de gerenciamento de pagamentos. O banco de dados contém os registros de pagamentos pendentes. O usuário insere um critério no campo de pesquisa para localizar um pagamento específico.

#### **Saída:** Exibição da lista de pagamentos pendentes em formato de tabela. Resultado da pesquisa de um pagamento específico. Confirmação da baixa do pagamento. Atualização do status do pagamento no sistema.

#### Destino: Interface de gerenciamento de pagamentos dentro do sistema da pousada. Banco de dados onde as informações de pagamento são armazenadas e atualizadas.

#### **Ação:** O funcionário autorizado acessa o módulo de baixa de pagamento. A tela exibe a lista de pagamentos pendentes, com opções de busca e filtros. O usuário pode pesquisar um pagamento específico utilizando o campo de pesquisa. Para processar um pagamento, o usuário clica no botão "Baixar". O sistema exibe uma tela de confirmação para validar a ação. Após a confirmação, o sistema registra a baixa do pagamento e atualiza a tabela.

#### **Pré-condição:** O funcionário deve estar autenticado no sistema com permissões adequadas para gerenciar pagamentos. O banco de dados deve estar acessível para recuperar e armazenar as informações.

#### **Pós-condição:** A lista de pagamentos é atualizada conforme novas baixas são realizadas. O status do pagamento é modificado corretamente no sistema.

#### Efeitos Colaterais: Caso um pagamento seja baixado incorretamente, pode ser necessário reverter a ação manualmente. Se houver erro na busca, um pagamento pode não ser encontrado imediatamente.Falhas na conexão ou erros internos podem impedir a conclusão da baixa do pagamento.

**Efeitos Colaterais:** Alteração de status incorreta caso haja falha de conexão com o banco de dados. Exibição de dados inconsistentes se o filtro for aplicado incorretamente ou os dados estiverem desatualizados.  
\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_

**ID:**RF-012  
**Nome: Módulo Financeiro**  
**Descrição:** O sistema deve exibir a tela de Relatório Financeiro, indicando ao usuário: receita total em verde, despesa total em vermelho e lucro total em azul, facilitando o entendimento do cliente de forma prática para visualizar os informes de rendimento do seu estabelecimento. Abaixo, a tela conta com um filtro de relatórios, que pode ser utilizado com os campos: "De", "Até" e "Tipo", concluindo a busca ao cliente clicando no botão "Filtrar". Após a busca, o sistema deve exibir, em formato de tabela, as transações previamente cadastradas e filtradas pela busca, com as respectivas informações: tipo, descrição, valor e data.  
**Entrada: ** Credenciais do funcionário para acessar o sistema. Data de início (campo “De”). Data de término (campo “Até”). Tipo da transação (Receita, Despesa ou Todos). Ação do botão “Filtrar”.  
**Origem:** Dados financeiros armazenados no banco de dados da aplicação (transações previamente registradas no sistema)  
**Saída: Indicadores no topo da tela com:** Receita Total (em verde), Despesa Total (em vermelho) e Lucro Total (em azul).  
**Lista de transações exibida em formato de tabela, contendo:** Tipo, Descrição, Valor (R$) e Data.  
**Destino** Exibição das informações na tela “Relatório Financeiro”, atualizadas conforme os filtros aplicados.  
**Ação:** O sistema deve exibir os totais de receita, despesa e lucro automaticamente com base nos dados filtrados. Ao preencher os campos de data e/ou tipo e clicar em “Filtrar”, o sistema atualiza os dados da tabela e os indicadores.  
**Pré-condição:** O funcionário deve estar autenticado no sistema com permissões. Deve haver transações financeiras previamente cadastradas na base de dados para exibição.  
**Pós-condição:** Os dados financeiros são exibidos com base no filtro selecionado.  
O usuário tem uma visão clara e resumida da movimentação financeira no período desejado.  
**Efeitos Colaterais:** Caso o filtro aplicado não retorne resultados, os valores exibidos serão todos zerados (R$ 0,00), e a tabela ficará vazia. Aplicar muitos filtros pode limitar excessivamente os dados exibidos.  
\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_  
