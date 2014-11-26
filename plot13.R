#wordcloud por hashtag
plot13 <- function(hashtag = "YaMeCanse") { 
   library(XML)
   library(ggplot2)
   library(lubridate)
   library(tm)
   doc <- xmlTreeParse("./tweet.xml",useInternal=TRUE)
   data <- xmlToDataFrame(doc)
   data <- data[data$hashtag == hashtag,]
   text <- data$text
   if (length(text) > 0) {
      text <- as.character(text)
      text <- strsplit(text, " ")
      text <- unlist(text)
      #text <- tolower(text)
      text <- text[text != "RT"] #elimina RT
      text <- text[!(grepl("#", text) | grepl("@", text))] #elimina hashtag y menciones
      text <- text[!grepl("http", text)] #elimina url
      text <- unlist(strsplit(text, "[[:punct:][:space:]]")) #elimina puntuacion
      text <- text[!(text %in% stopwords("spanish"))] #elimina stopwords
      text <- text[text != ""] #elimina palabras vacias
      text <- data.frame(text, replicate(length(text), 1))
      names(text) <- c("text", "count")
      count <- aggregate(text[, 'count'], by = list(text$text), FUN = sum)
      names(count) <- c("text", "count")
      count <- count[order(count$count, decreasing = TRUE),]
      png(filename = "wordcloud.png", width = 680, height = 680, units = "px")
      layout(matrix(c(1, 2), nrow=2), heights=c(.2, 4))
      par(mar=rep(0, 4))
      plot.new()
      text(x=0.5, y=0.5, paste("#", hashtag, sep=""), cex=2)
      print(wordcloud(count$text,count$count, scale=c(3,.3),min.freq=1,
                       max.words=500, random.order=FALSE, rot.per=.15, 
                       colors=brewer.pal(8,"Dark2")))
      dev.off();
   }
}

