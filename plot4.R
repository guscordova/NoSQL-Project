#top n de tweets agrupados por hashtag
plot4 <- function(date = Sys.Date(), top = 5) {
   library(XML)
   library(ggplot2)
   library(lubridate)
   doc <- xmlTreeParse("./tweet.xml",useInternal=TRUE)
   data <- xmlToDataFrame(doc)
   data$createdat <- strptime(data$createdat, "%a %b %d %H:%M:%S %z %Y")
   data <- data[as.Date(data$createdat,format = "%Y-%m-%d") == as.Date(date),]
   data$count <- 1
   tweets <- aggregate(data[, 'count'], by = list(data$hashtag), FUN = sum)
   names(tweets) <- c("hashtag", "count")
   tweets <- tweets[order(tweets$count, decreasing = TRUE),]
   png(filename = "plot4.png", width = 680, height = 680, units = "px")
   print(qplot(hashtag, count, data=head(tweets, n=top), fill=hashtag,
               main=paste("Top", top, "tweets en el dia", format(as.Date(date), format="%d %b %Y")),
               xlab="Hashtag", ylab="Total") + geom_bar(stat = "identity"))
   dev.off();
}


