# Standard Functions
Doctrine spatial is playing around name of functions. The DQL will respect
the OGC standards. As example to get a geometry from a text, the GeoStandard suggests to
name function ST_GeomFromText(). PostGreSQL and MySQL8 respects this name, but MySQL5 does not.

So when you compose yor DQL query, use ST_GeomFromText.
In this example, we suppose that :
* ```MyEntity``` is your Entity with a geometric property,
* ```geometry``` is your geometric property (SQL column).
* ```SqlTable``` is the real table name of your entity in your SQL database 
* ```geometry_column``` is the real column name in your SQL database 

```dql
SELECT ST_GeomFromText(geometry) FROM MyEntity
```
Then Doctrine layer will create the functional SQL query.
```sql
SELECT ST_GeomFromText(geometry_column) FROM SqlTable
```
All spatial functions declared in the OGC Standard and implemented in this doctrine spatial library.
Some of them have been documented.

You can find OGC Standard documented functions in this [directory](./standard/index.md). Some functions are not documented, yet.
You can find OGC Standard implemented functions in this [directory](../lib/CrEOF/Spatial/ORM/Query/AST/Functions/Standard).

If your application will only be used with one database server, you can use the spectific spatial function of your 
database server. But if your application could be deployed with different database server, you should avoid specific 
non-standard functions. 

You can find PostgreSQL documented functions in this [directory](./postgresql). Some functions are not documented, yet.
You can find PostgreSQL implemented functions in this [directory](../lib/CrEOF/Spatial/ORM/Query/AST/Functions/PostgreSql).

(Do not forget that MySQL is not the best database server and 
[has a lot of issues](https://sqlpro.developpez.com/tutoriel/dangers-mysql-mariadb/), you should avoid it.)
You can find MySQL documented functions in this [directory](./mysql). Some functions are not documented, yet.
You can find MySQL implemented functions in this [directory](../lib/CrEOF/Spatial/ORM/Query/AST/Functions/MySql).


