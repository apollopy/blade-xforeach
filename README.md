# BladeXForeach

## Example

```php
@foreach ($arr as $key => $val)
    @if ( @isforeachfirst($val) )
        first
    @endif

    key = {{ $key }}
    val = {{ $val }}

    {{ @foreachindex($val) }} <!-- int, starts at 0 -->

    @if ( @isforeachlast($val) )
        end
    @endif
@endforeach
```

