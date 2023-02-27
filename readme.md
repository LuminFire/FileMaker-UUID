# FileMaker UUID

A package to generate a FileMaker-compatible numeric UUID.

## Compatibility

| PHP   | Package Version |
| -----:| ---------------:|
| >=8.0 |             2.x |
|   7.x |             1.x |

## Usage

```php
use BrilliantPackages\FileMakerUuid\Uuid;
$uuid = Uuid::numeric()->toString();

// Results in something like 12063716518403015373000001000000000000000.
```

## Format

A 41-digit delimited number of the form:

*  `vrmmmmmmmmmmmmTssssssscccccnnnnnnnnnnnnnnn`
*  With version 1 and 2 UUIDs, the sections of the UUID correspond to:
   *  `v`: The UUID version (type) number: 1
   *  `r`: A variant code reserved by the RFC 4122 standard: 2
   *  `m`: The creation timestamp (seconds since 0001-01-01T00:00:00), or as close as we can get with PHP/Unix Epoch
   *  `s`: PHP microseconds
   *  `c`: Random bits ("session key" in FM)
   *  `n`: IP Address as a long ("Device ID" in FM)
