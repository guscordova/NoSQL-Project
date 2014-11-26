#top n de tweets por lugar
plot8 <- function(date = Sys.Date(), ntop = 5) {
   library(XML)
   library(ggplot2)
   library(lubridate)
   doc <- xmlTreeParse("./tweet.xml",useInternal=TRUE)
   data <- xmlToDataFrame(doc)
   data <- data[data$place != "",]
   data$createdat <- strptime(data$createdat, "%a %b %d %H:%M:%S %z %Y")
   data <- data[as.Date(data$createdat,format = "%Y-%m-%d") == as.Date(date),]
   data$count <- 1
   tweets <- aggregate(data[, 'count'], by = list(data$place), FUN = sum)
   names(tweets) <- c("place", "count")
   tweets <- tweets[order(tweets$count, decreasing = TRUE),]
   top <- head(tweets[,"place"], n=ntop)
   png(filename = "plot8.png", width = 680, height = 680, units = "px")
   print(qplot(place, data=data[data$place %in% top,], fill=place,
               main=paste("Top", ntop, "tweets por lugar en el dia", format(as.Date(date), format="%d %b %Y")),
               xlab="Lugar", ylab="Total"))
   dev.off();
}


