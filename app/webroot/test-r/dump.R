############# BEGIN
library(RMySQL)
con = dbConnect(MySQL(), default.file = '/root/confi_MieSeQeLe')

asig = dbGetQuery(con, "select id as Id, nombre as Nombre from asignaturas where id in (210,212,216,218,220,222,223,224,225,228,232,233,234,235,236);")

q1='select p.id as Prob, u.login as Login, e.nota as Nota, b.asignatura_id as Asig, e.created as Fecha' 
q2='FROM agrupaciones agr, ejecuciones e, bloques b, problemas p, alumnos al, usuarios u'
q3='WHERE b.asignatura_id in (210,212,216,218,220,222,223,224,225,228,232,233,234,235,236) and agr.bloque_id=b.id'
q4='and agr.problema_id=p.id and e.agrupacion_id=agr.id and e.alumno_id=al.id and al.usuario_id=u.id;' 
QR = paste(q1,q2,q3,q4)
ejec = dbGetQuery(con, QR)

q1='select b.asignatura_id as Asig, p.id as Idprob, p.nombre as Nombre, b.nombre as Capit  FROM agrupaciones agr, bloques b, problemas p   WHERE b.asignatura_id in' 
q2='(210,212,216,218,220,222,223,224,225,228,232,233,234,235,236) and agr.bloque_id=b.id  and agr.problema_id=p.id;'
QR = paste(q1,q2)
prob = dbGetQuery(con, QR)

nual = dbGetQuery(con, "select gr.asignatura_id, gr.nombre, count(*) from alumnos al, grupos gr where al.grupo_id = gr.id and gr.asignatura_id in (210,212,216,218,220,222,223,224,225,228,232,233,234,235,236) group by gr.asignatura_id, gr.nombre;")

setwd("/home/estatusadmin/aplicaciones/codigo/PFC/app/webroot/test-r")
save(asig, file='asignaturas.Rdata')
save(ejec, file='ejecuciones.Rdata')
save(prob, file='problemas.Rdata')
save(nual, file='numalumnos.Rdata')

resultado = dim(asig)

dbDisconnect(con)
