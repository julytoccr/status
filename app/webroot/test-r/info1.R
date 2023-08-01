
############# BEGIN
T0 = proc.time()
library(RMySQL)
con = dbConnect(MySQL(), default.file = '/root/confi_MieSeQeLe')

q1='select p.id as Prob, u.login as Login, e.nota as Nota, b.asignatura_id as Asig, e.created as Fecha' 
q2='FROM agrupaciones agr, ejecuciones e, bloques b, problemas p, alumnos al, usuarios u'
q3='WHERE b.asignatura_id in (210,212,216,218,220,222,223,224,225,228,232,233,234,235,236)'
q4='and agr.bloque_id=b.id and agr.problema_id=p.id and' 
q5='e.agrupacion_id=agr.id and e.alumno_id=al.id and al.usuario_id=u.id;'
QR = paste(q1,q2,q3,q4,q5)
ejec = dbGetQuery(con, QR)

q1='select p.id as Idprob, p.nombre as Nombre  FROM agrupaciones agr, bloques b, problemas p   WHERE b.asignatura_id in' 
q2='(210,212,216,218,220,222,223,224,225,228,232,233,234,235,236) and agr.bloque_id=b.id  and agr.problema_id=p.id;'
QR = paste(q1,q2)
prob = dbGetQuery(con, QR)

asig = dbGetQuery(con, "select id as Id, nombre as Nombre from asignaturas where id in (210,212,216,218,220,222,223,224,225,228,232,233,234,235,236);")

prob <- prob[!duplicated(prob),]
all0 <- merge(ejec,asig,by.x="Asig",by.y="Id",all.x=TRUE,all.y=FALSE)
all <- merge(all0,prob,by.x="Prob",by.y="Idprob",all.x=TRUE,all.y=FALSE)


#Depuración de la base inicial:
#quitamos las notas null
all1<-subset(all, !is.na(all$Nota))                                         # all$Nota != 'NULL')
#nos quedamos solo con los que son a partir de septiembre
all1$Fecha <- as.character(all1$Fecha)
all1 <- all1[all1$Fecha>="2014-09-01 00:00:00" , ] 
F = iconv(all1$Nombre.y, from="", to="UTF-8", "byte")
noprobs = c(grep("^Dedica", F), grep("Mis notas", F))
all2 = all1[-noprobs,]
Y14 = c(222,223,224,225,228)
for (i in 1:5) {
   m = paste("M",i,sep='')
   assign(m, all2[all2$Asig == Y14[i],])    
   assign(paste(m, "$Login", sep=''), as.factor(as.character(get(m)$Login)))
   assign(paste(m, "$Nombre.y", sep=''), as.factor(as.character(get(m)$Nombre.y)))
}
css = "<head><style> p {padding: 0px;}</style></head>"
resultado = paste(capture.output(summary(M1)), collapse='<br>')
T = proc.time()
resultado = paste(css, '<pre>', resultado, '</pre><br>', round(T[["elapsed"]]-T0[["elapsed"]], 2), 'seconds elapsed.')

dbDisconnect(con)
