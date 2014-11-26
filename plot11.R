#top n de tweets en base a lugar y hashtag
plot11 <- function(date = Sys.Date(), ntopLugar = 3, ntopTweet = 3) {
   library(XML)
   library(ggplot2)
   library(lubridate)
   doc <- xmlTreeParse("./tweet.xml",useInternal=TRUE)
   data <- xmlToDataFrame(doc)
   data <- data[data$place != "",]
   data$createdat <- strptime(data$createdat, "%a %b %d %H:%M:%S %z %Y")
   data$date <- as.Date(data$createdat,format = "%Y-%m-%d") 
   data <- data[as.Date(data$createdat,format = "%Y-%m-%d") == as.Date(date),]
   data$count <- 1
   times <- c(hms("12:00:00"), hms("13:00:00"), hms("14:00:00"), hms("15:00:00"), hms("16:00:00"), hms("17:00:00"), hms("18:00:00"), hms("19:00:00"), hms("20:00:00"), hms("21:00:00"), hms("22:00:00"), hms("23:00:00"), hms("00:00:00"), hms("01:00:00"), hms("02:00:00"), hms("03:00:00"), hms("04:00:00"), hms("05:00:00"), hms("06:00:00"), hms("07:00:00"), hms("08:00:00"), hms("09:00:00"), hms("10:00:00"), hms("11:00:00"))
   data$time <- cut(hour(data$createdat) + minute(data$createdat)/60, hour(times))
   tweetsPlace <- aggregate(data[, 'count'], by = list(data$place), FUN = sum)
   names(tweetsPlace) <- c("place", "count")
   tweetsPlace <- tweetsPlace[order(tweetsPlace$count, decreasing = TRUE),]
   tweetsPlace <- head(tweetsPlace[,"place"], n=ntopLugar)
   data <- data[data$place %in% tweetsPlace,]
   tweetsHashtag <- aggregate(data[, 'count'], by = list(data$hashtag), FUN = sum)
   names(tweetsHashtag) <- c("hashtag", "count")
   tweetsHashtag <- tweetsHashtag[order(tweetsHashtag$count, decreasing = TRUE),]
   top <- head(tweetsHashtag[,"hashtag"], n=ntopTweet)
   png(filename = "plot11.png", width = 680, height = 680, units = "px")
   print(qplot(time, data=data[data$hashtag %in% top,], facets = place ~ ., fill = hashtag,
               main=paste("Tweets por top", ntopLugar ,"lugar y top", ntopTweet, 
                          "hashtag en el dia", 
                          format(as.Date(date), format="%d %b %Y")),
               xlab="Hora", ylab="Total"))
   dev.off();
}









