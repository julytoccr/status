library(RMySQL)
con = dbConnect(MySQL(), default.file = '/root/confi_MieSeQeLe')
Y = dbGetQuery(con, "select nombre Asignaturas from asignaturas where id>120;")
resultado = Y
dbDisconnect(con)
