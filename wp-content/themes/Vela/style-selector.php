<?php
    $colors = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
    $template_directory = esc_url( get_template_directory_uri() );
?>
<div id="style-selector">
    <div class="selector-wrapper">
        <a href="#" class="selector-toggle"><i class="fa fa-cog"></i></a>
        <div class="option-wrapper">
            <h4>Style Selector</h4>
            <div class="option-section no-border">
                <h5>Color Scheme</h5>
                <ul id="color-list" class="option-list clear">
                    <?php 
                    $predifined_colors = wyde_predifined_colors();
                    for($i = 0; $i < count($colors); $i++){
                    ?>
                    <li><a href="#" data-item="<?php echo esc_attr( $colors[$i] );?>" data-color="<?php echo esc_attr( $predifined_colors[($i+1)] );?>"><img src="<?php echo $template_directory;?>/images/colors/<?php echo esc_attr( $colors[$i] );?>.png" alt="Color <?php echo esc_attr( $colors[$i] );?>"/></a></li>
                    <?php } ?>
                </ul>
            </div>
            <div class="option-section">
                <h5>Layout Style</h5>
                <select id="layout-mode" name="layout-mode">
                    <option value="wide">Wide</option>
                    <option value="boxed">Boxed</option>
                </select>
            </div>
            <div class="option-section">
                <h5>Background Style</h5>
                <select id="background-mode" name="background-mode">
                    <option value="pattern">Pattern</option>
                    <option value="image">Image</option>
                </select>
            </div>
            <div id="pattern-background" class="option-section">
                <h5>Background Patterns</h5>
                <ul id="bg-pattern-list" class="option-list clear">
                    <li><a href="#" data-pattern="pattern-1"><img src="<?php echo $template_directory;?>/images/patterns/1.png" alt="pattern 1" /></a></li>
                    <li><a href="#" data-pattern="pattern-2"><img src="<?php echo $template_directory;?>/images/patterns/2.png" alt="pattern 2" /></a></li>
                    <li><a href="#" data-pattern="pattern-3"><img src="<?php echo $template_directory;?>/images/patterns/3.png" alt="pattern 3" /></a></li>
                    <li><a href="#" data-pattern="pattern-4"><img src="<?php echo $template_directory;?>/images/patterns/4.png" alt="pattern 4" /></a></li>
                    <li><a href="#" data-pattern="pattern-5"><img src="<?php echo $template_directory;?>/images/patterns/5.png" alt="pattern 5" /></a></li>
                </ul>
                <h5>Background Position Fixed</h5>
                <div id="btn-pattern-fixed" class="btn-group" data-toggle="buttons">
                    <label class="btn btn-default">
                        <input type="radio" id="pattern-fixed-on" name="pattern-fixed"  value="on"  />On
                    </label>
                    <label class="btn btn-default active">
                        <input type="radio" id="pattern-fixed-off" name="pattern-fixed"  value="off"  />Off
                    </label>
                </div>
            </div>
            <div id="image-background" class="option-section">
                <h5>Background Images</h5>
                <ul id="bg-image-list" class="option-list clear">
                    <li><a href="#" data-image="<?php echo $template_directory;?>/images/background/1.jpg"><img src="<?php echo $template_directory;?>/images/background/1-thumb.jpg" alt="background 1" /></a></li>
                    <?php 
                    /* Edit and replace with your background images
                    <li><a href="#" data-image="<?php echo $template_directory;?>/images/background/2.jpg"><img src="<?php echo $template_directory;?>/images/background/2-thumb.jpg" alt="background 2" /></a></li>
                    <li><a href="#" data-image="<?php echo $template_directory;?>/images/background/3.jpg"><img src="<?php echo $template_directory;?>/images/background/3-thumb.jpg" alt="background 3" /></a></li>
                    <li><a href="#" data-image="<?php echo $template_directory;?>/images/background/4.jpg"><img src="<?php echo $template_directory;?>/images/background/4-thumb.jpg" alt="background 4" /></a></li>
                    <li><a href="#" data-image="<?php echo $template_directory;?>/images/background/5.jpg"><img src="<?php echo $template_directory;?>/images/background/5-thumb.jpg" alt="background 5" /></a></li>
                    */
                    ?>
                </ul>
                <h5>Background Pattern Overlay</h5>
                <div id="btn-pattern-overlay" class="btn-group" data-toggle="buttons">
                    <label class="btn btn-default">
                        <input type="radio" id="pattern-overlay-on" name="pattern-overlay"  value="on"  />On
                    </label>
                    <label class="btn btn-default active">
                        <input type="radio" id="pattern-overlay-off" name="pattern-overlay" value="off" />Off
                    </label>
                </div>
            </div>
            <div id="reset-style" class="option-section">
            <a href="#" class="reset-style button">Reset Styles</a>
            </div>
        </div>
    </div>
</div>