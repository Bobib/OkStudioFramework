$(document).ready(function() {

    $('.addLink').click(function(){

        var valueName = $(this).attr('id');
        var selectName = "Select" + valueName;
        var value = $("select[ name$="  + selectName + " ]").val();
        var hiddenValue = $("select[ name$="  + valueName + " ]");

        var objInfosBdd = document.getElementById("queryInfos" + valueName);
        var objOutput = document.getElementById("result" + valueName);

             var existing = false;
             var buffer = document.getElementsByName(valueName)[0].value;
            buffer +=";";
            var re = new RegExp(value + "\;");
            if (buffer.match(re)) {
                    existing = true;
            }

            if (!existing) {
                var separateur;
                if (document.getElementsByName(valueName)[0].value == "") {
                    separateur = "";
                } else {
                    separateur = ";";
                }
                document.getElementsByName(valueName)[0].value = document.getElementsByName(valueName)[0].value + separateur + value;
            }

            //update item list
            $.post("adminajax.html",
                    {bddinfos:objInfosBdd.className, values : document.getElementsByName(valueName)[0].value, element : valueName},
                    function(data){
                        objOutput.innerHTML = data;
                    }
             );

    });

    $(".removeLink").live('click', function(){
            
            var chaine =  $(this).attr("id");
            var  test = chaine.split(/(.+)\|(\d+)$/g);
            var objInfosBdd = document.getElementById("queryInfos" + test[1]);
            var objOutput = document.getElementById("result" +test[1]);
            var identifiers = document.getElementsByName(test[1])[0].value;

            var re = new RegExp((test[2]), "g");
            identifiers = identifiers.replace(re, "");
            identifiers = identifiers.replace(/(;;)/g, ";");
            document.getElementsByName(test[1])[0].value = identifiers;
            $.post("adminajax.html",
                    {bddinfos:objInfosBdd.className, values : identifiers, element :  test[1]},
                    function(data){
                        objOutput.innerHTML = data;
                    }
             );
    });


    //sorting functions
    $(".sortUP").live("click", function(){
       updatePosition( $(this).attr("rel"), "up" );
    });

    $(".sortDOWN").live("click", function(){
        updatePosition( $(this).attr("rel"), "down");
    });

});

function updatePosition(id,  sens ) {

        var listID = new Array();
        var listPos = new Array();
        var pos = 0;

        var actualPos;
        var targetPos;
        var actualid = id;
        var targetId;

        $(".sortIdentifier").each(function(){

            listID[pos] = $(this).html() ;
            listPos[$(this).html()] = pos;

            pos++;
        });

        if (sens == "up") {

            actualPos = listPos[id];
            targetPos = actualPos - 1;
            targetId = listID[targetPos];

        } else if ( sens == "down") {
            
            actualPos = listPos[id];
            targetPos = actualPos + 1;
            targetId = listID[targetPos];
        }

            $.post("adminajax.html",
                    {actualId:actualid, actualPos: actualPos, targetId: targetId, targetPos: targetPos , table: $("#tableInformations").html(), idField: $("#idFieldInformations").html()},
                    function(data){
                        location.reload(true);
                    }
             );
}

bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });