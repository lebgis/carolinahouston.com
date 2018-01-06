<?php
$theme['msg'] = htmlspecialchars_decode($theme['msg']);
 ?>
 <style type="text/css">

#<?php echo $wpmcw_id; ?> {
padding: 0 5px;
background: #27313B;
text-align: center;
width: 100%;
<?php  if(isset($theme["bg_c"])){
    echo 'background-color:'.$theme["bg_c"].';';
}?>
}

#<?php echo $wpmcw_id;?> div{
  position:relative;
}

#<?php echo $wpmcw_id;?> h3{
color: #F4233C;
line-height: 20px;
padding-top:18px;
font-size: 20px;
margin: 0;
<?php 
if(isset($theme["heading_f"]['f'])){
    array_push($wpmc_font, $theme["heading_f"]['f']);
echo 'font-family:'.$theme["heading_f"]['f'].';';
}
if(isset($theme["heading_f"]['s'])){
echo 'font-size:'.$theme["heading_f"]['s'].'px;';
}
if(isset($theme["heading_f"]['w'])){
echo 'font-weight:'.$theme["heading_f"]['w'].';';
}
if(isset($theme["heading_f"]['st'])){
echo 'font-style:'.$theme["heading_f"]['st'].';';
}
if(isset($theme["heading_fc"])){
echo 'color:'.$theme["heading_fc"].';';
}
?>
}
#<?php echo $wpmcw_id;?> .wpmchimpa_para{
  margin: 12px auto;
  line-height: 14px;
}
#<?php echo $wpmcw_id;?> .wpmchimpa_para,#<?php echo $wpmcw_id;?> .wpmchimpa_para * {
font-size: 12px;
color: #959595;
<?php if(isset($theme["msg_f"]['f'])){
    array_push($wpmc_font, $theme["msg_f"]['f']);
  echo 'font-family:'.$theme["msg_f"]['f'].';';
}if(isset($theme["msg_f"]['s'])){
    echo 'font-size:'.$theme["msg_f"]['s'].'px;';
}?>
}
#<?php echo $wpmcw_id;?> form{
margin: 20px auto;
}
#<?php echo $wpmcw_id;?> .formbox > div:first-of-type{
  width: 65%;
  float: left;
}
#<?php echo $wpmcw_id;?> .formbox > div:first-of-type + div{
  width: 35%;
  float: left;
}
#<?php echo $wpmcw_id;?> .formbox input[type="text"]{
border-radius: 3px 0 0 3px;
}
#<?php echo $wpmcw_id;?>  .wpmchimpa-field{
position: relative;
width:100%;
margin: 0 auto 10px auto;
text-align: left;
<?php 
  if(isset($theme["tbox_w"])){
      echo 'width:'.$theme["tbox_w"].'px;';
  }
?>
}
#<?php echo $wpmcw_id;?> .inputicon{
display: none;
}
#<?php echo $wpmcw_id;?> .wpmc-ficon .inputicon {
display: block;
width: 35px;
height: 35px;
position: absolute;
top: 0;
left: 0;
pointer-events: none;
<?php 
if(isset($theme["tbox_h"])){
  echo 'width:'.$theme["tbox_h"].'px;';
  echo 'height:'.$theme["tbox_h"].'px;';
}
?>
}
#<?php echo $wpmcw_id;?> .wpmc-ficon input[type="text"],
#<?php echo $wpmcw_id;?> .wpmc-ficon input[type="text"] ~ .inputlabel{
  padding-left: 35px;
  <?php 
