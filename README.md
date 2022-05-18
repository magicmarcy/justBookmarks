## Table Layout

- USER:
  - ID [INT]
  - NAME [TEXT]
  - EMAIL [TEXT]
  - PASS [TEXT]
  - VERIFIED [INT]
  - CREATED [TEXT]
  - LASTLOGIN [TEXT]

- BOOKMARK
  - ID [INT]
  - CATEGORYID [INT]
  - USERID [INT]
  - NAME [TEXT]
  - URL [TEXT]

- CATEGORY
  - ID [INT]
  - NAME [TEXT]
  - USERID [INT]

- PARAMETER
  - ID [INT]
  - NAME [TEXT]
  - VALUE [TEXT]

## Parameter
Bestimmte Bereiche und Funktionen können per Parameter aktiviert/deaktiviert werden. Dafür habe ich folgende Vorgehensweise festgelegt:
Ein Parameter kann für alle oder einen speziellen User angelegt werden. Dafür verfügt die Tabelle PARAMETER über ein Feld USERID. Default ist die USERID immer 0 -> also der Parameter gilt für alle User. Wird der Parameter mit einer speziellen USERID hinterlegt, gilt der Parameter NUR für die angegebene USERID.
