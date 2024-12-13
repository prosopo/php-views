{{--it's a comment--}}

<div>{{ $var }}</div>

<div>{!! $html !!}</div>

@for($i = 0; $i < 3; $i++)item@endfor

@for($i = 0; $i < 3; $i++)
- item
@endfor

@foreach($items as $item)item@endforeach

@foreach($items as $item)
- item
@endforeach

@if($var)item@endif

@if($var)
-item
@endif

@if($var)
-first
@elseif($var2)
-second
@else
-third
@endif

@php
$a = 1;
@endphp

@use('my\package')

@use("my\package")

@selected($var)

@checked($var)

@class(['first',
'second' => $var])

@switch($var)
@case(1)
- first
@break
@case(2)
- second
@break
@default
- default
@endswitch