<?

$shrink = $_GET["shrink"];

?>
<style type="text/css">
  body {
    margin:0px;
    padding:0px;
}
iframe
{
  border:none;
}

</style>
<title>General Title Editor</title>
<? if($shrink == 0)
{
?>
<div style="display:block;float:left;width:20%;height:100%"><iframe name="list_editor" style="width:100%;height:100%" src="eventtitlelist.php"></iframe></div>
<div style="display:block;float:left;width:40%;height:100%"><iframe name="list" style="width:100%;height:100%" src="titlelist.php"></iframe></div>
<div style="display:block;float:left;width:40%;height:100%"><iframe name="edit" style="width:100%;height:100%" src="titleedit.php"></iframe></div>
<? } else { ?>
<div style="width:225px;height:100%"><iframe name="list" style="width:100%;height:100%" src="titlelist.php"></iframe></div>
<div style="position:absolute;left:225px;top:0px;width:700px;height:100%"><iframe name="edit" style="width:100%;height:100%" src="titleedit.php"></iframe></div>
<? } ?>
<!--
<frameset cols="20%,40%,40%">
  <frame name="list_editor" src="eventtitlelist.php"></frame>
  <frame name="list" src="titlelist.php"></frame>
  <frame name="edit" src="titleedit.php"></frame>
</frameset>
-->



