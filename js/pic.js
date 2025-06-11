
var app = new Vue({
  el: "#app",
    
      data() {
        return {
          output: null
        }
      },
  
      methods: {
        print() {
/*
          html2canvas(document.querySelector(".specific"), {
            "logging": true, //Enable log (use Web Console for get Errors and Warnings)
            "proxy": "html2canvasproxy",
            "useCORS": false,
            "onrendered": function (canvas) {
                var img = new Image();
                img.onload = function() {
                    img.onload = null;
                    document.body.appendChild(img);
                };
                img.onerror = function() {
                    img.onerror = null;
                    if(window.console.log) {
                        window.console.log("Not loaded image from canvas.toDataURL");
                    } else {
                        alert("Not loaded image from canvas.toDataURL");
                    }
                };
                img.src = canvas.toDataURL("image/png");
            }
        });
*/
         //

          /*
          var canvasPromise  = html2canvas(document.querySelector(".specific"), {
            logging: true, //Enable log (use Web Console for get Errors and Warnings)
            proxy: "html2canvasproxy",
            useCORS: false,
        });
        canvasPromise.then(function(canvas) {
        document.body.appendChild(canvas);
        console.log(canvas);
        canvas.toDataURL('image/png');
        });

        */
/*
        html2canvas(document.querySelector('.specific'), {
          useCORS: true,
          allowTaint : true,
          onrendered: function(canvas) {
            //document.body.appendChild(canvas);
            //return Canvas2Image.saveAsPNG(canvas);

            const context = canvas.getContext('2d');
            context.mozImageSmoothingEnabled = false;
            context.webkitImageSmoothingEnabled = false;
            context.msImageSmoothingEnabled = false;
            context.imageSmoothingEnabled = false;
            const src64 = canvas.toDataURL();
            const newImg = document.createElement('img');
            newImg.crossOrigin = 'Anonymous';
            newImg.src = src64;
            document.body.appendChild(newImg);
          },
          logging:true
        });
*/
        
          html2canvas(document.querySelector(".specific"), { proxy: "html2canvasproxy", useCORS: false, logging: true, allowTaint: true}).then(canvas => {
            //document.body.appendChild(canvas)
            return Canvas2Image.saveAsPNG(canvas);

          //const el = this.$refs.printMe;
          // add option type to get the image version
          // if not provided the promise will return 
          // the canvas.
          // const options = {
          //   type: 'dataURL'
          // };
          // (async () => {
          //     html2canvas(document.querySelector('.specific'), {
          //       onrendered: function(canvas) {
          //         // document.body.appendChild(canvas);
          //         return Canvas2Image.saveAsPNG(canvas);
          //       }
          //     });
          // })()
        
      });

      
    }
  }
});