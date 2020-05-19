# FileMaker UUID

A package to generate a FileMaker-compatible numeric UUID.

## Usage

```php
use BrilliantPackages\FileMakerUuid\Uuid;
$uuid = Uuid::numeric();
```

## Format

A 41-digit delimited number of the form:

*  v-r-mmmmmmmmmmmmTsssssss-ccccc@nnnnnnnnnnnnnnn
*  With version 1 and 2 UUIDs, the sections of the UUID correspond to:
*  v: The UUID version (type) number - 1
*  r: A variant code reserved by the RFC 4122 standard - 2
*  m: The creation timestamp (seconds since 0001-01-01T00:00:00) - or as close as we can get with PHP/Unix Epoch
*  s: PHP microseconds
*  c: Random bits ("session key" in FM)
*  n: IP Address as a long ("Device ID" in FM)


## MySQL Query

```sql
SELECT
CONCAT('12', -- as prefix
LPAD(UNIX_TIMESTAMP() + 62125920000,12,'0'), -- as secf, -- 62125920000 = 1970 * 356 * 24 * 60 * 60 // Seconds since year 0
-- 'T' AS T,
LEFT(REPLACE(SUBSTRING(conv(concat(substring(uid,16,3), substring(uid,10,4), substring(uid,1,8)),16,10) / 10000 - (141427 * 24 * 60 * 60 * 1000),10),'.',''),7), -- as usecf,
-- '-' AS dash,
LPAD(FLOOR(0 + RAND() * 99999),5,'0'), --  AS c,
-- '@' AS ahoba,
LPAD(CONCAT(CONV(SUBSTR(uid,25,4),12,10),CONV(SUBSTR(uid,29,4),12,10),CONV(SUBSTR(uid,31,4),12,10),CONV(SUBSTR(uid,35,2),12,10)),15,'0') --  AS node
) AS fm_uuid
FROM ( select uuid() uid ) AS alias;
```
