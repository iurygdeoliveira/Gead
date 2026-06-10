# 7. Fila Assíncrona via Laravel Queue (Driver Database)

Date: 2026-06-09
Status: proposed

## Context

RF-05 e UC-04 mencionam explicitamente o uso de Laravel Queues para envio de e-mails em lote (~54 e-mails com PDF anexo). O digest diário (RF-13) também enfileirará e-mails. A infraestrutura já inclui PostgreSQL gerenciado (ADR-002).

**Opções de driver avaliadas:**
- `database`: Usa o PostgreSQL existente; zero infra adicional.
- `redis`: Mais rápido, mas adiciona dependência de serviço.
- `sqs`: Desacoplado, mas vendor lock-in AWS.

## Decision

Usar **Laravel Queue** com driver **`database`** (tabela `jobs` no PostgreSQL):

1. Jobs de envio de e-mail (UC-04), digest diário (RF-13), e potencialmente geração de PDF são enfileirados.
2. Worker roda no mesmo container ou como processo separado no Laravel Cloud.
3. Retries com backoff exponencial; `failed_jobs` para monitoramento.
4. Se escala exigir, migrar para Redis é reversível (`QUEUE_CONNECTION=redis`).

## Consequences

- **Prós:** Zero infra adicional; o PostgreSQL já está na stack; suficiente para o volume (~54 e-mails + digest); retry nativo; monitoramento via Filament (pulse ou horizon lite, se aplicável).
- **Contras:** Driver database é mais lento que Redis para alto volume (irrelevante neste caso); tabela `jobs` compartilha carga com queries transacionais (mitigável com connection separada se necessário).
- **Reversibilidade:** Alta — basta trocar `QUEUE_CONNECTION` em `.env`.
