<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Seguro — GeAD</title>
    <!-- Fallback to sans-serif if Inter is not supported in the email client -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background-color: #171918;
            color: #E9ECEB;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            background-color: #171918;
            padding: 40px 20px;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background-color: #1E2220;
            border: 1px solid #2A302D;
            border-radius: 12px;
            overflow: hidden;
        }
        .header {
            background: #242A27; /* Approximate gradient start */
            background: linear-gradient(165deg, #242A27 0%, #1A201E 100%);
            padding: 40px 30px;
            text-align: center;
            border-bottom: 1px solid #2A302D;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .title {
            font-size: 24px;
            font-weight: 700;
            color: #E9ECEB;
            margin-top: 0;
            margin-bottom: 12px;
        }
        .subtitle {
            font-size: 16px;
            color: #C3C9C6;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .seal {
            background-color: #1E2E24; /* Green 600 with 0.08 opacity mixed with bg */
            border: 1px solid #243B2A;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 32px;
            text-align: left;
        }
        .seal-title {
            color: #60D68A;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 4px;
            margin-top: 0;
        }
        .seal-text {
            color: #C3C9C6;
            font-size: 13px;
            margin: 0;
            line-height: 1.5;
        }
        .btn {
            display: inline-block;
            background-color: #268A4A;
            color: #FFFFFF;
            font-weight: 600;
            font-size: 16px;
            text-decoration: none;
            padding: 16px 24px;
            border-radius: 8px;
            width: 100%;
            box-sizing: border-box;
            text-align: center;
        }
        .footer {
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #8B9490;
        }
    </style>
</head>
<body>
    <!-- Inline styles added for max compatibility across email clients -->
    <div class="wrapper" style="width: 100%; background-color: #171918; padding: 40px 20px; font-family: 'Inter', sans-serif;">
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 500px; margin: 0 auto; background-color: #1E2220; border: 1px solid #2A302D; border-radius: 12px; overflow: hidden;">
            <tr>
                <td class="header" style="background-color: #242A27; padding: 40px 30px; text-align: center; border-bottom: 1px solid #2A302D;">
                    <img src="{{ $message->embed(public_path('images/logo.png')) }}" alt="GeAD Logo" style="height: 100px; width: auto; display: block; margin: 0 auto; filter: drop-shadow(0 0 12px rgba(255,255,255,0.95)) drop-shadow(0 0 32px rgba(255,255,255,0.75)) drop-shadow(0 0 60px rgba(255,255,255,0.35)) drop-shadow(0 4px 16px rgba(0,0,0,0.65));">
                </td>
            </tr>
            <tr>
                <td class="content" style="padding: 40px 30px; text-align: center;">
                    <h2 class="title" style="font-size: 24px; font-weight: 700; color: #E9ECEB; margin-top: 0; margin-bottom: 12px;">Acesso Seguro</h2>
                    <p class="subtitle" style="font-size: 16px; color: #C3C9C6; margin-bottom: 30px; line-height: 1.6;">
                        Você solicitou acesso ao sistema GeAD via link mágico. Clique no botão abaixo para entrar sem necessidade de senha.
                    </p>
                    

                    <!-- We use table for the button to ensure it renders correctly in Outlook -->
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td align="center">
                                <a href="{{ $url }}" class="btn" style="display: inline-block; background-color: #268A4A; color: #FFFFFF; font-weight: 600; font-size: 16px; text-decoration: none; padding: 16px 24px; border-radius: 8px; width: 100%; box-sizing: border-box; text-align: center;">Entrar com E-mail Institucional</a>
                            </td>
                        </tr>
                    </table>
                    
                    <p style="color: #8B9490; font-size: 12px; margin-top: 24px; margin-bottom: 0;">
                        Este link é válido por 15 minutos. Se você não solicitou este acesso, pode ignorar este e-mail.
                    </p>
                </td>
            </tr>
        </table>
        
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 500px; margin: 0 auto;">
            <tr>
                <td class="footer" style="padding: 20px; text-align: center; font-size: 12px; color: #8B9490;">
                    © {{ date('Y') }} GeAD — Campus Araguaína · Gerência de Ensino
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
