<style>
    .partner-info-container {
        position: absolute;
        top: 1.5rem;
        right: 2rem;
        z-index: 30;
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: center;
        gap: 0.8rem;
        font-size: 0.75rem;
        white-space: nowrap;
        color: #94a3b8;
    }

    .partner-info-link {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
        transition: opacity 0.2s, color 0.2s;
    }

    .partner-info-link:hover span {
        color: var(--primary-500, #ccff03) !important;
    }

    .partner-info-link:hover {
        opacity: 0.85;
    }

    /* Esconde o texto do botão apenas abaixo de 700px */
    @media (max-width: 700px) {
        .fi-btn-text-responsive {
            display: none !important;
        }
    }

    /* Ajuste para telas mobile (abaixo de 1024px) */
    @media (max-width: 1023px) {
        .partner-info-container {
            position: relative !important;
            top: 0 !important;
            right: 0 !important;
            margin-bottom: 1rem !important;
            padding-top: 0.5rem !important;
            display: flex !important;
            justify-content: center !important;
            width: 100% !important;
            gap: 1.5rem !important;
        }
    }
</style>

<div class="partner-info-container">
    @livewire('feedback-widget')

    <div class="partner-info-link" onclick="window.open('http://labsis.dev.br', '_blank'); event.preventDefault(); event.stopPropagation();">
        <span style="font-weight: 700; letter-spacing: 0.02em; transition: color 0.2s;">Precisando de software?</span>
        <div style="display: inline-flex; align-items: center;">
            <img
                src="{{ asset('images/labsis_logo.png') }}"
                alt="LabSis"
                style="height: 1.2rem; width: auto; object-fit: contain;"
            >
        </div>
    </div>
</div>
