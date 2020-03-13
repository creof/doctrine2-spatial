# ST_Buffer

float ST_Area(geometry g1);

float ST_Area(geography geog, boolean use_spheroid=true);

Example:

```php
    $queryBuilder = $manager->createQueryBuilder();
    $queryBuilder
        ->select("id, ST_Area(things.geometry) as area")
        ->from("geometryOfThings", "things");
    $results = $queryBuilder->getQuery()->getResult();
```
