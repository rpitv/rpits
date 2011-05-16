<link rel="stylesheet" type="text/css" href="ui.css" media="screen" />
<script src="js/jquery-1.5.1.min.js" type="text/javascript"></script>
<script src="js/jquery-ui-1.8.12.custom.min.js" type="text/javascript"></script>
<script src="js/jquery.scrollintoview.js" type="text/javascript"></script>
<script>
  $.extend($.expr[":"], {
    "containsC": function(elem, i, match, array) {
      return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
    }});
  $(document).keydown(function(event)
  {
    if(event.keyCode != 13 && $("#input input").is(":focus"))
    {
      return;
    }
    //alert(event.keyCode);
    if (event.keyCode == '32')
    {
      event.preventDefault();
      if($("#program").hasClass("on"))
      {
        $(".on-program").removeClass("on-program");
        $("#program .label").text("Program");
        $("#program .image").html("");
        $("#program").removeClass("on");

      }
      else
      {
        $(".selected").addClass("on-program");
        $("#program").addClass("on");
        $("#program .label").text("Program - " + $(".selected").text());
        $("#program .image").html("<img src=\"" + $(".selected").children().first().attr("src") + "\" />");
      }
    }
    else if(event.keyCode == '13')
    {
      event.preventDefault();
      if($("#input").is(":visible"))
      {
        $("#input").hide();
        var data = $("#input input").val();
        $("li").removeClass("selected");
        var target = $("li:containsC("+data+"):first");
        target.addClass("selected");
        target.scrollintoview({duration: 0});
        $("#input input").blur();
      }
      else
      {
        $("#input").show();
        $("#input input").focus();
        $("#input input").val("");
      }
    }
    else if(event.keyCode == '82')
    {
      $("li").removeClass("on-edit");
      $("li").removeClass("on-preview");
      $(".selected").addClass("on-preview");
      $("#preview .label").text("Preview - " + $(".selected").text());
      $("#preview .image").html("<img src=\"" + $(".selected").children().first().attr("src") + "\" />");
    }
    else if(event.keyCode == '69')
    {
      $("li").removeClass("on-preview");
      $("li").removeClass("on-edit");
      $(".selected").addClass("on-edit");
      $("#preview .label").text("Editing " + $(".selected").text());
      $("#preview .image").html("<img src=\"" + $(".selected").children().first().attr("src") + "\" />");
    }
    else if(event.keyCode == '40')
    {
      event.preventDefault();
      if($(".selected + li").length > 0)
      {
        $(".selected + li").addClass("selected");
        $(".selected:first").removeClass("selected");
        $(".selected").scrollintoview({duration: 0});
      }
    }
    else if(event.keyCode == '38')
    {
      event.preventDefault();
      if($(".selected").prev("li").length > 0)
      {
        $(".selected").prev("li").addClass("selected");
        $(".selected:last").removeClass("selected");
        $(".selected").scrollintoview({duration: 0});
        //$("#pane").scrollTop($("#pane").scrollTop()-40);
      }
    }
    else if (event.keyCode == '9')
    {
      event.preventDefault();
      //$(".tab").addClass("active");
      if($(".active + .tab").length == 0)
      {
        $(".tab").removeClass("active");
        $(".tab:first").addClass("active");
        $("#titles").load($(".active:last").attr("request"),function(){
          $("li:first").addClass("selected");
        });
        $("li:first").addClass("selected");
      }
      else
      {
        $(".active + .tab").addClass("active");
        $("#titles").load($(".active:last").attr("request"),function(){
          $("li:first").addClass("selected");
        });
        $(".active:first").removeClass("active");
        $("li:first").addClass("selected");
      }
    }
  });
  $(document).ready(function(){
    $("#titles").load('title_list.php',function(){
      $("li:first").addClass("selected");
    });
    $(".tab").click(function()
    {
      if($(this).hasClass("active"))
        return;
      else
      {
        $(".tab").removeClass("active");
        $(this).addClass("active");
        $("#titles").load($(this).attr("request"));
        $("li:first").addClass("selected");
      }
    });
    $(function() {
      $( "#titles" ).sortable({
        placeholder: "ui-state-highlight",
        helper : 'clone',
        distance:40
      });
      $( "#titles" ).disableSelection();
    
    });
    $("li").live("mouseover",function(){
      $(".selected").removeClass("selected");
      $(this).addClass("selected");
    });
    $("li").live("dblclick",function()
    {
      if($(this).hasClass("on-program"))
      {
        $("li").removeClass("on-program");
        $("#program .label").text("Program");
        $("#program .image").html("");
        $("#program").removeClass("on");
      }
      else
      {
        $("li").removeClass("on-program");
        $(this).addClass("on-program");
        $("#program .label").text("Program - " + $(this).text());
        $("#program .image").html("<img src=\"" + $(this).children().first().attr("src") + "\" />");
        $("#program").addClass("on");
      }

    });
    $("li").live("click",function(){
      $("li").removeClass("on-preview");
      $(this).addClass("on-preview");
      $("#preview .label").text("Preview - " + $(this).text());
      $("#preview .image").html("<img src=\"" + $(this).children().first().attr("src") + "\" />");
    });
  });
</script>
<div id="program"><div class="label">Program</div><div class="image"></div></div>
<div id="preview"><div class="label">Preview</div><div class="image"></div></div>
<div id="pane">
  <ul id="titles">

  </ul>
</div>
<div id="tabstrip">
  <div class="tab active" request="title_list.php">All Titles</div>
  <div class="tab" request="title_list.php?event=1">Hockey Titles</div>
  <div class="tab" request="title_list.php?team=rpi">RPI Players</div>
  <div class="tab" request="title_list.php?team=colgate">Colgate Players</div>
</div>
<div id="input"><input type="text" /></div>
<div id="actions"></div>
<div id="options"></div>