if(isset($theme["tbox_h"])){
  echo 'padding-left:'.$theme["tbox_h"].'px;';
  }?>
}
<?php
$col = ((isset($theme["inico_c"]))? $theme["inico_c"] : '#888');
foreach ($form['fields'] as $f) {
  $fi = false;
  if($f['icon'] == 'idef'){
    if($f['tag']=='email')
      $fi = 'a02';
    else if($f['tag']=='FNAME' || $f['tag']=='LNAME')
      $fi = 'c06';
  }
  else if( $f['icon'] != 'inone')
    $fi = $f['icon'];
  if($fi)
    echo '#'.$wpmcw_id.' .wpmc-ficon [wpmcfield="'.$f['tag'].'"] ~ .inputicon {background: '.$plugin->getIcon($fi,15,$col).' no-repeat center}';
}
?>
#<?php echo $wpmcw_id;?> .wpmchimpa-field textarea,
#<?php echo $wpmcw_id;?> .wpmchimpa-field select,
#<?php echo $wpmcw_id;?> input[type="text"]{
text-align: left;
width: 100%;
height: 35px;
background: #fff;
 padding: 0 10px;
border-radius: 3px;
color: #353535;
font-size:14px;
outline:0;
display: block;
border: 1px solid #efefef;
<?php 
    if(isset($theme["tbox_f"]['f'])){
    array_push($wpmc_font, $theme["tbox_f"]['f']);
      echo 'font-family:'.$theme["tbox_f"]['f'].';';
    }
    if(isset($theme["tbox_f"]['s'])){
        echo 'font-size:'.$theme["tbox_f"]['s'].'px;';
    }
    if(isset($theme["tbox_f"]['w'])){
        echo 'font-weight:'.$theme["tbox_f"]['w'].';';
    }
    if(isset($theme["tbox_f"]['st'])){
        echo 'font-style:'.$theme["tbox_f"]['st'].';';
    }
    if(isset($theme["tbox_fc"])){
        echo 'color:'.$theme["tbox_fc"].';';
    }
    if(isset($theme["tbox_bgc"])){
        echo 'background:'.$theme["tbox_bgc"].';';
    }
    if(isset($theme["tbox_w"])){
        echo 'width:'.$theme["tbox_w"].'px;';
    }
    if(isset($theme["tbox_h"])){
        echo 'height:'.$theme["tbox_h"].'px;';
    }
    if(isset($theme["tbox_bor"]) && isset($theme["tbox_borc"])){
        echo ' border:'.$theme["tbox_bor"].'px solid '.$theme["tbox_borc"].';';
    }
?>
}

#<?php echo $wpmcw_id;?> .wpmchimpa-field.wpmchimpa-multidrop select{
  height: 100px;
}

#<?php echo $wpmcw_id;?> .wpmchimpa-field.wpmchimpa-drop:before{
content: '';
width: 35px;
height: 35px;
position: absolute;
right: 0;
top: 0;
pointer-events: none;
background: no-repeat center;
background-image: <?=$plugin->getIcon('dd',16,'#000');?>;
<?php 
if(isset($theme["tbox_h"])){
  echo 'width:'.$theme["tbox_h"].'px;';
  echo 'height:'.$theme["tbox_h"].'px;';
}
?>
}
#<?php echo $wpmcw_id;?> input[type="text"] ~ .inputlabel{
position: absolute;
top: 0;
left: 0;
right: 0;
pointer-events: none;
width: 100%;
line-height: 35px;
color: rgba(0,0,0,0.6);
font-size: 14px;
font-weight:500;
padding: 0 10px;
white-space: nowrap;
<?php 
if(isset($theme["tbox_f"]['f'])){
    array_push($wpmc_font, $theme["tbox_f"]['f']);
  echo 'font-family:'.str_replace("|ng","",$theme["tbox_f"]['f']).';';
}
if(isset($theme["tbox_f"]['s'])){
    echo 'font-size:'.$theme["tbox_f"]['s'].'px;';
}
if(isset($theme["tbox_f"]['w'])){
    echo 'font-weight:'.$theme["tbox_f"]['w'].';';
}
if(isset($theme["tbox_f"]['st'])){
    echo 'font-style:'.$theme["tbox_f"]['st'].';';
}
if(isset($theme["tbox_fc"])){
    echo 'color:'.$theme["tbox_fc"].';';
}
?>
}
#<?php echo $wpmcw_id;?> input[type="text"]:valid + .inputlabel{
display: none;
}
#<?php echo $wpmcw_id;?> select.wpmcerror,
#<?php echo $wpmcw_id;?> input[type="text"].wpmcerror{
  border-color: red;
}

