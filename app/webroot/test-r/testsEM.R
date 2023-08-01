Num_preguntas = function(nom.txt) {
  T = readLines(url(paste0("http://naxos.upc.edu/teaching/best/", nom.txt)))
  x = grep("^@", T)
  length(x)
}

css = "<head><style> body {font-family: sans-serif;} td {padding: 2px;} table, th, td {border: 1px solid black; text-align: center;}</style></head>"

############# BEGIN
library(RMySQL)
con = dbConnect(MySQL(), default.file = '/root/confi_MieSeQeLe')

if (!exists("param")) {   # nombre asignatura, ejemplo "Estad Med T15"
  id.asig = 247
  param = 0
} else {
  Q = paste0("select id from asignaturas where nombre='", param, "';")
  ejec = dbGetQuery(con, Q)
  if (dim(ejec)[1]==1) {
     id.asig = ejec[1,1]
  } else {
     param = -1
     resultado = "El nombre de asignatura no existe"
     dbDisconnect(con)
  }
}

if (param != -1) {
##-- alumnos del curso
 Q = paste0("select alu.usuario_id User, us.login Login, us.apellidos Nombre from alumnos alu, grupos gr, usuarios us where gr.asignatura_id=", id.asig, " and alu.grupo_id=gr.id and alu.usuario_id=us.id;")
 alcu = dbGetQuery(con, Q)

 datos0 <- read.table(url("http://ka.upc.es/estadisticas/notas_test.dat"),sep="")
 names(datos0) <- c("wday","month","day","hour","year","user","test","question","res")

##--Usuarios  pepe, dummy y profCM
 user.pepe <- 8631
 user.dummy <- 14384
 user.profcm <- 14374
 user.rm <- which(datos0$user %in% c(user.pepe, user.dummy,user.profcm))
 if (length(user.rm)>0){datos1 <- datos0[-user.rm,]}else{datos1 <- datos0}

##-- Fecha
 fechas.char <- with(datos1,paste(year,"-",month,"-",ifelse(day<10,paste(0,day,sep=""),day),sep=""))
 datos1$fecha <- as.Date(fechas.char,format="%Y-%b-%d")

##-- Solo tests de EM
 G = datos1[grep('^EM[1-9]$', datos1$test),]

##-- Remove columns
 datos <- G[,-c(1:5)]

##-- Número preguntas
 Tests = 4   # numerados de 1 a Tests 

 npr = array(NA, dim=Tests)
 not = array(NA, dim=Tests)
 for (i in 1:Tests) {
    npr[i] = Num_preguntas(sprintf("EstadMed-%d.txt", i))
 }

 Tab = '<table><tr><th>User</th>'
 niveles = c()
 for (i in 1:Tests) {
    Tab = paste0(Tab, '<th>Test ', i, '</th>')
    niveles = c(niveles, paste0("EM", i))
 }
 Tab = paste0(Tab, '<th>Calificacion</th></tr>')

 curris = sort(unique(datos$user))
 for (a in curris) {
    k = which(alcu$User==a)
    aux = subset(datos, datos$user==a)
    aux$test = factor(aux$test, levels=niveles)
    g = tapply(aux$res, list(aux$question, aux$test), max)
    Tab = paste0(Tab, '<tr><td>', alcu$Login[k], '</td>')
    for (i in 1:Tests) {
        h = is.na(g[,i])
        j = g[!h, i]
	not[i] = ifelse(sum(j)>=35, 10, sum(j)/3.5)
        Tab = paste0(Tab, '<td>', sum(j), ' / ', length(j), '<br>', round(not[i],1),'</td>')
    }
    Tab = paste0(Tab, '<td>', round(mean(not), 1), '</td></tr>')
 }
 Tab = paste0(Tab, '<tr><td>TOT. preg.</td>')
 for (i in 1:Tests) 
    Tab = paste0(Tab, '<td>', npr[i], '</td>')
 Tab = paste0(Tab, '</tr></table>')

 resultado = paste(css, Tab, "<p>Las celdas muestran las preguntas bien contestadas (alguna vez), las preguntas contestadas (sin contar repeticiones) y la nota provisional en base a un objetivo de 35 aciertos")

 dbDisconnect(con)
}