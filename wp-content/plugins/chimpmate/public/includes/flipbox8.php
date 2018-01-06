<?php 
$theme['msg'] = htmlspecialchars_decode($theme['msg']);
$this->social=true;
?>
<style type="text/css">
#wpmchimpaf .wpmchimpaf{
  position:fixed;z-index: 99999;
  display: inline-block;
  width: 320px;
background: #262E43;
text-align: center;
box-shadow: 0 0 20px rgba(0,0,0,.2);
overflow: hidden;
border-radius: 3px;
  -webkit-backface-visibility: hidden;
-webkit-transition: -webkit-transform 0.3s cubic-bezier(0.785, 0.135, 0.15, 0.86);
transition: transform 0.3s cubic-bezier(0.785, 0.135, 0.15, 0.86);
  <?php 
    if(isset($theme["bg_c"])){
        echo 'background:'.$theme["bg_c"].';';
    }
  ?>
}
#wpmchimpaf.wpmctb_mid .wpmchimpaf{left: calc(50% - 160px);bottom: 0}
#wpmchimpaf.wpmctb_mid .wpmchimpaf.wpmchimpaf-close{
-webkit-transform: translateY(1000px);transform: translateY(1000px);
}
#wpmchimpaf.wpmctb_left .wpmchimpaf{left: 10px;bottom: 10px}
#wpmchimpaf.wpmctb_left .wpmchimpaf.wpmchimpaf-close{
-webkit-transform: translateX(-500px);transform: translateX(-500px);
}
#wpmchimpaf.wpmctb_right .wpmchimpaf{right: 10px;bottom: 10px}
#wpmchimpaf.wpmctb_right .wpmchimpaf.wpmchimpaf-close{
-webkit-transform: translateX(500px);transform: translateX(500px);
}
.wpmchimpaf h3{
line-height: 20px;
padding-top:20px;
color: #fff;
font-size: 20px;
<?php 
  if(isset($theme["heading_f"]['f'])){
    array_push($wpmc_font, $theme["heading_f"]['f']);
    echo 'font-family:'.$theme["heading_f"]['f'].';';
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
.wpmchimpaf .wpmchimpa_para{
    margin: 10px 10px 0;
  line-height: 14px;
}
.wpmchimpaf .wpmchimpa_para,.wpmchimpaf .wpmchimpa_para * {
font-size: 12px;
color: #ADBBE0;
<?php if(isset($theme["msg_f"]['f'])){
    array_push($wpmc_font, $theme["msg_f"]['f']);
echo 'font-family:'.$theme["msg_f"]['f'].';';
}?>
}

.wpmchimpaf form{
margin-top: 20px;
overflow: hidden;
}
.wpmchimpaf .formbox{
margin: 0 auto;
width: calc(100% - 20px);
}

.wpmchimpaf  .wpmchimpa-field{
position: relative;
width:100%;
margin: 0 auto 10px auto;
text-align: left;
}
.wpmchimpaf .inputicon{
display: none;
}
.wpmchimpaf .wpmc-ficon .inputicon {
display: block;
width: 35px;
height: 35px;
position: absolute;
top: 0;
left: 0;
pointer-events: none;

}
.wpmchimpaf .wpmchimpa-field.wpmc-ficon input[type="text"],
.wpmchimpaf .wpmchimpa-field.wpmc-ficon .inputlabel{
  padding-left: 35px;

}
<?php
$col = ((isset($theme["inico_c"]))? $theme["inico_c"] : '#888');
foreach ($form['fields'] as $f) {
  if($f['icon'] != 'idef' && $f['icon'] != 'inone')
    echo '.wpmchimpaf .wpmc-ficon [wpmcfield="'.$f['tag'].'"] ~ .inputicon {background: '.$this->getIcon($f['icon'],25,$col).' no-repeat center}';
}
?>
.wpmchimpaf .wpmchimpa-field textarea,
.wpmchimpaf .wpmchimpa-field select,
.wpmchimpaf input[type="text"]{
text-align: left;
width: 100%;
height: 35px;
background: #fff;
 padding: 0 10px;
-webkit-border-radius: 3px;
-moz-border-radius: 3px;
border-radius: 3px;
color: #353535;
font-size:17px;
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
  if(isset($theme["tbox_bor"]) && isset($theme["tbox_borc"])){
      echo ' border:'.$theme["tbox_bor"].'px solid '.$theme["tbox_borc"].';';
  }
?>
}

.wpmchimpaf .wpmchimpa-field.wpmchimpa-multidrop select{
  height: 100px;
}
.wpmchimpaf .wpmchimpa-field.wpmchimpa-drop:before{
content: '';
width: 35px;
height: 35px;
position: absolute;
right: 0;
top: 0;
pointer-events: none;
background: no-repeat center;
background-image: <?=$this->getIcon('dd',16,'#000');?>;

}
.wpmchimpaf input[type="text"] ~ .inputlabel{
position: absolute;
top: 0;
left: 0;
right: 0;
pointer-events: none;
width: 100%;
line-height: 35px;
color: rgba(0,0,0,0.6);
font-size: 17px;
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
.wpmchimpaf input[type="text"]:valid + .inputlabel{
display: none;
}
.wpmchimpaf select.wpmcerror,
.wpmchimpaf input[type="text"].wpmcerror{
  border-color: red;
}
.wpmchimpaf .wpmchimpa-field.wpmchimpa-check,
.wpmchimpaf .wpmchimpa-field.wpmchimpa-radio{
  text-align: left;
}
.wpmchimpaf .wpmchimpa-check *,
.wpmchimpaf .wpmchimpa-radio *{
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
.wpmchimpaf .wpmchimpa-item input {
  display: none;
}
.wpmchimpaf .wpmchimpa-item span {
  cursor: pointer;
  display: inline-block;
  position: relative;
  padding-left: 35px;
  margin-right: 10px;
  line-height: 26px;
}

.wpmchimpaf .wpmchimpa-item span:before,
.wpmchimpaf .wpmchimpa-item span:after {
  content: '';
  display: inline-block;
  width: 16px;
  height: 16px;
  left: 0;
  top: 5px;
  position: absolute;
}
.wpmchimpaf .wpmchimpa-item span:before {
background-color: #fff;
transition: all 0.3s ease-in-out;
<?php
  if(isset($theme["check_borc"])){
      echo 'border: 1px solid'.$theme["check_borc"].';';
  }
?>
}
.wpmchimpaf .wpmchimpa-item input:checked + span:before{
  <?php if(isset($theme["check_c"]))echo 'background: '.$theme["check_c"].';';?>
}
.wpmchimpaf .wpmchimpa-item input[type='checkbox'] + span:hover:after, .wpmchimpaf input[type='checkbox']:checked + span:after {
  content:'';
  background: no-repeat center;
  <?php if(isset($theme['check_shade']))$chs=$theme['check_shade'];else $chs='1';
  echo 'background-image: '.$this->chshade($chs).';';?>
  left: -1px;
}
.wpmchimpaf .wpmchimpa-item input[type='radio'] + span:before {
border-radius: 50%;
width: 16px;
height: 16px;
top: 4px;
}
.wpmchimpaf input[type='radio']:checked + span:after {
background: <?php echo ($chs == 1)?'#7C7C7C':'#fafafa';?>;
width: 12px;
height: 12px;
top: 6px;
left: 2px;
border-radius: 50%;
}
.wpmchimpaf .wpmcinfierr{
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

.wpmchimpaf .wpmchimpa-subs-button{
-webkit-border-radius: 3px;
-moz-border-radius: 3px;
border-radius: 3px;
width: 100%;
color: #fff;
font-size: 17px;
border: 1px solid #50B059;
background-color: #73C557;
height: 36px;
line-height: 36px;
text-align: center;
cursor: pointer;
transition: all 0.5s ease;
<?php
  if(isset($theme["button_f"]['f'])){
    array_push($wpmc_font, $theme["button_f"]['f']);
    echo 'font-family:'.$theme["button_f"]['f'].';';
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
  if(isset($theme["button_bc"])){
      echo 'background:'.$theme["button_bc"].';';
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
.wpmchimpaf .wpmchimpa-subs-button::before{
  content: '<?php if(isset($theme['button'])) echo $theme['button'];else echo __( 'Subscribe', 'chimpmate-wpmca' );?>';
  display: block;
}
.wpmchimpaf .wpmchimpa-subs-button:hover{
background-color: #50B059; 
    <?php 
        if(isset($theme["button_bch"])){
            echo 'background:'.$theme["button_bch"].';';
        }
        if(isset($theme["button_fch"])){
            echo 'color:'.$theme["button_fch"].';';
        }
      ?>
}
.wpmchimpaf .wpmchimpa-subsc{position: relative;}
.wpmchimpaf .wpmchimpa-subs-button.subsicon:before{
padding-left: 36px;

}
.wpmchimpaf .wpmchimpa-subs-button.subsicon::after{
content:'';
position: absolute;
height: 36px;
width: 36px;
top: 0;
left: 0;
pointer-events: none;
  <?php 

  if($theme["button_i"] != 'inone' && $theme["button_i"] != 'idef'){
    $col = ((isset($theme["button_fc"]))? $theme["button_fc"] : '#fff');
     echo 'background: '.$this->getIcon($theme["button_i"],25,$col).' no-repeat center;';
  }
  ?>
}
.wpmchimpaf .wpmchimpa-social{
display: inline-block;
margin: 12px auto 0;
height: 90px;
width: 100%;
background: rgba(75, 75, 75, 0.3);
box-shadow: 0px 1px 1px 1px rgba(116, 116, 116, 0.94);
}
.wpmchimpaf .wpmchimpa-social::before{
content: '<?php if(isset($theme['soc_head'])) echo $theme['soc_head'];else echo __( 'Subscribe with', 'chimpmate-wpmca' );?>';
font-size: 13px;
line-height: 13px;
color: #ADACB2;
width: 100%;
display: block;
margin: 15px auto 5px;
<?php
if(isset($theme["soc_f"]['f'])){
    array_push($wpmc_font, $theme["soc_f"]['f']);
  echo 'font-family:'.$theme["soc_f"]['f'].';';
}
if(isset($theme["soc_f"]['s'])){
    echo 'font-size:'.$theme["soc_f"]['s'].'px;';
    echo 'line-height:'.$theme["soc_f"]['s'].'px;';
}
if(isset($theme["soc_f"]['w'])){
    echo 'font-weight:'.$theme["soc_f"]['w'].';';
}
if(isset($theme["soc_f"]['st'])){
    echo 'font-style:'.$theme["soc_f"]['st'].';';
}
if(isset($theme["soc_fc"])){
    echo 'color:'.$theme["soc_fc"].';';
}
?>
}

.wpmchimpaf .wpmchimpa-social .wpmchimpa-soc{
display: inline-block;
width:40px;
height: 40px;
border-radius: 2px;
cursor: pointer;
-webkit-transition: all 0.1s ease;
transition: all 0.1s ease;
-webkit-backface-visibility:hidden;
border:1px solid #262E43;
<?php  if(isset($theme["bg_c"])){
    echo 'border-color: '.$theme["bg_c"].';';
}?>
}
.wpmchimpaf .wpmchimpa-social .wpmchimpa-soc::before{
content: '';
display: block;
width:40px;
height: 40px;
background: no-repeat center;
}

.wpmchimpaf .wpmchimpa-social .wpmchimpa-soc.wpmchimpa-fb {
<?php if(!isset($settings["fb_api"])){
echo 'display:none;';
}?>
}
.wpmchimpaf .wpmchimpa-social .wpmchimpa-soc.wpmchimpa-fb::before {
background-image:<?php echo $this->getIcon('fb1',15,'#fff');?>
}
.wpmchimpaf .wpmchimpa-social .wpmchimpa-soc.wpmchimpa-fb:hover:before {
background-image:<?php echo $this->getIcon('fb1',15,'#2d609b');?>
}
.wpmchimpaf .wpmchimpa-social .wpmchimpa-soc.wpmchimpa-gp {
<?php if(!isset($settings["gp_api"])){
echo 'display:none;';
}?>
}
.wpmchimpaf .wpmchimpa-social .wpmchimpa-soc.wpmchimpa-gp:before {
background-image: <?php echo $this->getIcon('gp1',15,'#fff');?>
}
.wpmchimpaf .wpmchimpa-social .wpmchimpa-soc.wpmchimpa-gp:hover:before {
background-image: <?php echo $this->getIcon('gp1',15,'#eb4026');?>
}
.wpmchimpaf .wpmchimpa-social .wpmchimpa-soc.wpmchimpa-ms {
<?php if(!isset($settings["ms_api"])){
echo 'display:none;';
}?>
}
.wpmchimpaf .wpmchimpa-social .wpmchimpa-soc.wpmchimpa-ms::before {
background-image: <?php echo $this->getIcon('ms1',15,'#fff');?>
}
.wpmchimpaf .wpmchimpa-social .wpmchimpa-soc.wpmchimpa-ms:hover:before {
background-image: <?php echo $this->getIcon('ms1',15,'#00BCF2');?>
}

.wpmchimpaf .wpmchimpa-feedback{
text-align: center;
position: relative;
color: #ccc;
font-size: 10px;
height: 12px;
top: -91px;
margin-top: -12px;
  <?php
    if(isset($theme["status_f"]['f'])){
    array_push($wpmc_font, $theme["status_f"]['f']);
      echo 'font-family:'.$theme["status_f"]['f'].';';
    }
    if(isset($theme["status_f"]['s'])){
        echo 'font-size:'.$theme["status_f"]['s'].'px;';
        echo 'height:'.$theme["status_f"]['s'].'px;';
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

.wpmchimpaf .wpmchimpa-feedback.wpmchimpa-done:before{
content:'';
background:<?=$this->getIcon('ch1',15,'#fff');?> no-repeat center;
width: 40px;
height: 40px;
border-radius: 20px;
line-height: 46px;
display: block;
background-color: #01E169;
margin: 30px auto;
}
.wpmchimpaf .wpmchimpa-feedback.wpmchimpa-done{
font-size: 15px;top: 0;  margin: 0;height: auto;
}

.wpmchimpaf .wpmchimpaf-close-button {
display: inline-block;
top: 0;
right: 0;
width: 25px;
position: absolute;
cursor:pointer;
}

.wpmchimpaf .wpmchimpaf-close-button::before {
    content: "\00D7";
font-size: 25px;
line-height: 25px;
font-weight: 100;
color: #999;
opacity: 0.4;
}
.wpmchimpaf .wpmchimpaf-close-button:hover:before {
opacity: 1;
}

.wpmchimpaf .wpmchimpa-signal {
display: none;
position: absolute;
top: 4px;
right: 3px;
-webkit-transform: scale(0.5);
-ms-transform: scale(0.5);
transform: scale(0.5);
}
#wpmchimpaf.signalshow .wpmchimpa-signal {
  display: inline-block;
}
#wpmchimpaf.wosoc .wpmchimpa-social{
display: none;
}

@media only screen and (max-width : 1024px) {
#wpmchimpaf .wpmchimpaf{
  display: none;
}
}
</style>
<div class="wpmchimpaf-tray chimpmatecss wpmchimpselector<?php if(isset($theme['addon_dissoc']))echo ' wosoc';?> wpmctb_right" id="wpmchimpaf">
<div class="wpmchimpa-reset wpmchimpaf wpmchimpaf-close">
    <?php echo isset($theme['heading'])?'<h3>'.$theme['heading'].'</h3>' : '<h3>Subscribe Now</h3>';?>
    <div class="wpmchimpaf-close-button"></div>
  
<?php if(isset($theme['msg'])) echo '<div class="wpmchimpa_para">'.$theme['msg'].'</div>';?>
  <form action="" method="post">
              <div class="formbox">
<input type="hidden" name="action" value="wpmchimpa_add_email_ajax"/>
<input type="hidden" name="wpmcform" value="<?php echo $form['id'];?>"/>
<?php $set = array(
  'icon' => false,
  'type' => 1
  );
$this->stfield($form['fields'],$set); 
?>
              <div class="wpmchimpa-subsc">
                <div class="wpmchimpa-subs-button<?php echo (isset($theme['button_i']) && $theme['button_i'] != 'inone' && $theme['button_i'] != 'idef')? ' subsicon' : '';?>"></div>
                <div class="wpmchimpa-signal"><?php 
    echo $this->getSpin(isset($theme["spinner_t"])?$theme["spinner_t"]:'3','wpmchimpaf',isset($theme["spinner_c"])?$theme["spinner_c"]:'#000');?></div>
              
              </div>
    
            </div>

            <div class="wpmchimpa-social">
                <div class="wpmchimpa-soc wpmchimpa-fb"></div>
                <div class="wpmchimpa-soc wpmchimpa-gp"></div>
                <div class="wpmchimpa-soc wpmchimpa-ms"></div>
            </div>
  </form>
  <div class="wpmchimpa-feedback" wpmcerr="gen"></div>
  </div>
</div>