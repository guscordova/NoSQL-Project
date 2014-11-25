#numero de tweets en un rango de fecha
plot7 <- function(startDate = "2014-11-20", endDate = "2014-11-24") {
   library(XML)
   library(ggplot2)
   library(lubridate)
   doc <- xmlTreeParse("./tweet.xml",useInternal=TRUE)
   data <- xmlToDataFrame(doc)
   data$createdat <- strptime(data$createdat, "%a %b %d %H:%M:%S %z %Y")
   data$date <- as.Date(data$createdat,format = "%Y-%m-%d") 
   data <- data[as.Date(data$createdat,format = "%Y-%m-%d") >= as.Date(startDate) &
                   as.Date(data$createdat,format = "%Y-%m-%d") <= as.Date(endDate) ,]
   q <- qplot(date, data=data)
   q
}



















