count.no.na <- function(x) sum(!is.na(x))

stat_table = function(tab, dir, res) {   # mean & n
   # dir = 1 <=> alumnos; dir = 2 <=> problemas

   MA=round(apply(tab, dir, mean, na.rm=TRUE), 2)
   SA=apply(tab, dir, count.no.na)
   lab = attributes(MA)$names
   res = paste(res, '<table><tr><th></th><th>Media</th><th>Num.</th>')
   for (j in 1:length(lab)) {
     res = paste(res, '<tr> <td>', lab[j], '</td> <td>', MA[j], '</td> <td>', SA[j], '</td> </tr>')
   }
   res = paste(res, '</table>')
   res
}


############# BEGIN
css = "<head><style> td {padding-left: 5px;} table, th, td {border: 1px solid black;}</style></head>"
T0 = proc.time()
library(RMySQL)
con = dbConnect(MySQL(), default.file = '/root/confi_MieSeQeLe')

q1='select p.id as Prob, u.login as Login, e.nota as Nota, b.asignatura_id as Asig, e.created as Fecha' 
q2='FROM agrupaciones agr, ejecuciones e, bloques b, problemas p, alumnos al, usuarios u'
q3='WHERE b.asignatura_id in (210,212,216,218,220,222,223,224,225,228,232,233,234,235,236) and agr.bloque_id=b.id'
q4='and agr.problema_id=p.id and e.agrupacion_id=agr.id and e.alumno_id=al.id and al.usuario_id=u.id;' 
QR = paste(q1,q2,q3,q4)
ejec = dbGetQuery(con, QR)

q1='select p.id as Idprob, p.nombre as Nombre  FROM agrupaciones agr, bloques b, problemas p   WHERE b.asignatura_id in' 
q2='(210,212,216,218,220,222,223,224,225,228,232,233,234,235,236) and agr.bloque_id=b.id  and agr.problema_id=p.id;'
QR = paste(q1,q2)
prob = dbGetQuery(con, QR)

Y14 = c(222,223,224,225,228)
nual = dbGetQuery(con, paste("select gr.asignatura_id, count(*) from alumnos al, grupos gr where al.grupo_id = gr.id and gr.asignatura_id in (222,223,224,225,228) and gr.nombre in ('1','MY') group by gr.asignatura_id;"))

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
resultado = '<table><tr align="center"><th>M&oacute;dulo</th><th>Nota media global</th><th>N&uacute;mero medio ejecuciones/alumno</th></tr>'
for (i in 1:5) {
   m = paste("M",i,sep='')
   resultado = paste(resultado, '<tr><td>', m, '</td>') 
   assign(m, all2[all2$Asig == Y14[i],])    
   assign(paste(m, "$Login", sep=''), as.factor(as.character(get(m)$Login)))
   assign(paste(m, "$Nombre.y", sep=''), as.factor(as.character(get(m)$Nombre.y)))

   #Selección de notas máximas
   m1<-with(get(m),tapply(Nota,list(Login,Nombre.y),max,na.rm=TRUE))
   #Nota media global
   m11<-as.matrix(m1)
   nmg = round(mean(m11,na.rm=T), 3)
   #Número medio de ejecuciones por alumno
   nej1<-length(get(m)$Login)
   mej1=round(nej1/nual[i,2], 3)
   mej2 = paste('&nbsp;&nbsp;(', nej1, '/', nual[i,2], ')', sep='')
   resultado = paste(resultado, '<td>', nmg, '</td><td>', mej1, mej2, '</td></tr>') 

}
resultado = paste(resultado, '</table>') 

for (i in 1:5) {
   m = paste("M",i,sep='')
   resultado = paste(resultado,"<h3>M&oacute;dulo", m, "</h3><br>")
   assign(m, all2[all2$Asig == Y14[i],])    
   m1<-with(get(m),tapply(Nota,list(Login,Nombre.y),max,na.rm=TRUE))
   resultado = paste(resultado, "Alumnos")
   resultado = stat_table(m1, 1, resultado)
   resultado = paste(resultado, "<br>Problemas")
   resultado = stat_table(m1, 2, resultado)
}

T = proc.time()
resultado = paste(css, resultado, '<br>', round(T[["elapsed"]]-T0[["elapsed"]], 2), 'seconds elapsed.')

dbDisconnect(con)
