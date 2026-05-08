@props([
    'id',
    'iconClass',
    'ext',
    'title',
    'statusClass',
    'status',
    'metaText1',
    'metaText2',
    'desc',
    'dataType' => null,
    'dataOwner' => null,
])

@php
    $iconSvg = match($iconClass) {
        'pdf' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" /><polyline points="14 2 14 8 20 8" /><line x1="16" y1="13" x2="8" y2="13" /><line x1="16" y1="17" x2="8" y2="17" /><polyline points="10 9 9 9 8 9" />',
        'doc' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" /><polyline points="14 2 14 8 20 8" /><line x1="16" y1="13" x2="8" y2="13" /><line x1="16" y1="17" x2="8" y2="17" />',
        'pptx' => '<rect x="2" y="3" width="20" height="14" rx="2" /><line x1="8" y1="21" x2="16" y2="21" /><line x1="12" y1="17" x2="12" y2="21" />',
        'video' => '<polygon points="23 7 16 12 23 17 23 7" /><rect x="1" y="5" width="15" height="14" rx="2" />',
        'xlsx' => '<rect x="3" y="3" width="18" height="18" rx="2" /><line x1="3" y1="9" x2="21" y2="9" /><line x1="3" y1="15" x2="21" y2="15" /><line x1="9" y1="3" x2="9" y2="21" />',
        'zip' => '<path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />',
        'physical' => '<path d="M4 7h16v10H4z" /><path d="M6 7v10" /><path d="M18 7v10" />',
        default => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" /><polyline points="14 2 14 8 20 8" />'
    };

    $metaIcon1Svg = '<svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" /><line x1="16" y1="2" x2="16" y2="6" /><line x1="8" y1="2" x2="8" y2="6" /><line x1="3" y1="10" x2="21" y2="10" /></svg>';
    $metaIcon2Svg = '<svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" /><polyline points="7 10 12 15 17 10" /><line x1="12" y1="15" x2="12" y2="3" /></svg>';
@endphp

<!-- Resource Card -->
<div class="resource-card" data-id="{{ $id }}" @if($dataType) data-type="{{ $dataType }}" @endif @if($dataOwner) data-owner="{{ $dataOwner }}" @endif>
    <div class="res-icon-box icon-{{ $iconClass }}">
        <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.8">
            {!! $iconSvg !!}
        </svg>
        <span class="res-ext">{{ $ext }}</span>
    </div>
    <div class="res-info">
        <div class="res-header">
            <div class="res-title">{{ $title }}</div>
            <span class="status-badge status-{{ $statusClass }}">{{ $status }}</span>
        </div>
        <div class="res-meta">
            <span>
                {!! $metaIcon1Svg !!}
                {{ $metaText1 }}
            </span>
            <span>
                {!! $metaIcon2Svg !!}
                {{ $metaText2 }}
            </span>
        </div>
        <p class="res-desc">
            {{ $desc }}
        </p>
        <div class="res-actions">
            <button class="btn-abrir" onclick="openModal({{ $id }})">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                    <polyline points="15 3 21 3 21 9" />
                    <line x1="10" y1="14" x2="21" y2="3" />
                </svg>
                Abrir
            </button>
            <button class="btn-devolver" onclick="devolver({{ $id }})">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <polyline points="9 14 4 19 9 24" />
                    <path d="M20 4v7a4 4 0 0 1-4 4H4" />
                </svg>
                Devolver
            </button>
            <button class="btn-share">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="18" cy="5" r="3" />
                    <circle cx="6" cy="12" r="3" />
                    <circle cx="18" cy="19" r="3" />
                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49" />
                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49" />
                </svg>
            </button>
        </div>
    </div>
</div>