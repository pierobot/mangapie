<div class="list-group side-top-menu d-inline-flex d-md-flex flex-wrap flex-md-nowrap flex-row flex-md-column mb-2 mb-md-auto">
    @foreach ($items as $item)
        <a class="list-group-item @if ($active === $item['title']) active @endif"
           href="{{ URL::action($item['action']) }}"
        >
            <span class="fa fa-{{ $item['icon'] }}"></span>

            <div class="d-none d-md-inline-block">
                &nbsp;{{ $item['title'] }}
            </div>
        </a>
    @endforeach
</div>
