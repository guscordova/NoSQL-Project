#top n de tweets agrupados por lugar por dia
plot10 <- function(startDate = "2014-11-19", endDate = Sys.Date(), ntop = 3) {
   library(XML)
   library(ggplot2)
   library(lubridate)
   doc <- xmlTreeParse("./tweet.xml",useInternal=TRUE)
   data <- xmlToDataFrame(doc)
   data <- data[data$place != "",]
   data$createdat <- strptime(data$createdat, "%a %b %d %H:%M:%S %z %Y")
   data$date <- as.Date(data$createdat,format = "%Y-%m-%d") 
   data <- data[as.Date(data$createdat,format = "%Y-%m-%d") >= as.Date(startDate) &
                   as.Date(data$createdat,format = "%Y-%m-%d") <= as.Date(endDate) ,]
   data$count <- 1
   tweets <- aggregate(data[, 'count'], by = list(data$place), FUN = sum)
   names(tweets) <- c("place", "count")
   tweets <- tweets[order(tweets$count, decreasing = TRUE),]
   top <- head(tweets[,"place"], n=ntop)
   png(filename = "plot10.png", width = 680, height = 680, units = "px")
   print(qplot(date, data=data[data$place %in% top,], facets= place ~ ., fill=place,
               main=paste("Tweets por lugar entre el dia", 
                          format(as.Date(startDate), format="%d %b %Y"), "y",
                          format(as.Date(endDate), format="%d %b %Y")),
               xlab="Fecha", ylab="Total") + theme(legend.position="none"))
   dev.off();
}








