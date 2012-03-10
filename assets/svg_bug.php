<svg width="1920" height="1080" viewBox="0 0 1920 1080" version="1.1">
<?
/*<path d="M 0 0 L 1920 0 L 1920 1080 L 0 1080 Z" stroke="#000000" stroke-width="5" fill="rgba(0,0,0,0)"/>
<path d="M 96 54 L 1824 54 L 1824 1026 L 96 1026 Z" stroke="#000000" stroke-width="5" fill="rgba(0,0,0,0)"/>
<path d="M 240 0 L 240 1080" stroke="#000000" stroke-width="5"/>
<path d="M 1680 0 L 1680 1080" stroke="#000000" stroke-width="5"/>*/?>


<? $png = file_get_contents("http://localhost/hockey/small_bug.png");?>
<image x="136" y="876"  width="200" height="150" xlink:href="data:image/png;base64,<?= base64_encode($png) ?>" />

</svg>