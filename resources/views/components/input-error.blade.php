@props(['messages' => []])

@foreach ((array) $messages as $message)
    <p {{ $attributes->merge(['class' => 'text-sm font-medium text-destructive mt-1']) }}>{{ $message }}</p>
@endforeach