# ST_AsBinary

bytea ST_AsBinary(geometry g1);

bytea ST_AsBinary(geometry g1, text NDR_or_XDR);

bytea ST_AsBinary(geography geog);

bytea ST_AsBinary(geography geog, text NDR_or_XDR);

Example:

```php
    $queryBuilder = $manager->createQueryBuilder();
    $queryBuilder
        ->select("id, ST_Area(things.geometry) as area")
        ->from("geometryOfThings", "things");
    $results = $queryBuilder->getQuery()->getResult();
```
