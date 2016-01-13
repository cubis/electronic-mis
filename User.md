# User Attribute in Database #

User Attributes
| Attribute | Template | Values | Type |  Max length | Hashed? (Y/N) | Notes |
|:----------|:---------|:-------|:-----|:------------|:--------------|:------|
| PK\_member\_id | 1, 2, 3...etc | #s     | int(11) | N           | AUTO\_INCREMENT |
| FirstName |          |        | varchar(45) |             | Any character |
| LastName  | Abc, aBc...etc | N/A    | varchar(45) | N           | Any character |
| Password  |          |        | varchar(50) | Y           |               |
| Sex       |          |        | varchar(1) | N           |               |
| UserName  |          |        | varchar(22) | N           |               |
| Type      |          |        | smallint | N           | Level of Access |
| Email     |          |        | varchar(45) | N           |               |
| Birthday  |          |        | date | N           |               |
| PhoneNumber |          |        | varchar(14) | N           |               |
| SSN       |          |        | varchar(20) | Y           | hashed        |
| ExpirationDate |          |        | date | N           |               |
| Locked    |          |        | tinyint(1) | N           |               |
| NeedApproval |          |        | tinyint(1) | N           |               |

[Back to Database Elements](http://code.google.com/p/electronic-mis/wiki/Database_Elements)