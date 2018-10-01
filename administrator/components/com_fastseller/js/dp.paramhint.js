var parambtnHintTimeout;
		$$('.prodparam-btn').addEvents({
			mouseover:function(){
				var btn=this;
				parambtnHintTimeout=setTimeout(function(){showParamBtnHint(btn)}, parambtn_hint_delay);
			},
			mouseout:function(){
				clearTimeout(parambtnHintTimeout);
				$('parambtn-hint').addClass('hid');
			}
		
		});
		function showParamBtnHint(btn){
			var ptid=$(btn.getAttribute('data-row')+'-tr').getAttribute('data-ptid');
			var balloonTop, myEffects;
			if(ptid!='none'){
				var paramname=btn.getAttribute('data-paramname');
				var hintstr='<table cellpadding="0" cellspacing="0">';
				hintstr+='<tr><td class="hint-td1">Parameter name:</td><td class="hint-td2" style="color:#BF4545">'+paramname+'</td></tr>';
				hintstr+='<tr><td class="hint-td1">Parameter label:</td><td class="hint-td2" style="color:#3E55A9">'+paramInfo[ptid][paramname]["label"]+'</td></tr>';
				hintstr+='<tr><td class="hint-td1">Parameter type:</td><td class="hint-td2" style="color:#9D8F18">';
				switch(paramInfo[ptid][paramname]["type"]){
					case 'S': hintstr+='Short Text [S]';break;
					case 'V': hintstr+='Multiple Values [V]';break;
					case 'T': hintstr+='Text [T]';break;
					case 'I': hintstr+='Integer [I]';break;
					case 'F': hintstr+='Float [F]';break;
					case 'C': hintstr+='Char [C]';break;
					case 'D': hintstr+='Date & Time [D]';break;
					case 'A': hintstr+='Date [A]';break;
					case 'M': hintstr+='Time [M]';break;
					case 'B': hintstr+='Break Line [B]';break;
				}
				hintstr+='</td></tr>';
				hintstr+='<tr><td class="hint-td1">Product Values:</td><td class="hint-td2" style="color:#1A8E1D;max-width:300px;">';
				var s=btn.getAttribute('data-btnvalue');
				hintstr+= (s)?s:'-';
				hintstr+='</td></tr>';
				hintstr+='<tr><td class="hint-td1">Parameter unit:</td><td class="hint-td2" style="color:#1A8E1D">'+paramInfo[ptid][paramname]["unit"]+'</td></tr></table>';
				hintstr+='<div style="margin-top:0px;"><span style="color:#333333;">Filter Multi-Mode:</span> <span style="color:#9D2DB3">';
				hintstr+= (paramInfo[ptid][paramname]["multiselect"]=='Y')? 'Yes':'No';
				hintstr+='</span></div>';
				
				var hintBalloon=$('parambtn-hint');
				hintBalloon.getFirst().innerHTML=hintstr;
				hintBalloon.removeClass('hid');
				var btnpos=btn.getPosition();
		
				if(parambtn_hint_transition) {
					balloonTop=btnpos.y-hintBalloon.offsetHeight-20-10;
					hintBalloon.setStyles({left:btnpos.x-30,top:balloonTop,opacity:0});
					myEffects = new Fx.Styles('parambtn-hint', {duration: 250, transition: Fx.Transitions.Cubic.easeInOut});
					myEffects.start({
						'top': [balloonTop, balloonTop+10],
						'opacity':[0,1]
					});
				} else {
					balloonTop=btnpos.y-hintBalloon.offsetHeight-20;
					hintBalloon.setStyles({left:btnpos.x-30,top:balloonTop,opacity:1});
				}
			}
		}