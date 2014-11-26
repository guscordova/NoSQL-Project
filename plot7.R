#numero de tweets en un rango de fecha
plot7 <- function(startDate = "2014-11-19", endDate = Sys.Date()) {
   library(XML)
   library(ggplot2)
   library(lubridate)
   doc <- xmlTreeParse("./tweet.xml",useInternal=TRUE)
   data <- xmlToDataFrame(doc)
   data$createdat <- strptime(data$createdat, "%a %b %d %H:%M:%S %z %Y")
   data$date <- as.Date(data$createdat,format = "%Y-%m-%d") 
   data <- data[as.Date(data$createdat,format = "%Y-%m-%d") >= as.Date(startDate) &
                   as.Date(data$createdat,format = "%Y-%m-%d") <= as.Date(endDate) ,]
   png(filename = "plot7.png", width = 680, height = 680, units = "px")
   print(qplot(date, data=data,
               main=paste("Tweets entre el dia", 
                          format(as.Date(startDate), format="%d %b %Y"), "y",
                          format(as.Date(endDate), format="%d %b %Y")),
               xlab="Fecha", ylab="Total"))
   dev.off();
}



















