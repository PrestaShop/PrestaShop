# Foreign key Generator

This script will break your shop but try to add all foreign keys that were
removed during the Shop installation.

This script have known bugs, must be updated manually everytime we update a table and is not tested.

Once you have executed it, you can use modelisation tools like MySQL Workbench to generate an EER or MCD diagram.

## How to use it?

```
./tools/foreignkeyGenerator/foreign.php
```

* *relations.php* file contains the relations between tables;
* *changes.php* applies some changes to tables;
