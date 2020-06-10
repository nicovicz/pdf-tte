<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>'Hello, world!' example</title>
  </head>
  <body>
    <div class="container">
      <div style="width: 50%; float: right;">
        <iframe style="margin-left:2%;width:100%;height:100vh" id="preview"></iframe>
      </div>
      <div style="width: 50%; float: left;">
        <span id="ket"></span><button id="kirim">Kirim</button>
        <div id="paging">
          <input type="text" id="hal" size="2" value="1" /> /
          <span id="total"></span>
        </div>
        <div id="annotation-layer" style="width: 102px; height: 46px;">
          <img src="qr.png" style="width: 25px; height: 25px;" />
          <p style="font-size: 7px; margin: 0px; margin-top: -3px;">
            Ditandatangani secara elektronik
          </p>
          <p style="font-size: 8px; margin: 0px; margin-top: 0px;">
            NAMA CONTOH SAJA
          </p>
        </div>
        <canvas
          id="the-canvas"
          style="border: 1px solid black; direction: ltr; width: 100%;"
        ></canvas>
      </div>
    </div>

    <script src="../build/pdf.js"></script>
    <script src="../build/pdf.worker.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
    <script
      src="https://code.jquery.com/jquery-3.5.1.min.js"
      integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
      crossorigin="anonymous"
    ></script>
    <script id="script">
      interact("#the-canvas").dropzone({
        accept: "#annotation-layer",
      });

      interact("#annotation-layer").draggable({
        inertia: true,
        modifiers: [
          interact.modifiers.restrictRect({
            restriction: "parent",
            endOnly: true,
          }),
        ],
        autoScroll: true,

        listeners: { move: dragMoveListener },
      });
      var canvas = document.getElementById("the-canvas");
      var context = canvas.getContext("2d");
      var pdfDoc;
      function dragMoveListener(event) {
        var target = event.target;
        // keep the dragged position in the data-x/data-y attributes
        var x = (parseFloat(target.getAttribute("data-x")) || 0) + event.dx;
        var y = (parseFloat(target.getAttribute("data-y")) || 0) + event.dy;

        // translate the element
        target.style.webkitTransform = target.style.transform =
          "translate(" + x + "px, " + y + "px)";

        // update the posiion attributes
        target.setAttribute("data-x", x);
        target.setAttribute("data-y", y);
        console.log('Real :', Math.round((parseFloat(y)) * 0.2645833333));
        x = Math.round((parseFloat(x)) * 0.2645833333);
        y = Math.round((parseFloat(y)) * 0.2645833333);
        
        document.querySelector("#ket").innerHTML = 'x='+x+'&y='+y;
      }
      //
      // If absolute URL from the remote server is provided, configure the CORS
      // header on that server.
      //
      var url =
        "http://localhost/pdfjs/test/compressed.tracemonkey-pldi-09.pdf";

      //
      // Asynchronous download PDF
      //

      var loadingTask = pdfjsLib.getDocument(url);
      loadingTask.promise.then(function (pdf) {
        //
        // Fetch the first page
        //
        pdfDoc = pdf;
        document.querySelector("#total").innerHTML = pdf.numPages;
        var pageNum = parseInt(document.querySelector("#hal").value);

        showPage(pageNum);
      });

      function showPage(pageNum) {
        pdfDoc.getPage(pageNum).then(function (page) {
          var scale = 2;
          var viewport = page.getViewport({ scale: scale });

          document.querySelector("#hal").value = pageNum;
          //
          // Prepare canvas using PDF page dimensions
          //

          canvas.height = viewport.height;
          canvas.width = viewport.width;

          document.querySelector("#ket").innerHTML =
            viewport.height + " x " + viewport.width;
          //
          // Render PDF page into canvas context
          //
          var renderContext = {
            canvasContext: context,
            viewport: viewport,
          };
          page.render(renderContext);
        });
      }

      $(document).on("click", "#kirim", function () {
        var q = $("#ket").text();
        $('#preview').attr('src',"Helper.php?" + q);
       
      });
    </script>
  </body>
</html>
