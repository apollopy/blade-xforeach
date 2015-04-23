# BladeXForeach

## Example

```php
@foreach ($arr as $key => $val)
    @if ( @isforeachfirst($val) )
        first
        <br />
    @endif

    key = {{ $key }}
    <br />
    val = {{ $val }}
    <br />

    {{ @foreachindex($val) }}
    <br />

    @if ( @isforeachlast($val) )
        end
        <br />
    @endif
    <hr />
@endforeach
```

