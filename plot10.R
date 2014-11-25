#top n de tweets agrupados por lugar por dia
plot10 <- function(ntop = 3) {
   library(XML)
   library(ggplot2)
   library(lubridate)
   doc <- xmlTreeParse("./tweet.xml",useInternal=TRUE)
   data <- xmlToDataFrame(doc)
   data <- data[data$place != "",]
   data$createdat <- strptime(data$createdat, "%a %b %d %H:%M:%S %z %Y")
   data$date <- as.Date(data$createdat,format = "%Y-%m-%d") 
   data$count <- 1
   tweets <- aggregate(data[, 'count'], by = list(data$place), FUN = sum)
   names(tweets) <- c("place", "count")
   tweets <- tweets[order(tweets$count, decreasing = TRUE),]
   top <- head(tweets[,"place"], n=ntop)
   q <- qplot(date, data=data[data$place %in% top,], facets= place ~ .)
   q
}








