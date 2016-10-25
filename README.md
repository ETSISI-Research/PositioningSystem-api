# PositioningSystem-api

[Notes](#notes)

## Prerequisites



## Installation



## Notes

<strong>Doctrine notes:</strong>

Create schema:
```
vendor\bin\doctrine orm:schema-tool:create
```

Command for reverse engineer, from database to php models (xml, yaml, annotations):
```
vendor\bin\doctrine orm:convert-mapping --from-database annotation api/src/<br>
vendor\bin\doctrine orm:convert-mapping --from-database xml api/src/<br>
vendor\bin\doctrine orm:convert-mapping --from-database yaml api/src/<br>
```

Note: This mapping could be affected by an Doctrine known issue, it's not allowed to create a mapping with N:M relationships because doctrine does not support N:M relationships with PRIMARY KEYS as FOREIGN KEY

Database Install Considerations:

Some dates like start/end inside products table should have a default value like CURRENT_DATE

## SQL Commands

- Assign a random status to all the products
```
UPDATE products SET STATUS = FLOOR( RAND( ) * ( 2 -0 +1 ) ) +0
```

- Assign the current date to every item in the database
```
UPDATE products SET creationDate = CURRENT_TIMESTAMP();
UPDATE products SET startDate = CURRENT_TIMESTAMP();
UPDATE products SET endDate = DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL 10 DAY);
UPDATE contacts SET creationDate = CURRENT_TIMESTAMP();
UPDATE families SET creationDate = CURRENT_TIMESTAMP();
UPDATE messages SET creationDate = CURRENT_TIMESTAMP();
UPDATE partners SET creationDate = CURRENT_TIMESTAMP();
UPDATE projects SET creationDate = CURRENT_TIMESTAMP();
UPDATE snapshots SET creationDate = CURRENT_TIMESTAMP();
UPDATE subfamilies SET creationDate = CURRENT_TIMESTAMP();
UPDATE users SET creationDate = CURRENT_TIMESTAMP();
```

Update products endDate field width random values

```
UPDATE products SET enddate= CURRENT_TIMESTAMP + INTERVAL FLOOR(5+RAND() * 14) DAY
```
