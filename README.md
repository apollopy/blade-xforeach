# BladeXForeach

A simple blade extension for foreach (supports laravel 4.2+).

## Installation

Requirements
```JSON
"php": ">=5.4.0",
"illuminate/support": "4.2.*"
```

Composer
```JSON
  "repositories": [
    {
      "type": "git",
      "url": "https://github.com/apollopy/blade-xforeach.git"
    }
  ],
  "require": {
    "apollopy/blade-xforeach": "v1.3"
  }
```

Add the service provider in app/config/app.php:
```php
'ApolloPY\BladeXForeach\ServiceProvider'
```

## Example

```php
@foreach ($arr as $key => $val)
    @if ( @isforeachfirst($val) )
        first
    @endif

    key = {{ $key }}
    val = {{ $val }}

    {{ @foreachindex($val) }} <!-- int, starts at 0 -->
    
    {{ @foreachiteration($val) }} <!-- int, starts at 1 -->

    @if ( @isforeachlast($val) )
        end
    @endif
    
    @continue
    @break
@endforeach
```

