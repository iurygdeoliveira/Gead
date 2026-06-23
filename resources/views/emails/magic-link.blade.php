<x-mail::message>
# Acesso ao GeAD

Você solicitou um link de acesso para o sistema GeAD. Clique no botão abaixo para entrar de forma segura, sem precisar de senha.

<x-mail::button :url="$url">
Acessar o Sistema
</x-mail::button>

Ou copie e cole o seguinte link no seu navegador:
[{{ $url }}]({{ $url }})

Este link expira em 15 minutos. Se você não solicitou este link, pode ignorar este e-mail.
</x-mail::message>
