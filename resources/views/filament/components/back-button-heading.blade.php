@props([
    'title',
    'url',
])

<div style="display: flex; align-items: center; gap: 0.75rem;">
    <a
        href="{{ $url }}"
        title="Voltar"
        class="bg-white dark:bg-gray-900"
        style="
            display: flex; 
            align-items: center; 
            justify-content: center; 
            width: 2.25rem; 
            height: 2.25rem; 
            border-radius: 0.5rem; 
            border: 1px solid rgba(156, 163, 175, 0.4); 
            color: inherit;
            text-decoration: none;
            flex-shrink: 0;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: opacity 0.2s;
        "
        onmouseover="this.style.opacity='0.7'"
        onmouseout="this.style.opacity='1'"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width: 1.25rem; height: 1.25rem;">
            <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
        </svg>
    </a>
    <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $title }}</span>
</div>
