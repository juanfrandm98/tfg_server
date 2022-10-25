

########################################################
# author:  ZheyuanLi
#####################
CubicInterpSplineAsPiecePoly <- function (x, y, method = c("fmm", "natural", "periodic", "hyman")) {
  ## method validation
  if (!(method %in% c("fmm", "natural", "periodic", "hyman")))
    mystop("'method' must be one of the following: 'fmm', 'natural', 'periodic', 'hyman'!")
  ## use `splinefun` for cubic spline interpolation
  CubicInterpSpline <- stats::splinefun(x, y, method)
  ## extract construction info
  construction_info <- environment(CubicInterpSpline)$z
  ## export as an "PiecePoly" object
  pieces <- seq_len(length(construction_info$x) - 1L)
  PiecePolyCoef <- with(construction_info, rbind(y[pieces], b[pieces], c[pieces], d[pieces], deparse.level = 0L))
  structure(list(PiecePoly = list(coef = PiecePolyCoef, shift = TRUE),
                 knots = construction_info$x), method = method,
                 class = c("PiecePoly", "CubicInterpSpline"))
  }

  ###########################################################

## represent a fitted smoothing spline as an interpolation spline
SmoothSplineAsPiecePoly <- function (SmoothSpline) {
  ## input validation
  if (!inherits(SmoothSpline, "smooth.spline"))
    mystop("This function only works with models that inherit 'smooth.spline' class!")
  ## knots of the smoothing spline
  kx <- with(SmoothSpline$fit, knot * range + min)
  kx <- kx[4:(length(kx) - 3)]
  ky <- predict(SmoothSpline, kx, 0L)[[2]]  ## deriv = 0L
  ## natural cubic spline interpolation over the knots
  CubicInterpSplineAsPiecePoly(kx, ky, method = "natural")
  }

  ##########################################################
  solve <- function (a) {
  ## change symbol
  PiecePolyObject <- a

  ## extract piecewise polynomial coefficients
  PiecePolyCoef <- PiecePolyObject$PiecePoly$coef
  shift <- PiecePolyObject$PiecePoly$shift
  n_pieces <- dim(PiecePolyCoef)[2L]
  ## extract knots
  x <- PiecePolyObject$knots
  ## list of roots on each piece
  xr <- vector("list", n_pieces)
  ## loop through pieces
  i <- 1L
  while (i <= n_pieces) {
    ## polynomial coefficient
    pc <- PiecePolyCoef[, i]
    ## take derivative
    pcd <- pc[-1] * c(1:3)
    ## complex roots
    croots <- base::polyroot(pcd)
    ## real roots (be careful when testing 0 for floating point numbers)
    rroots <- Re(croots)[round(Im(croots), 10) == 0]
    ## is shifting needed?
    if (shift) rroots <- rroots + x[i]
#    write("rroots","")
#    write(rroots,"")
#    write(x[i],"")
#     write(x[i+1],"")
        ## real roots in (x[i], x[i + 1])
       if (length(rroots)>0)

   xr[[i]]<-addRoots(rroots, pcd, x[i],x[i+1])

    ## next piece
    i <- i + 1L
    }
  ## collapse list to atomic vector and return
  unlist(xr)
  }

  #################################################
  getY<-function(x, pol3)
  {
  return (pol3[1]+pol3[2]*x+pol3[3]*x^2)
  }

  ####################################################

  addRoots<-function(roots, pcd, l1, l2)
  {
   #return(roots[(roots >= l1) & (roots <= l2)])
  r<-c()
  for (i in 1:length(roots))
  {
 if (roots[i]>=l1 & roots[i]<=l2)
 {
 #write(paste("roots[i]:",roots[i],sep=""),"")
 #write(paste("left:",l1, ", right:", l2,sep=""),"")
 # write(paste("leftY:",getY(roots[i]-1,pcd),sep=""),"")
 #   write(paste("rightY:",getY(roots[i]+1,pcd),sep=""),"")
 }
   if (roots[i]>=l1 & roots[i]<=l2 & getY(roots[i]-l1-1e-05,pcd)>0 & getY(roots[i]-l1+1e-05,pcd)<0)
   r<-c(r,roots[i])
   }

   return (r)
    }

##############################################
###################################################
###################################################


readData<-function(file, column, sRate, char=" ")
{
data<-read.table(file, sep=char)[,column]
return(centerSignal(data, sRate))
}

##############################
centerSignal<-function(signal, sRate)
    {
      return(signal[(sRate + 1):(length(signal) -sRate)])
    }


  ###################################
filterSignal<-function(signal, sRate, lowcut = 0, highcut = 15, filter_order = 1)
    {
     nyquist_freq = 0.5 * sRate
    low = lowcut/nyquist_freq
    high = highcut/nyquist_freq
    bandpass <- signal::butter(n = filter_order, W = c(low, high), type = "pass")
    signal_filt <- signal::filtfilt(bandpass, c(rep(signal[1],
        sRate), signal, rep(signal[length(signal)], sRate)))
    signal_filt <- centerSignal(signal_filt, sRate)
        return (signal_filt)
    }

