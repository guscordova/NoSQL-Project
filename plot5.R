#top n de tweets agrupados por hashtag por hora
plot5 <- function(ntop = 3) {
   library(XML)
   library(ggplot2)
   library(lubridate)
   doc <- xmlTreeParse("./tweet.xml",useInternal=TRUE)
   data <- xmlToDataFrame(doc)
   data$createdat <- strptime(data$createdat, "%a %b %d %H:%M:%S %z %Y")
   times <- c(hms("12:00:00"), hms("13:00:00"), hms("14:00:00"), hms("15:00:00"), hms("16:00:00"), hms("17:00:00"), hms("18:00:00"), hms("19:00:00"), hms("20:00:00"), hms("21:00:00"), hms("22:00:00"), hms("23:00:00"), hms("00:00:00"), hms("01:00:00"), hms("02:00:00"), hms("03:00:00"), hms("04:00:00"), hms("05:00:00"), hms("06:00:00"), hms("07:00:00"), hms("08:00:00"), hms("09:00:00"), hms("10:00:00"), hms("11:00:00"))
   data$time <- cut(hour(data$createdat) + minute(data$createdat)/60, hour(times))
   data$count <- 1
   tweets <- aggregate(data[, 'count'], by = list(data$hashtag), FUN = sum)
   names(tweets) <- c("hashtag", "count")
   tweets <- tweets[order(tweets$count, decreasing = TRUE),]
   top <- head(tweets[,"hashtag"], n=ntop)
   q <- qplot(time, data=data[data$hashtag %in% top,], facets= hashtag ~ .)
   q
}