#<?php echo $wpmcw_id;?> .wpmchimpa-check *,
#<?php echo $wpmcw_id;?> .wpmchimpa-radio *{
color: #fff;
<?php
if(isset($theme["check_f"]["f"])){
  echo 'font-family:'.str_replace("|ng","",$theme["check_f"]["f"]).';';
}
if(isset($theme["check_f"]["s"])){
    echo 'font-size:'.$theme["check_fs"].'px;';
}
if(isset($theme["check_f"]["w"])){
    echo 'font-weight:'.$theme["check_fw"].';';
}
if(isset($theme["check_f"]["st"])){
    echo 'font-style:'.$theme["check_fst"].';';
}
if(isset($theme["check_fc"])){
    echo 'color:'.$theme["check_fc"].';';
}
?>
}
#<?php echo $wpmcw_id;?> .wpmchimpa-item input {
  display: none;
}
#<?php echo $wpmcw_id;?> .wpmchimpa-item span {
  cursor: pointer;
  display: inline-block;
  position: relative;
  padding-left: 35px;
  margin-right: 10px;
  line-height: 26px;
}

#<?php echo $wpmcw_id;?> .wpmchimpa-item span:before,
#<?php echo $wpmcw_id;?> .wpmchimpa-item span:after {
  content: '';
  display: inline-block;
  width: 16px;
  height: 16px;
  left: 0;
  top: 5px;
  position: absolute;
}
#<?php echo $wpmcw_id;?> .wpmchimpa-item span:before {
background-color: #fff;
transition: all 0.3s ease-in-out;
<?php
  if(isset($theme["check_borc"])){
      echo 'border: 1px solid'.$theme["check_borc"].';';
  }
?>
}
#<?php echo $wpmcw_id;?> .wpmchimpa-item input:checked + span:before{
  <?php if(isset($theme["check_c"]))echo 'background: '.$theme["check_c"].';';?>
}
#<?php echo $wpmcw_id;?> .wpmchimpa-item input[type='checkbox'] + span:hover:after, #<?php echo $wpmcw_id;?> input[type='checkbox']:checked + span:after {
  content:'';
  background: no-repeat center;
  <?php if(isset($theme['check_shade']))$chs=$theme['check_shade'];else $chs='1';
  echo 'background-image: '.$plugin->chshade($chs).';';?>
  left: -1px;
}
#<?php echo $wpmcw_id;?> .wpmchimpa-item input[type='radio'] + span:before {
border-radius: 50%;
width: 16px;
height: 16px;
top: 4px;
}
#<?php echo $wpmcw_id;?> input[type='radio']:checked + span:after {
background: <?php echo ($chs == 1)?'#7C7C7C':'#fafafa';?>;
width: 12px;
height: 12px;
top: 6px;
left: 2px;
border-radius: 50%;
}
#<?php echo $wpmcw_id;?> .wpmcinfierr{
  display: block;
  height: 10px;
  text-align: left;
  line-height: 10px;
  margin-bottom: -10px;
  font-size: 10px;
  color: red;
pointer-events: none;
  <?php
    if(isset($theme["status_f"]['f'])){
    array_push($wpmc_font, $theme["status_f"]['f']);
      echo 'font-family:'.str_replace("|ng","",$theme["status_f"]['f']).';';
    }
    if(isset($theme["status_f"]['w'])){
        echo 'font-weight:'.$theme["status_f"]['w'].';';
    }
    if(isset($theme["status_f"]['st'])){
        echo 'font-style:'.$theme["status_f"]['st'].';';
    }
  ?>
}

