        <div class="web p3">
        	<ul>
	        	<li>
		        	<strong>站内信标题：</strong>
		            <input class="text_input" type="text" name="<?php echo $notify['code']; ?>[]" value="<?php echo $template[$notify['code']][0];?>" datatype="*" />
		            
	           	</li>
	           	<li>
		        	<strong>站内信内容：</strong>
		            <textarea name="<?php echo $notify['code']; ?>[]"  datatype="*"><?php echo $template[$notify['code']][1];?></textarea>
		          
	            </li>
            </ul>
        </div>
