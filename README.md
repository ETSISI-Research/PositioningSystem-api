# PositioningSystem-api

## Prerequisites



## Installation



## Notes

<strong>Doctrine notes:</strong>

Create schema:<br>
(i.e) vendor\bin\doctrine orm:schema-tool:create

Command for reverse engineer, from database to php models (xml, yaml, annotations):<br>
vendor\bin\doctrine orm:convert-mapping --from-database annotation api/src/<br>
vendor\bin\doctrine orm:convert-mapping --from-database xml api/src/<br>
vendor\bin\doctrine orm:convert-mapping --from-database yaml api/src/<br>

Note: This mapping could be affected by an Doctrine known issue, it's not allowed to create a mapping with N:M relationships because doctrine does not support N:M relationships with PRIMARY KEYS as FOREIGN KEY

Database Install Considerations:

Some dates like start/end inside Products table should have a default value like CURRENT_DATE

## SQL Commands

UPDATE products SET STATUS = FLOOR( RAND( ) * ( 2 -0 +1 ) ) +0

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


Update Products enddate and startdate fields:

UPDATE products SET enddate= CURRENT_TIMESTAMP + INTERVAL FLOOR(5+RAND() * 14) DAY
