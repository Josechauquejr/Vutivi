@props([])

<!-- TOPBAR -->
<header class="topbar">
    <div class="search-box">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#8A8AA0" stroke-width="2.2">
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
        <input type="text" id="searchInput" placeholder="Pesquisar recursos..." />
    </div>
    <div class="topbar-right">
        <button class="notif-btn">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#8A8AA0" stroke-width="2">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
            <span class="notif-dot"></span>
        </button>
        <div class="user-profile">
            <span class="user-name">{{ $userName ?? 'Brown Smith' }}</span>
            <div class="avatar-placeholder">{{ $avatar ?? 'BS' }}</div>
        </div>
    </div>
</header>