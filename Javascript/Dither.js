var bayerThresholdMap4x4 = [
    [  15, 135,  45, 165 ],
    [ 195,  75, 225, 105 ],
    [  60, 180,  30, 150 ],
    [ 240, 120, 210,  90 ]
  ];

  var bayerThresholdMap2x2 = [
    [  15, 45, ],
    [ 60,  30, ]
  ];
  
  var lumR = [];
  var lumG = [];
  var lumB = [];
  for (var i=0; i<256; i++) {
    lumR[i] = i*0.299;
    lumG[i] = i*0.587;
    lumB[i] = i*0.114;
  }

function monochrome(imageData, threshold, type) {

    var imageDataLength = imageData.data.length;

    for (var i = 0; i <= imageDataLength; i += 4) {
        imageData.data[i] = Math.floor(lumR[imageData.data[i]] + lumG[imageData.data[i + 1]] + lumB[imageData.data[i + 2]]);
    }

    var w = imageData.width;
    var newPixel, err;

    for (var currentPixel = 0; currentPixel <= imageDataLength; currentPixel += 4) {

        if (type === "none") {
            imageData.data[currentPixel] = imageData.data[currentPixel] < threshold ? 0 : 255;
        } 

        else if (type === "randomBayer") {
            var x = currentPixel / 4 % w;
            var y = Math.floor(currentPixel / 4 / w);
            var map = Math.floor((imageData.data[currentPixel] + bayerThresholdMap4x4[x % 4][y % 4]) / 2);
            imageData.data[currentPixel] = (map < Math.floor(Math.random() * 255)) ? 0 : 255;
        } 

        else if (type === "randomNone"){
            imageData.data[currentPixel] = imageData.data[currentPixel] < Math.floor(Math.random() * 255) ? 0 : 255;
        }

        else if (type === "randomAtkinson"){
            newPixel = imageData.data[currentPixel] < Math.floor(Math.random() * 255) ? 0 : 255;
            err = Math.floor((imageData.data[currentPixel] - newPixel) / 8);
            imageData.data[currentPixel] = newPixel;

            imageData.data[currentPixel + 4] += err;
            imageData.data[currentPixel + 8] += err;
            imageData.data[currentPixel + 4 * w - 4] += err;
            imageData.data[currentPixel + 4 * w] += err;
            imageData.data[currentPixel + 4 * w + 4] += err;
            imageData.data[currentPixel + 8 * w] += err;
        }

        else if(type === "atkinson") {
            
            newPixel = imageData.data[currentPixel] < threshold ? 0 : 255;
            err = Math.floor((imageData.data[currentPixel] - newPixel) / 8);
            imageData.data[currentPixel] = newPixel;

            imageData.data[currentPixel + 4] += err;
            imageData.data[currentPixel + 8] += err;
            imageData.data[currentPixel + 4 * w - 4] += err;
            imageData.data[currentPixel + 4 * w] += err;
            imageData.data[currentPixel + 4 * w + 4] += err;
            imageData.data[currentPixel + 8 * w] += err;
        }

        else if (type === "floydsteinberg") {
            newPixel = imageData.data[currentPixel] < 129 ? 0 : 255;
            err = Math.floor((imageData.data[currentPixel] - newPixel) / 16);
            imageData.data[currentPixel] = newPixel;

            imageData.data[currentPixel + 4] += err * 7;
            imageData.data[currentPixel + 4 * w - 4] += err * 3;
            imageData.data[currentPixel + 4 * w] += err * 5;
            imageData.data[currentPixel + 4 * w + 4] += err * 1;
        } 

        else if (type === "bayer4x4") {
            var x = currentPixel / 4 % w;
            var y = Math.floor(currentPixel / 4 / w);
            var map = Math.floor((imageData.data[currentPixel] + bayerThresholdMap4x4[x % 4][y % 4]) / 2);
            imageData.data[currentPixel] = (map < threshold) ? 0 : 255;
        }
        else if (type === "bayer2x2") {
            var x = currentPixel / 4 % w;
            var y = Math.floor(currentPixel / 4 / w);
            var map = Math.floor((imageData.data[currentPixel] + bayerThresholdMap2x2[x % 2][y % 2]));
            imageData.data[currentPixel] = (map < threshold) ? 0 : 255;
        }

        imageData.data[currentPixel + 1] = imageData.data[currentPixel + 2] = imageData.data[currentPixel];
    }

    return imageData;
}

var canvas = document.getElementById("display");
var ctx = canvas.getContext('2d');
var img = new Image();
var size = 300

document.querySelector('input[type="file"]').addEventListener('change', function() {
    ctx.clearRect(0, 0, size, size);
    if (this.files && this.files[0]) {
        img.width = size
        img.height = size
        img.src = URL.createObjectURL(this.files[0]);;
        img.onload = function () {
            ditherCall(this)
        };
    }
});


function ditherCall(){
    var threshold = document.getElementById("thresholdSlider").value;
    var contrast= document.getElementById("contrastSlider").value;
    var ditherStyle = document.getElementById("dithering").value;

    document.getElementById("thresholdValue").innerHTML = threshold
    document.getElementById("contrastValue").innerHTML = contrast + "%"

    ctx.filter = "contrast("+contrast+"%)"

    ctx.drawImage(img, 0, 0, size, size);
    var imageData = ctx.getImageData(0, 0, size, size);

    monochrome(imageData, threshold, ditherStyle)

    ctx.putImageData(imageData, 0, 0);
};