#############################################
detect_rpeaks<-function (signal, sRate)
{
   ssignal<-smooth.spline(signal, spar=0.3)
   oo <- SmoothSplineAsPiecePoly(ssignal)
   peaks <- solve(oo)
    return(peaks/sRate)
}

#############################################
getIntervales<-function(peaks, needConversion)
{
  distances<-vector()
  cnt<-2

  while(cnt <= length(peaks)) {
    interval<-peaks[cnt] - peaks[cnt - 1]
    interval<-abs(interval)

    if(needConversion) dist<-(interval * 1000.0)
    else dist<-interval

    distances<-c(distances, dist)
    cnt<-cnt + 1
  }

  return(distances)
}

#############################################
getSqIntervales<-function(peaks)
{
  sqDistances<-vector()
  cnt<-2

  while(cnt <= length(peaks)) {
    interval<-peaks[cnt] - peaks[cnt - 1]
    dist<-(interval * 1000.0)
    sqDistances<-c(sqDistances, dist * dist)
    cnt<-cnt + 1
  }

  return(sqDistances)
}

#############################################
calcBPM<-function(peaks)
{
  distances<-getIntervales(peaks, T)
  bpm<-60000/mean(distances)
  return(bpm)
}

#############################################
calcIBI<-function(peaks)
{
  distances<-getIntervales(peaks, T)
  ibi<-mean(distances)
  return(ibi)
}

#############################################
calcSDNN<-function(peaks)
{
  distances<-getIntervales(peaks, T)
  sdnn<-sd(distances)
  return(sdnn)
}

#############################################
calcSDSD<-function(peaks)
{
  distances<-getIntervales(peaks, T)
  difDistances<-getIntervales(distances, F)
  sdsd<-sd(difDistances)
  return(sdsd)
}

#############################################
calcRMSSD<-function(peaks)
{
  distances<-getSqIntervales(peaks)
  rmssd<-sqrt(mean(distances))
  return(rmssd)
}

#############################################
calcPNN<-function(peaks, num)
{
  distances<-getIntervales(peaks, T)
  difDistances<-getIntervales(distances, F)

  pnn<-0

  for(distance in difDistances) {
    if(distance > num) pnn<-pnn + 1
  }

  return(pnn/length(difDistances))
}

#################################
######################## MAIN #############
#######################################

# example of use:
# $ Rscript filtroSplinBatch.R valores_crudos_30fsMar_3canales.txt 1 valores_crudos_30fsMar_3canales.txt.xPeaks 30 1 ","





args = commandArgs(trailingOnly=TRUE)
#if (length(args)<1) stop("At least one argument must be supplied: input csv file name with row intensity values. You may also enter whether to make a plot: (1) or not (0) --default is 0-, the output filename -default is same as inputfile plus \".xPeaks\",  the recording frequency (number of photograms by second) -default is 30-, the column number with the intensity values -default is 1- and the field separation char between quotation marks -default is white space-")


inputfile<-'./storage/app/results.txt'

#if (length(args)>1)
#makeplot<-as.numeric(args[2]) else
#makeplot<-0

#if (length(args)>2)
#outputfile<-args[3] else
#outputfile<-paste(inputfile, ".xPeaks", sep="")



#if (length(args)>3)
fs<-as.numeric(args[1]) #else
#fs<-30


#if (length(args)>4)
#column<-as.numeric(args[5]) else
column<-1

#if (length(args)>5)
#sep<-args[6] else
sep=" "


data<-readData(inputfile,column, fs, sep)
p<-detect_rpeaks(data, fs)

bpm<-calcBPM(p)
ibi<-calcIBI(p)
sdnn<-calcSDNN(p)
sdsd<-calcSDSD(p)
rmssd<-calcRMSSD(p)
pnn20<-calcPNN(p, 20)
pnn50<-calcPNN(p, 50)

json<-sprintf(
  '{\"bpm\": %f, \"ibi\": %f, \"sdnn\": %f, \"sdsd\": %f, \"rmssd\": %f, \"pnn20\": %f, \"pnn50\": %f}',
  bpm, ibi, sdnn, sdsd, rmssd, pnn20, pnn50
)

write(json, file=inputfile)

return(json)

# write.table(p, outputfile,  row.names = F, col.names = F)
#
# if (makeplot==1)
# {
#
#
# fileName=paste(inputfile, ".png", sep="")
# par(mar=c(1,1,1,1))
# png(fileName, width = 1200, height = 250, res=100)
#
#
# l<-length(data)
# xPos<-seq(1,l, by=fs)
# xLabs=paste(seq(1,l/fs), " s",sep="")
# if (length(xPos)>length(xLabs)) xPos<-xPos[-length(xPos)]
#
#
# plot(data, type="l", cex = 0.5, xaxt="n", xlab=inputfile)
# axis(1, at=xPos, labels=xLabs)
# abline(v=p*fs,col='red',lty=2)
# dev.off()
# }
