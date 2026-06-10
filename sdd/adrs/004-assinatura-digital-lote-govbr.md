# 4. Assinatura Digital em Lote via Gov.br com Fallback Local

Date: 2026-06-09
Status: proposed

## Context

RF-04 exige assinatura digital em lote dos relatórios PDF, acionada exclusivamente pelo Gerente de Ensino (UC-03, Fase B). RSK-03 classifica como risco **alto** a possibilidade de falha técnica na integração com a API de assinatura, recomendando PoC antecipada.

O relatório PDF precisa ter validade institucional. O Gov.br (Assina GOV) é a referência mencionada nos artefatos. A alternativa é assinatura local via certificado digital A1/A3.

**Trade-off central:** Depender exclusivamente de API externa (Gov.br) pode travar o ciclo se a API estiver fora do ar; assinatura local é mais autônoma, mas pode não ter o mesmo peso institucional.

## Decision

1. **Primário:** Integrar com a API **Gov.br (Assina GOV)** para assinatura digital em lote.
2. **Fallback:** Assinatura local via **certificado digital A1/A3** usando biblioteca PHP (TCPDF/FPDI + OpenSSL ou equivalente).
3. **PoC obrigatória** (RSK-03): Validar ambos os caminhos antes do release — Gov.br API e fallback local.
4. O sistema deve permitir **alternar** entre os dois modos via configuração de ambiente, sem alteração de código.

## Consequences

- **Prós:** Resiliência — o ciclo não trava se Gov.br estiver indisponível; PoC reduz risco técnico antes do release; ambos os modos cobrem validade institucional.
- **Contras:** Dois caminhos de assinatura significam mais código e testes; a aceitação institucional da assinatura local precisa ser validada com a gestão.
- **Decisões bloqueadas:** Formato final do PDF (layout de carimbo de assinatura) depende do resultado da PoC.
- **Custo operacional:** Gov.br pode exigir credenciamento institucional e renovação periódica.
