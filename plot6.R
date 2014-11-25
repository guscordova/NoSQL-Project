#top n de tweets agrupados por hashtag por dia
plot6 <- function(ntop = 3) {
   library(XML)
   library(ggplot2)
   library(lubridate)
   doc <- xmlTreeParse("./tweet.xml",useInternal=TRUE)
   data <- xmlToDataFrame(doc)
   data$createdat <- strptime(data$createdat, "%a %b %d %H:%M:%S %z %Y")
   data$date <- as.Date(data$createdat,format = "%Y-%m-%d") 
   data$count <- 1
   tweets <- aggregate(data[, 'count'], by = list(data$hashtag), FUN = sum)
   names(tweets) <- c("hashtag", "count")
   tweets <- tweets[order(tweets$count, decreasing = TRUE),]
   top <- head(tweets[,"hashtag"], n=ntop)
   q <- qplot(date, data=data[data$hashtag %in% top,], facets= hashtag ~ .)
   q
}




