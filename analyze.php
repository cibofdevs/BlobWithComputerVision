<?php
    if (isset($_POST["submit"])) {
        if (isset($_POST["url"])) {
            $imageUrl = $_POST["url"];
        } else {
            header("Location: index.php");
        }
    } else {
        header("Location: index.php");
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Results of Image Analysis</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">        
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    </head>
    <body>
     
    <div class="container" style="margin-top: 20px;">
        <div>
            <div class="float-left col-8">
                <b>Response :</b>
                <br><br>
                <textarea id="responseTextArea" class="form-control" rows="20" disabled></textarea>
            </div>
            <div class="float-right col-4">
                <b>Image :</b>
                <br><br>
                <div class="card" style="width: 18rem;">
                    <img class="card-img-top" id="sourceImage">
                    <div class="card-body">
                        <h5 class="card-title">Description</h5>
                        <p class="card-text" id="description">...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function () {
            var subscriptionKey = "cf78d30d03804d2ba692efcf2b6c6271";
     
            var uriBase =
                "https://southeastasia.api.cognitive.microsoft.com/vision/v2.0/analyze";
     
            var params = {
                "visualFeatures": "Categories,Description,Color",
                "details": "",
                "language": "en",
            };
     
            var sourceImageUrl = "<?php echo $imageUrl; ?>";
            document.querySelector("#sourceImage").src = sourceImageUrl;
     
            $.ajax({
                url: uriBase + "?" + $.param(params),
     
                // Request headers.
                beforeSend: function(xhrObj){
                    xhrObj.setRequestHeader("Content-Type","application/json");
                    xhrObj.setRequestHeader(
                        "Ocp-Apim-Subscription-Key", subscriptionKey);
                },
     
                type: "POST",
     
                // Request body.
                data: '{"url": ' + '"' + sourceImageUrl + '"}',
            })
     
            .done(function(data) {
                // Show formatted JSON on webpage.
                $("#responseTextArea").val(JSON.stringify(data, null, 2));
                $("#description").text(data.description.captions[0].text);
            })
     
            .fail(function(jqXHR, textStatus, errorThrown) {
                // Display error message.
                var errorString = (errorThrown === "") ? "Error. " :
                    errorThrown + " (" + jqXHR.status + "): ";
                errorString += (jqXHR.responseText === "") ? "" :
                    jQuery.parseJSON(jqXHR.responseText).message;
                alert(errorString);
            });
        });
    </script>
    </body>
</html>