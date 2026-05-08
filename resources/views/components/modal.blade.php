@props([])

<!-- ─── MODAL ─── -->
<div class="modal-overlay" id="modalOverlay" onclick="closeModal(event)">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Detalhes do Recurso</div>
            <button class="modal-close" onclick="closeModalDirect()">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="modal-icon-row">
                <div class="modal-icon-box" id="modalIconBox"></div>
                <div>
                    <div class="modal-res-name" id="modalResName"></div>
                    <div class="modal-res-sub" id="modalResSub"></div>
                </div>
            </div>
            <div class="modal-info-row" id="modalInfoRow"></div>
            <div class="modal-actions">
                <button class="modal-btn-abrir" id="modalBtnAbrir">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                        <polyline points="15 3 21 3 21 9" />
                        <line x1="10" y1="14" x2="21" y2="3" />
                    </svg>
                    Abrir Recurso
                </button>
                <button class="modal-btn-emprestar" id="modalBtnEmprestar">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path d="M12 4v16m8-8H4" />
                    </svg>
                    Emprestar
                </button>
                <button class="modal-btn-devolver" id="modalBtnDevolver">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <polyline points="9 14 4 19 9 24" />
                        <path d="M20 4v7a4 4 0 0 1-4 4H4" />
                    </svg>
                    Devolver
                </button>
            </div>
        </div>
    </div>
</div>