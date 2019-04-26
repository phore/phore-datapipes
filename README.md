# Phore Datapipes

## Reading CSV 

```php
$csv = new CsvInput("/path/to/file.csv", ";");
$csv->setIngoreLinesStartingWith(["#"]);
$csv->readHeader();

foreach ($csv->getData() as $row) {
    echo $row["header1"] . $row["header2"];
}
```

Features:
- Ingores empty Lines
- Ingores Comments (optional)
- Strict mode for checking for column count
- Reads Header (optional)



## DateTime Reading

Parse files on the harddisk for timestamps and bring them into correct order

```php



```
