<?

$gravities = array("west"=>imagick::GRAVITY_WEST,"center"=>imagick::GRAVITY_CENTER,"east"=>imagick::GRAVITY_EAST);
$fonts = array("fontN"=>"fonts/GothamNarrow-Bold.otf","font"=>"fonts/Gotham-Bold.ttf","fontX"=>"fonts/GothamXNarrow-Bold.otf");

function slantRectangle(&$canvas,$x,$y,$w,$h,$color1,$image=0)
{
  $background = "#FFFFFF";
  //$ltr = '#000000';
  $ltr='#888';
  $bd = '#A6A6A6';
  $iglow='#404040';

  try{
    
    $gradient1 = new Imagick();
    $gradient1->newPseudoImage($h, $w*3/8, "gradient:white-$ltr");
    $gradient1->rotateImage(new ImagickPixel(),270);
    $gradient2 = new Imagick();
    $gradient2->newPseudoImage($h, $w*3/8, "gradient:$ltr-white");
    $gradient2->rotateImage(new ImagickPixel(),270);

    $lefttoright = new Imagick();
    $lefttoright->newPseudoImage($w,$h,"xc:$ltr");
    $lefttoright->compositeImage($gradient1,imagick::COMPOSITE_OVER,0,0);
    $lefttoright->compositeImage($gradient2,imagick::COMPOSITE_OVER,$w*5/8,0);

    $whiteup = new Imagick();
    $whiteup->newPseudoImage($w,$h/2,"gradient:black-#888");

    $gradient1 = new Imagick();
    $gradient1-> newPseudoImage($w,$h/7,"gradient:$bd-white");
    $gradient2 = new Imagick();
    $gradient2-> newPseudoImage($w,$h/7,"gradient:white-$bd");

    $bottomdark = new Imagick();
    $bottomdark->newPseudoImage($w,$h,"xc:white");
    $bottomdark->compositeImage($gradient1,imagick::COMPOSITE_OVER,0,0);
    $bottomdark->compositeImage($gradient2,imagick::COMPOSITE_OVER,0,($h/2)-($h/7));

    $background = new Imagick();
    $background->newPseudoImage($w,$h,"xc:$color1");
    
    

    
    $background->compositeImage($lefttoright,imagick::COMPOSITE_MULTIPLY,0,0);
    if($image)
    {
      $logo = new Imagick();
      $logo->readImage(realpath($image));
      $logo->resizeImage($h,$h,imagick::FILTER_TRIANGLE,1);
      $background->compositeImage($logo,imagick::COMPOSITE_OVER,$w-$h-($h/3),0);
    }
    $background->compositeImage($whiteup,imagick::COMPOSITE_SCREEN,0,0);
    $background->compositeImage($bottomdark,imagick::COMPOSITE_MULTIPLY,0,$h/2);

    $slantleft = new Imagick();
    $slantleft->newPseudoImage($h*sqrt(5)/2,8,"gradient:$iglow-white");
    $slantleft->rotateImage("none",296.6);
    $slantright = new Imagick();
    $slantright->newPseudoImage($h*sqrt(5)/2,8,"gradient:$iglow-white");
    $slantright->rotateImage("none",117.2);

    $top = new Imagick();
    $top->newPseudoImage($w,8,"gradient:$iglow-white");
    $bottom = new Imagick();
    $bottom->newPseudoImage($w,8,"gradient:white-$iglow");

    $slants = new Imagick();
    $slants->newPseudoImage($w,$h,"xc:white");
    $slants->compositeImage($slantleft,imagick::COMPOSITE_OVER,-1,0);
    $slants->compositeImage($slantright,imagick::COMPOSITE_OVER,$w-($h/2)-9,0);
    $slants->compositeImage($top,imagick::COMPOSITE_MULTIPLY,0,0);
    $slants->compositeImage($bottom,imagick::COMPOSITE_MULTIPLY,0,$h-8);

    $background->compositeImage($slants,imagick::COMPOSITE_MULTIPLY,0,00);
    
    $draw1 = new ImagickDraw();
    $draw1->pushPattern('gradient',0,0,$w,$h);
    $draw1->composite(Imagick::COMPOSITE_OVER,0,0,$w,$h,$background);
    $draw1->popPattern();
    $draw1->setFillPatternURL('#gradient');
    $draw1->polygon(array(array('x'=>00,'y'=>$h-1), array('x'=>($h/2)-1, 'y'=>00), array('x'=>$w-1,'y'=> 00),array('x'=>$w-($h/2)-1,'y'=>$h-1)));      

    $points = array(array('x'=>0,'y'=>$h-1), array('x'=>($h/2)-1, 'y'=>00), array('x'=>$w-1,'y'=> 00),array('x'=>$w-($h/2)-1,'y'=>$h-1));

    for($i=0;$i<4;$i++)
    {
      $points[$i]['x']+=10;
      $points[$i]['y']+=10;
    }
    
    $shadow = new Imagick();
    $shadow->newPseudoImage($w+20,$h+20,"xc:none");
    $draws = new ImagickDraw();
    $draws->setFillColor("black");
    $draws->polygon($points);
    $shadow->drawImage($draws);
    $shadow->blurImage(0,4,imagick::CHANNEL_ALPHA);
    
    $im = new Imagick();
    $im->newPseudoImage( $w, $h, "xc:none");

    $im->drawImage($draw1);

    $im2 = new Imagick();
    $im2->newPseudoImage($w+50,$h+50,"xc:none");
    $im2->compositeImage($shadow,imagick::COMPOSITE_OVER,5,5);
    $draw1 = new ImagickDraw();
    $draw1->setStrokeWidth(6);
    $draw1->setStrokeColor("black");

    $draw1->polygon($points);  
    $draw1->setStrokeWidth(2);
    $draw1->setStrokeColor("white");
    $draw1->polygon($points);  
    $im2->drawImage($draw1);
    
    $im2->compositeImage($im,imagick::COMPOSITE_OVER,10,10);
    
    

    $canvas->compositeImage($im2,imagick::COMPOSITE_OVER,$x-10,$y-10);

  }catch(Exception $e){
    echo 'Error: ',  $e->getMessage(), "";
  }
}

