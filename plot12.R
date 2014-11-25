#top n de tweets en base a hashtag y lugar
plot12 <- function(ntopTweet = 3, ntopLugar = 3) {
   library(XML)
   library(ggplot2)
   library(lubridate)
   doc <- xmlTreeParse("./tweet.xml",useInternal=TRUE)
   data <- xmlToDataFrame(doc)
   data <- data[data$place != "",]
   data$createdat <- strptime(data$createdat, "%a %b %d %H:%M:%S %z %Y")
   data$date <- as.Date(data$createdat,format = "%Y-%m-%d") 
   data$count <- 1
   tweetsHashtag <- aggregate(data[, 'count'], by = list(data$hashtag), FUN = sum)
   names(tweetsHashtag) <- c("hashtag", "count")
   tweetsHashtag <- tweetsHashtag[order(tweetsHashtag$count, decreasing = TRUE),]
   tweetsHashtag <- head(tweetsHashtag[,"hashtag"], n=ntopTweet)
   data <- data[data$hashtag %in% tweetsHashtag,]
   tweetsPlace <- aggregate(data[, 'count'], by = list(data$place), FUN = sum)
   names(tweetsPlace) <- c("place", "count")
   tweetsPlace <- tweetsPlace[order(tweetsPlace$count, decreasing = TRUE),]
   top <- head(tweetsPlace[,"place"], n=ntopLugar)
   q <- qplot(date, data=data[data$place %in% top,], facets = hashtag ~ ., fill = place)
   q
}