#<?php echo $wpmcw_id;?> .wpmchimpa-subs-button{
border-radius: 0 3px 3px 0;
width: 100%;
color: #fff;
font-size: 17px;
border: 1px solid #FA0B38;
background-color: #FF1F43;
height: 35px;
line-height: 30px;
text-align: center;
cursor: pointer;
position: relative;
top: 0;
transition: all 0.5s ease;
<?php
if(isset($theme["button_f"]['f'])){
    array_push($wpmc_font, $theme["button_f"]['f']);
echo 'font-family:'.$theme["button_f"]['f'].';';
}
if(isset($theme["button_f"]['s'])){
echo 'font-size:'.$theme["button_f"]['s'].'px;';
}
if(isset($theme["button_f"]['w'])){
echo 'font-weight:'.$theme["button_f"]['w'].';';
}
if(isset($theme["button_f"]['st'])){
echo 'font-style:'.$theme["button_f"]['st'].';';
}
if(isset($theme["button_fc"])){
echo 'color:'.$theme["button_fc"].';';
}
if(isset($theme["button_w"])){
echo 'width:'.$theme["button_w"].'px;';
}
if(isset($theme["button_h"])){
echo 'height:'.$theme["button_h"].'px;';
echo 'line-height:'.$theme["button_h"].'px;';
}
if(isset($theme["button_bc"])){
echo 'background-color:'.$theme["button_bc"].';';
}
if(isset($theme["button_br"])){
echo '-webkit-border-radius:'.$theme["button_br"].'px;';
echo '-moz-border-radius:'.$theme["button_br"].'px;';
echo 'border-radius:'.$theme["button_br"].'px;';
}
if(isset($theme["button_bor"]) && isset($theme["button_borc"])){
echo ' border:'.$theme["button_bor"].'px solid '.$theme["button_borc"].';';
}
?>
}
#<?php echo $wpmcw_id;?> .wpmchimpa-subs-button::before{
content: '<?php if(isset($theme['button'])) echo $theme['button'];else echo __( 'Subscribe', 'chimpmate-wpmca' );?>';
}
#<?php echo $wpmcw_id;?> .wpmchimpa-subs-button:hover{
background-color: #FA0B38;
<?php if(isset($theme["button_fch"])){
echo 'color:'.$theme["button_fch"].';';
}    
if(isset($theme["button_bch"])){
echo 'background-color:'.$theme["button_bch"].';';
}?>
}

#<?php echo $wpmcw_id;?> .wpmchimpa-subsc{
  text-align: center;
}
#<?php echo $wpmcw_id;?> .wpmchimpa-subs-button.subsicon:before{
padding-left: 35px;
  <?php 
  if(isset($theme["button_w"])){
      echo 'padding-left:'.$theme["button_h"].'px;';
  }
  ?>
}
#<?php echo $wpmcw_id;?> .wpmchimpa-subs-button.subsicon::after{
content:'';
position: absolute;
height: 35px;
width: 35px;
top: 0;
left: 0;
pointer-events: none;
  <?php 
  if(isset($theme["button_h"])){
      echo 'width:'.$theme["button_h"].'px;';
      echo 'height:'.$theme["button_h"].'px;';
  }
  if($theme["button_i"] != 'inone' && $theme["button_i"] != 'idef'){
    $col = ((isset($theme["button_fc"]))? $theme["button_fc"] : '#fff');
     echo 'background: '.$plugin->getIcon($theme["button_i"],15,$col).' no-repeat center;';
  }
  ?>
}
#<?php echo $wpmcw_id;?>.signalshow .wpmchimpa-subs-button::after,
#<?php echo $wpmcw_id;?>.signalshow .wpmchimpa-subs-button::before{
 display: none;
}

#<?php echo $wpmcw_id;?> .wpmchimpa-signal {
display: none;
  z-index: 1;
    top: 4px;
  left: calc(50% - 20px);
  position: absolute;
