@props(['items' => []])

@if (count($items))
    <nav aria-label="Breadcrumb" class="mx-auto mb-2 max-w-7xl">
        <ol class="flex flex-wrap items-center gap-2 text-xs font-bold text-[#806856] dark:text-[#bcae9f]">
            <li>
                <a href="{{ route('index') }}"
                    class="rounded-full px-2 py-1 transition hover:bg-[#fff1e6] hover:text-[#FE6807] dark:hover:bg-[#17120f]">Home</a>
            </li>
            @foreach ($items as $item)
                <li class="text-[#c0a590]">/</li>
                <li>
                    @if (!empty($item['url']))
                        <a href="{{ $item['url'] }}"
                            class="rounded-full px-2 py-1 transition hover:bg-[#fff1e6] hover:text-[#FE6807] dark:hover:bg-[#17120f]">{{ $item['label'] }}</a>
                    @else
                        <span
                            class="rounded-full bg-[#fff1e6] px-2 py-1 text-[#FE6807] dark:bg-[#17120f]">{{ $item['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif