# Appointment Attribute in Database #
Appointment Attributes
| Attribute | Template | Values | Type |  Max length | Hashed? (Y/N) | Notes |
|:----------|:---------|:-------|:-----|:------------|:--------------|:------|
| PK\_AppID |          |        | int(11) |             |               |
| FK\_DoctorID |          |        | int(11) |             |               |
| FK\_PatientID |          |        | int(11) |             |               |
| Date      |          |        | date |             |               |
| Time      |          |        | time |             |               |
| Address   |          |        | varchar(200) |             |               |
| Status    |          |        | varchar(45) |             |               |
| Blood\_Pres |          |        | smallint |             |               |
| Weight    |          |        | smallint |             |               |
| Reason    |          |        | varchar(2000) |             |               |
| Symptoms  |          |        | varchar(2000) |             |               |
| Diagnosis |          |        | varchar(2000) |             |               |
| Treatment |          |        | varchar(2000) |             |               |
| FK3\_Med\_ID |          |        | int(11) |             |               |
| Bill\_Total |          |        | double(9,2) |             |               |       |
| Payment\_Plan |          |        | varchar(2000) |             |               |
| Num\_Months |          |        | smallint |             |               |

[Back to Database Elements](http://code.google.com/p/electronic-mis/wiki/Database_Elements)