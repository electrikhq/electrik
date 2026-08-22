@if (class_exists(\Diglactic\Breadcrumbs\Breadcrumbs::class) && \Diglactic\Breadcrumbs\Breadcrumbs::exists())
    {!! \Diglactic\Breadcrumbs\Breadcrumbs::render() !!}
@elseif ($currentTeam ?? auth()->user()?->currentTeam)
    <span class="truncate text-sm text-muted-foreground">{{ ($currentTeam ?? auth()->user()->currentTeam)->name }}</span>
@endif
