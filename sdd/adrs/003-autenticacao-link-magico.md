# 3. Autenticação por Link Mágico (Sem Senha no MVP)

Date: 2026-06-09
Status: accepted

## Context

RF-14 e RNF-04 definem que alunos e professores autenticam-se por **link mágico** via e-mail institucional, sem senha. A decisão foi tomada para eliminar o atrito de criação de conta/senha para ~600 alunos e 54 professores que já possuem e-mail institucional cadastrado via importação CSV.

O mecanismo de autenticação do painel admin (Gerente/TAE) é separado — usa o login nativo do Filament (e-mail + senha).

**Parâmetros definidos (RF-14 / RNF-04):**
- Token de uso único (invalidado após primeiro consumo).
- TTL para consumir o link: **30 minutos** (padrão, override por variável de ambiente).
- Sessão após login: expira no **primeiro** entre: (i) 8 horas após autenticação, (ii) 23:59:59 do mesmo dia civil no fuso do campus, (iii) inatividade de 2 horas.
- **Sem rate limit** nas solicitações de envio de link no MVP (risco aceito — RSK-06).
- Tela de confirmação genérica para mitigar enumeração de contas.

## Decision

Implementar autenticação por **link mágico** para os panels Aluno e Professor no Filament v5:

1. Aluno/Professor informa e-mail institucional → sistema envia link com token assinado (URL temporária).
2. Token de uso único, TTL 30min (configurável via `MAGIC_LINK_TTL_MINUTES`).
3. Sessão: 8h / fim do dia / inatividade 2h (cada valor configurável via variável de ambiente).
4. Sem rate limit no MVP. Monitorar volume na implantação (RSK-06).
5. Mensagem de confirmação genérica ("Se o e-mail estiver cadastrado, você receberá um link.").

Admin (Gerente/TAE) usa autenticação nativa do Filament (e-mail + senha).

## Consequences

- **Prós:** Zero atrito de onboarding para alunos/professores; sem gestão de senhas; segurança adequada para o MVP institucional.
- **Contras:** Dependência total da entregabilidade do e-mail (SPF/DKIM devem estar configurados); sem rate limit expõe a fila de e-mail a abuso (RSK-06 aceito).
- **Decisões futuras:** Implementar throttling se RSK-06 se materializar; considerar 2FA ou OAuth institucional em versão futura.
- **Fora de escopo:** Autenticação de admin.
