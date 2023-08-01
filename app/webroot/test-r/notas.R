to.html <- function(x){
  x <- iconv(x, from="", to="UTF-8", "byte")
  n <- length(x)
  y <- c()
  for(i in 1:n){
    y[i] <- x[i]

    if(grepl("<e1>",y[i])) y[i] <- gsub("<e1>","&aacute;",y[i]) # a con acento
    if(grepl("<e9>",y[i])) y[i] <- gsub("<e9>","&eacute;",y[i]) # e con acento
    if(grepl("<ed>",y[i])) y[i] <- gsub("<ed>","&iacute;",y[i]) # i con acento
    if(grepl("<f3>",y[i])) y[i] <- gsub("<f3>","&oacute;",y[i]) # o con acento
    if(grepl("<fa>",y[i])) y[i] <- gsub("<fa>","&uacute;",y[i]) # u con acento
    
    if(grepl("<c1>",y[i])) y[i] <- gsub("<c1>","&Aacute;",y[i]) # A con acento
    if(grepl("<c9>",y[i])) y[i] <- gsub("<c9>","&Eacute;",y[i]) # E con acento
    if(grepl("<cd>",y[i])) y[i] <- gsub("<cd>","&Iacute;",y[i]) # I con acento
    if(grepl("<d3>",y[i])) y[i] <- gsub("<d3>","&Oacute;",y[i]) # O con acento
    if(grepl("<da>",y[i])) y[i] <- gsub("<da>","&Uacute;",y[i]) # U con acento

    if(grepl("<bf>",y[i])) y[i] <- gsub("<bf>","&iquest;",y[i]) # Interrogante
    if(grepl("<f1>",y[i])) y[i] <- gsub("<f1>","&ntilde;",y[i]) # n con virguilla

  }
  return(y)
}

notas<-function(yo, con)
{
query = paste("select asi.nombre as Asig, p.nombre as Prob, e.nota as Nota, p.id as Id", 
"from asignaturas asi, alumnos al, grupos g, ejecuciones e, agrupaciones ag, problemas p",
"where al.usuario_id =", yo, "and al.grupo_id = g.id and g.asignatura_id = asi.id and
al.id = e.alumno_id and e.agrupacion_id = ag.id and ag.problema_id = p.id and e.nota is not NULL and p.nombre not like 'Dedicaci%' and p.id != 1425;")

notas = dbGetQuery(con, query)

query = paste("select asi.nombre as Asig, p.nombre as Prob, p.id as Id",
"from asignaturas asi, bloques b, agrupaciones ag, problemas p, alumnos al, grupos g",
"where al.usuario_id =", yo, "and al.grupo_id = g.id and g.asignatura_id = asi.id and
asi.id = b.asignatura_id and ag.bloque_id = b.id and ag.problema_id = p.id
and p.nombre not like 'Dedicaci%' and p.id != 1425 and p.id != 1746 order by asi.nombre, p.nombre;")

probs= dbGetQuery(con, query)
ene = dim(probs)[1]
if (ene >0) {fr = table(probs$Asig)}

cnt = 0; j = 1
su = 0
css = "<head><style> td {padding-left: 10px; padding-right: 10px;} th {font-size:120%;}</style></head>"
tab = paste(css, "<table BORDER=1 RULES=ALL FRAME=BOX>")
tab = paste(tab, "<tr bgcolor=#F5E4E4 align=center><td>M&oacute;dulo</td><td>Problema</td><td>Nota</td></tr>")
i=1
while (i <= ene) {
  tab = paste(tab, "<tr>")
  if (cnt==0) {
      Course = probs$Asig[i]
      tab = paste(tab, "<th rowspan =", fr[j]+1, ">", to.html(Course), "</th>")
      j = j+1
  }
  x = notas$Nota[notas$Asig==Course & notas$Id == probs$Id[i]]
  
  if (length(x)==0) { y = 0 } else { y = max(x) }
  tab = paste(tab, "<td>", to.html(probs$Prob[i]), "</td> <td>", y, "</td></tr>")
  su = su+y
  cnt = (cnt+1) %% fr[j-1]
  if (cnt==0) {
    tab = paste(tab, "<td colspan=2  align='right' bgcolor=#DBF3F9><em>Nota de m&oacute;dulo: ", round(su / fr[j-1], 2), "</em></td></tr>")
    su = 0
  }
  i=i+1
}
tab = paste(tab, "</table>")
return(tab)
}
############# BEGIN
library(RMySQL)
con = dbConnect(MySQL(), default.file = '/root/confi_MieSeQeLe')

if (!exists("param")) {
  param = ''
}
logi = paste("'", param, "'", sep="")  # comillas alrededor
XX = dbGetQuery(con, paste("select id from usuarios where login=", logi, ";"))

if (dim(XX)[1] != 0)
{
    yo = XX$id[1]
    tab=notas(yo, con)
    resultado <- tab
}else{
    resultado <- 0
}        

dbDisconnect(con)

