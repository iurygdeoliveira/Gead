# UC-04: Enviar Resultados aos Docentes

## 1. Breve Descrição
Permite que o **Gerente de Ensino** dispare, **em um único comando**, o envio por e-mail dos PDFs **assinados** para cada professor. **TAEs** (e o Gerente) acompanham **status** de entrega e podem acionar **reenvio** em caso de falha pontual.

## 2. Atores
- Gerente de Ensino
- TAE (apoio à Gerência)

## 3. Pré-condições
- O processo de consolidação e assinatura (UC-03, Fase B) está finalizado.
- Os PDFs assinados estão disponíveis e vinculados ao cadastro de cada professor.
- Serviço de e-mail (SMTP/API) configurado.

## 4. Fluxo Principal (Happy Path)
1. O **Gerente** acessa o painel com a listagem de relatórios **assinados**.
2. O Gerente clica em **Disparar e-mails (em lote)**.
3. O sistema exibe confirmação (ex.: "54 e-mails prontos para envio. Deseja prosseguir?").
4. O Gerente confirma.
5. O sistema enfileira o envio (Laravel Queues).
6. O sistema envia e-mails com PDF anexo por destinatário.
7. A listagem atualiza status para **Enviado** ou equivalente.

## 5. Fluxos Alternativos e Exceções

### Exceção 1: Falha no Envio para um Professor Específico
1. No envio, o provedor recusa ou ocorre bounce para um destinatário.
2. O sistema registra **Erro de envio** na linha do professor e segue com os demais.
3. **Gerente ou TAE** corrige o e-mail no cadastro, se necessário, e aciona **Reenviar** naquela linha.

### Exceção 2: Tentativa de disparo em lote por TAE
1. Se TAE tentar executar o passo 2 (disparo em lote), o sistema **nega** (403), conforme RF-12.

### Exceção 3: Falha Geral do Serviço de E-mail
1. Falhas consecutivas nas filas (credenciais, cota, etc.).
2. O sistema alerta perfis operacionais e pode pausar a fila até correção.
3. Após ajuste, a fila é retomada.

## 6. Pós-condições
- Professores recebem resultados conforme envios bem-sucedidos; falhas pontuais ficam registradas para reenvio operacional.
