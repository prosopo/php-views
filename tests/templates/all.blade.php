{{--it's a comment--}}

<div>{{ $var }}</div>

<div>{{ $output() }}</div>

<div>{!! $html !!}</div>

@for($i = 0; $i < 3; $i++)item@endfor

@for($i = 0; $i < 3; $i++){{ $i }}@endfor

@for($i = $get(); $i < 3; $i++)item@endfor

@for($i = 0; $i < 3; $i++)
- item
@endfor

@for($i = 0; $i < 3; $i++)
    {{ $i }}
@endfor

@foreach($items as $item)item@endforeach

@foreach($items as $item){{ $item }}@endforeach

@foreach($getItems() as $item)item@endforeach

@foreach($items as $item)
- item
@endforeach

@foreach($items as $item)
    {{ $item }}
@endforeach

@if($var)item@endif

@if($var){{ $output }}@endif

@if($right())item@endif

@if($var)
-item
@endif

@if($var)
{{ $output }}
@endif

@if($var)
-first
@elseif($var2)
-second
@else
-third
@endif

@if($var)
{{ $output }}
@elseif($var2)
{{ $output }}
@else
{{ $output }}
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

@switch($var)
@case(1)
{{ $output1 }}
@break
@case(2)
{{ $output2 }}
@break
@default
{{ $output3 }}
@endswitch