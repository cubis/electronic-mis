# History Attribute in Database #


History Attributes:
| Attribute | Template | Values | Type |  Max length | Hashed? (Y/N) | Notes |
|:----------|:---------|:-------|:-----|:------------|:--------------|:------|
| PK\_History\_ID |          |        | int(11) |             |               |
| Table\_Name |          |        | varchar(22) |             |               |
| Row\_ID   |          |        | int(11) |             |               |
| Column\_Name |          |        | varchar(32) |             |               |
| Old\_Int  |          |        | int(11) |             |               |
| New\_Int  |          |        | int(11) |             |               |
| Old\_Date |          |        | date |             |               |
| New\_Date |          |        | date |             |               |
| Old\_Time |          |        | time |             |               |
| Neww\_Time |          |        | time |             |               | New\_Time couldn’t be a valid name |
| Old\_String |          |        | varchar(2000) |             |               |
| New\_String |          |        | varchar(2000) |             |               |
| Old\_Double |          |        | double(9,2) |             |               |       |
| New\_Double |          |        | double(9,2) |             |               |       |

[Back to Database Elements](http://code.google.com/p/electronic-mis/wiki/Database_Elements)