function blackBox(&$canvas,$x,$y,$w,$h)
{
  $rectangle = new Imagick();
  $rectangle->newPseudoImage($w,$h,"xc:none");
  $draw1 = new ImagickDraw();
  $draw1->pushPattern('gradient',0,0,5,5);
  $tile = new Imagick();
  $tile->readImage(realpath("assets/diag_tile.png"));
  $draw1->composite(Imagick::COMPOSITE_OVER,0,0,5,5,$tile);
  $draw1->popPattern();
  $draw1->setFillPatternURL('#gradient');
  $draw1->rectangle(0,0,$w,$h);
  $rectangle->drawImage($draw1);
  
  $gradient = new Imagick();
  $gradient->newPseudoImage($w,$h,"gradient:#DDD-#666");
  
  $rectangle->compositeImage($gradient,Imagick::COMPOSITE_COPYOPACITY,0,0);
  
  $black = new Imagick();
  $black->newPseudoImage($w,$h,"xc:black");
  
  $layered = new Imagick();
  $layered->newPseudoImage($w+20,$h+20,"xc:none");
  $layered->compositeImage($black,Imagick::COMPOSITE_OVER,5,0);
  $layered->compositeImage($black,Imagick::COMPOSITE_OVER,5,5);
  $layered->compositeImage($gradient,Imagick::COMPOSITE_COPYOPACITY,5,5);
  $layered->blurImage(4,5,imagick::CHANNEL_ALPHA);
  $layered->compositeImage($black,Imagick::COMPOSITE_DSTOUT,0,0);
  
  $canvas->compositeImage($layered,Imagick::COMPOSITE_OVER,$x,$y);
  $canvas->compositeImage($rectangle,Imagick::COMPOSITE_OVER,$x,$y);
  
}

function defaultText($w,$h,$string,$gravity,$font)
{
  global $gravities,$fonts;
  $text = new Imagick();
  $text->setFont($fonts[$font]);
  $text->setBackgroundColor("none");
  $text->setGravity($gravities[$gravity]);
  
  $text->newPseudoImage($w,$h,"caption:" . $string);
  //$metrics = $text->queryFontMetrics( $annotate, $text );
  
  return $text;
}

function plainText(&$canvas,$x,$y,$w,$h,$string,$gravity,$font,$color)
{
  $text = defaultText($w,$h,$string,$gravity,$font); 
  $shadow = $text->clone();
  $shadow->blurImage(4,2,imagick::CHANNEL_ALPHA);
  $text->colorizeImage($color,1);
  
  $canvas->compositeImage($shadow,imagick::COMPOSITE_OVER,$x,$y);
  $canvas->compositeImage($text,imagick::COMPOSITE_OVER,$x,$y);
}

function getTextWidth($w,$h,$string,$font)
{
  $text = defaultText($w,$h,$string,"center",$font);
  $text->trimImage(0);
  $geo = $text->getImageGeometry();
  return $geo["width"];
}

function shadowedText(&$canvas,$x,$y,$w,$h,$string,$gravity,$font,$color)
{
  $text = defaultText($w,$h,$string,$gravity,$font);      
  $shadow = $text->clone();
  $stroke = $text->clone();
  $shadow->blurImage(4,5,imagick::CHANNEL_ALPHA);
  $text->colorizeImage($color,1);

  $canvas->compositeImage($shadow,imagick::COMPOSITE_OVER,$x+5,$y+5);
  $canvas->compositeImage($shadow,imagick::COMPOSITE_OVER,$x,$y);
  $canvas->compositeImage($text,imagick::COMPOSITE_OVER,$x,$y);
}

function placeImage(&$canvas,$x,$y,$w,$h,$path)
{
  try{
    $logo = new Imagick();
    $logo->readImage(realpath($path));
    
    // This might cause problems with non-Player Portraits if aspect ratios are off.
    $size = @getimagesize($path);
    $logo->cropImage($size[0], $size[0]*1.2, 0, 0);
    
    $logo->resizeImage($w,$h,imagick::FILTER_TRIANGLE,1);
    $canvas->compositeImage($logo,imagick::COMPOSITE_OVER,$x,$y);
  }
  catch(Exception $e){
    echo 'Error: ',  $e->getMessage(), "";
  }
}
?>