-webkit-transform: scale(0.5);
-ms-transform: scale(0.5);
transform: scale(0.5);
}
#<?php echo $wpmcw_id;?>.signalshow .wpmchimpa-signal {
  display: inline-block;
}
#<?php echo $wpmcw_id;?> .wpmchimpa-feedback{
text-align: center;
position: relative;
color: #ccc;
font-size: 10px;
height: 12px;
margin-top: -12px;
<?php
if(isset($theme["status_f"]['f'])){
    array_push($wpmc_font, $theme["status_f"]['f']);
  echo 'font-family:'.$theme["status_f"]['f'].';';
}
if(isset($theme["status_f"]['s'])){
    echo 'font-size:'.$theme["status_f"]['s'].'px;';
}
if(isset($theme["status_f"]['w'])){
    echo 'font-weight:'.$theme["status_f"]['w'].';';
}
if(isset($theme["status_f"]['st'])){
    echo 'font-style:'.$theme["status_f"]['st'].';';
}
if(isset($theme["status_fc"])){
    echo 'color:'.$theme["status_fc"].';';
}
?>
}
#<?php echo $wpmcw_id;?> .wpmchimpa-feedback.wpmchimpa-done{
font-size: 15px; margin: 10px;height: auto;
}
#<?php echo $wpmcw_id;?> .wpmchimpa-feedback.wpmchimpa-done:before{
content:<?=$plugin->getIcon('ch1',15,'#fff');?>;
width: 40px;
height: 40px;
border-radius: 20px;
line-height: 46px;
display: block;
background-color: #01E169;
margin: 40px auto;
}

#<?php echo $wpmcw_id;?> .wpmchimpa-tag{
margin: 5px auto;
}
#<?php echo $wpmcw_id;?> .wpmchimpa-tag,
#<?php echo $wpmcw_id;?> .wpmchimpa-tag *{
color:#fff;
font-size: 10px;
<?php
  if(isset($theme["tag_f"]['f'])){
    array_push($wpmc_font, $theme["tag_f"]['f']);
    echo 'font-family:'.$theme["tag_f"]['f'].';';
  }
  if(isset($theme["tag_f"]['s'])){
      echo 'font-size:'.$theme["tag_f"]['s'].'px;';
  }
  if(isset($theme["tag_f"]['w'])){
      echo 'font-weight:'.$theme["tag_f"]['w'].';';
  }
  if(isset($theme["tag_f"]['st'])){
      echo 'font-style:'.$theme["tag_f"]['st'].';';
  }
  if(isset($theme["tag_fc"])){
      echo 'color:'.$theme["tag_fc"].';';
  }
?>
}
#<?php echo $wpmcw_id;?> .wpmchimpa-tag:before{
content:<?php
  $tfs=10;
  if(isset($theme["tag_f"]['s'])){$tfs=$theme["tag_f"]['s'];}
  $tfc='#fff';
  if(isset($theme["tag_fc"])){$tfc=$theme["tag_fc"];}
  echo $plugin->getIcon('lock1',$tfs,$tfc);?>;
margin: 5px;
top: 1px;
position:relative;
}
</style>
<div class="widget-text wp_widget_plugin_box">
<div class="wpmchimpa-reset wpmcintselector wpmchimpa chimpmatecss" id="<?php echo $wpmcw_id;?>">
          <?php if(isset($theme['heading'])) echo '<h3>'.$theme['heading'].'</h3>';?>
          <?php if(isset($theme['msg'])) echo '<div class="wpmchimpa_para">'.$theme['msg'].'</div>';?>
    <form action="" method="post" >
              
<input type="hidden" name="action" value="wpmchimpa_add_email_ajax"/>
<input type="hidden" name="wpmcform" value="<?php echo $form['id'];?>"/>
<?php $set = array(
'icon' => true,
'bui' => (isset($theme['button_i']) && $theme['button_i'] != 'inone' && $theme['button_i'] != 'idef')?true:false,
'type' => 2,
'sig' => $plugin->getSpin(isset($theme["spinner_t"])?$theme["spinner_t"]:'8',$wpmcw_id,isset($theme["spinner_c"])?$theme["spinner_c"]:'#000')
);
$plugin->stfield($form['fields'],$set);
?>
  
                <div style="clear:both"></div>

              <?php if(isset($theme['tag_en'])){
              if(isset($theme['tag'])) $tagtxt= $theme['tag'];
              else $tagtxt=__( 'Secure and Spam free...', 'chimpmate-wpmca' );
              echo '<div class="wpmchimpa-tag">'.$tagtxt.'</div>';
              }?>

    </form>
    	<div class="wpmchimpa-feedback" wpmcerr="gen"></div>
	</div>	
</div>