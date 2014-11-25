#top n de tweets por lugar
plot8 <- function(ntop = 10) {
   library(XML)
   library(ggplot2)
   library(lubridate)
   doc <- xmlTreeParse("./tweet.xml",useInternal=TRUE)
   data <- xmlToDataFrame(doc)
   data <- data[data$place != "",]
   data$count <- 1
   tweets <- aggregate(data[, 'count'], by = list(data$place), FUN = sum)
   names(tweets) <- c("place", "count")
   tweets <- tweets[order(tweets$count, decreasing = TRUE),]
   top <- head(tweets[,"place"], n=ntop)
   q <- qplot(place, data=data[data$place %in% top,])
   q
}


