#top n de tweets en base a lugar y hashtag
plot11 <- function(ntopLugar = 3, ntopTweet = 3) {
   library(XML)
   library(ggplot2)
   library(lubridate)
   doc <- xmlTreeParse("./tweet.xml",useInternal=TRUE)
   data <- xmlToDataFrame(doc)
   data <- data[data$place != "",]
   data$createdat <- strptime(data$createdat, "%a %b %d %H:%M:%S %z %Y")
   data$date <- as.Date(data$createdat,format = "%Y-%m-%d") 
   data$count <- 1
   tweetsPlace <- aggregate(data[, 'count'], by = list(data$place), FUN = sum)
   names(tweetsPlace) <- c("place", "count")
   tweetsPlace <- tweetsPlace[order(tweetsPlace$count, decreasing = TRUE),]
   tweetsPlace <- head(tweetsPlace[,"place"], n=ntopLugar)
   data <- data[data$place %in% tweetsPlace,]
   tweetsHashtag <- aggregate(data[, 'count'], by = list(data$hashtag), FUN = sum)
   names(tweetsHashtag) <- c("hashtag", "count")
   tweetsHashtag <- tweetsHashtag[order(tweetsHashtag$count, decreasing = TRUE),]
   top <- head(tweetsHashtag[,"hashtag"], n=ntopTweet)
   q <- qplot(date, data=data[data$hashtag %in% top,], facets = place ~ ., fill = hashtag)
   q
}









